<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\GroomerSpacerProfile;
use App\Support\InvoiceNumber;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use DOMElement;
use Dompdf\Frame;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\HeaderUtils;

class BookingInvoicePdfController extends Controller
{
    /** PDF width (pt); height is computed from `.page` content. */
    private const INVOICE_PAGE_WIDTH_PT = 595;

    /** Fallback height if `.page` frame cannot be read (matches former A4 default). */
    private const INVOICE_PAGE_FALLBACK_HEIGHT_PT = 842;

    /** Tall canvas for measurement pass so content does not paginate. */
    private const INVOICE_MEASURE_MAX_HEIGHT_PT = 100000;

    /** Absolute floor (pt) for dynamic height to avoid invalid tiny pages. */
    private const INVOICE_PAGE_ABS_MIN_HEIGHT_PT = 120;

    /** Small slack after tight layout read (Dompdf rounding). */
    private const INVOICE_PAGE_MEASURE_BUFFER_PT = 10;

    public function __invoke(Booking $booking)
    {
        $data = $this->buildInvoiceViewData($booking);

        $pageHeightPt = $this->measureInvoicePageHeightPt($data);

        $pdf = Pdf::loadView('pdf.booking-invoice', $data)->setPaper([0, 0, self::INVOICE_PAGE_WIDTH_PT, $pageHeightPt], 'portrait');

        $this->registerDompdfInvoiceFonts($pdf);

        $filename = 'Fursgo-Invoice-'.$data['invoiceNo'].'.pdf';
        $output = $pdf->output();
        $len = (string) strlen($output);

        $dispositionType = request()->boolean('inline') ? 'inline' : 'attachment';

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition($dispositionType, $filename, $filename),
            'Content-Length' => $len,
            'Cache-Control' => 'private, must-revalidate, max-age=0',
        ]);
    }

    /**
     * Render the invoice Blade as HTML so you can use browser DevTools (Elements, computed layout).
     * Only available when APP_ENV=local. Dompdf may still differ slightly (e.g. fonts, fixed positioning).
     */
    public function previewHtml(Booking $booking)
    {
        abort_unless(app()->isLocal(), 404);

        return response()
            ->view('pdf.booking-invoice', $this->buildInvoiceViewData($booking))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * Dompdf does not support shrink-wrapped pages natively: render once on a tall canvas,
     * read laid-out height, then use that for the final page size.
     *
     * Using `.page` margin height is unreliable: its resolved CSS height often stretches to the
     * full measurement canvas. `.page-content` matches in-flow invoice/footer height; add `.page`
     * vertical padding from computed styles.
     */
    private function measureInvoicePageHeightPt(array $viewData): float
    {
        $pdf = Pdf::loadView('pdf.booking-invoice', $viewData)
            ->setPaper([0, 0, self::INVOICE_PAGE_WIDTH_PT, self::INVOICE_MEASURE_MAX_HEIGHT_PT], 'portrait');
        $this->registerDompdfInvoiceFonts($pdf);
        $pdf->render();

        $pageFrame = $this->findFrameWithClass($pdf, 'page');
        $contentFrame = $this->findFrameWithClass($pdf, 'page-content');

        if ($pageFrame !== null && $contentFrame !== null) {
            $contentH = (float) $contentFrame->get_margin_height();
            $style = $pageFrame->get_style();
            $cbw = (float) $pageFrame->get_containing_block('w');
            $paddingVertical = (float) $style->length_in_pt(
                [$style->padding_top, $style->padding_bottom],
                $cbw
            );
            $h = ceil($contentH + $paddingVertical + self::INVOICE_PAGE_MEASURE_BUFFER_PT);

            return min(max((float) $h, self::INVOICE_PAGE_ABS_MIN_HEIGHT_PT), (float) self::INVOICE_MEASURE_MAX_HEIGHT_PT);
        }

        if ($pageFrame !== null) {
            $h = ceil((float) $pageFrame->get_margin_height() + self::INVOICE_PAGE_MEASURE_BUFFER_PT);

            return min(max((float) $h, self::INVOICE_PAGE_ABS_MIN_HEIGHT_PT), (float) self::INVOICE_MEASURE_MAX_HEIGHT_PT);
        }

        return (float) self::INVOICE_PAGE_FALLBACK_HEIGHT_PT;
    }

    private function findFrameWithClass(DomPdfWrapper $pdf, string $className): ?Frame
    {
        $root = $pdf->getDomPDF()->getTree()->get_root();

        return $this->findFrameByClassRecursive($root, $className);
    }

    private function findFrameByClassRecursive(Frame $frame, string $className): ?Frame
    {
        $node = $frame->get_node();
        if ($node instanceof DOMElement && $node->hasAttribute('class')) {
            $classes = preg_split('/\s+/', trim($node->getAttribute('class')));
            if (in_array($className, $classes, true)) {
                return $frame;
            }
        }
        for ($c = $frame->get_first_child(); $c; $c = $c->get_next_sibling()) {
            $found = $this->findFrameByClassRecursive($c, $className);
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Shared variables for pdf.booking-invoice (PDF and HTML preview).
     *
     * @return array<string, mixed>
     */
    private function buildInvoiceViewData(Booking $booking): array
    {
        abort_unless(Auth::guard('groomer_spacer')->check(), 403);

        $profileId = (int) Auth::guard('groomer_spacer')->id();
        abort_unless((int) $booking->goormer_spacer_id === $profileId, 403);

        if (! in_array((string) $booking->booking_status, ['completed', 'cancelled'], true)) {
            abort(404);
        }

        /** @var GroomerSpacerProfile $issuer */
        $issuer = Auth::guard('groomer_spacer')->user();
        $issuerDetails = $this->issuerDisplay($issuer);

        $booking->loadMissing(['petOwner:id,name,email,address', 'pets:id,name,pet_type,address']);

        $logoCandidates = [
            public_path('images/logo/fursgo-invoice-wordmark.svg'),
            public_path('images/logo/logo.svg'),
        ];
        $logoDataUri = '';
        foreach ($logoCandidates as $logoPath) {
            if (is_readable($logoPath)) {
                $logoDataUri = 'data:image/svg+xml;base64,'.base64_encode((string) file_get_contents($logoPath));

                break;
            }
        }

        $footerWatermarkIconPath = public_path('images/pdf/fg-icon.svg');
        $footerWatermarkIconDataUri = '';
        if (is_readable($footerWatermarkIconPath)) {
            $footerWatermarkIconDataUri = 'data:image/svg+xml;base64,'.base64_encode((string) file_get_contents($footerWatermarkIconPath));
        }

        $addonRows = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(function ($item) {
                return [
                    'label' => trim((string) data_get($item, 'label', '')),
                    'amount' => (float) data_get($item, 'amount', 0),
                ];
            })
            ->filter(fn (array $item) => $item['label'] !== '')
            ->values();

        $petNames = $booking->pets->pluck('name')->filter()->implode(', ');
        if ($petNames === '') {
            $petNames = strtolower((string) ($issuer->user_type ?? '')) === 'space' ? '—' : 'N/A';
        }

        $serviceAmount = (float) $booking->amount;
        $extrasAmount = (float) $addonRows->sum('amount');
        $discount = (float) ($booking->discount ?? 0);
        $subtotal = max(0, $serviceAmount + $extrasAmount - $discount);

        $platformPercent = (float) config('services.fursgo.platform_fee_percent', 5);
        $platformPercent = max(0, min(100, $platformPercent));
        $platformFee = round($subtotal * ($platformPercent / 100), 2);
        $totalDue = round($subtotal + $platformFee, 2);

        $invoiceNo = InvoiceNumber::customerBooking((int) $booking->id, $booking->date);

        $lines = [];
        $lines[] = [
            'description' => $booking->service ?: 'Service',
            'subtitle' => $this->visitTypeLabel($booking->visit_type).' · '.optional($booking->date)->format('d M Y'),
            'pet_name' => $petNames,
            'amount' => $serviceAmount,
        ];
        foreach ($addonRows as $addon) {
            $lines[] = [
                'description' => $addon['label'],
                'subtitle' => 'Add-on',
                'pet_name' => $petNames,
                'amount' => (float) $addon['amount'],
            ];
        }
        if ($discount > 0) {
            $lines[] = [
                'description' => 'Promo discount',
                'subtitle' => '',
                'pet_name' => '—',
                'amount' => -$discount,
            ];
        }

        return [
            'logoDataUri' => $logoDataUri,
            'invoiceNo' => $invoiceNo,
            'invoiceDate' => optional($booking->date)->format('d F Y') ?? now()->format('d F Y'),
            'billToName' => $booking->petOwner->name ?? 'Customer',
            'billToAddress' => $this->billToAddressLine($booking),
            'billToEmail' => $booking->petOwner->email ?? '',
            'issuerName' => $issuerDetails['name'],
            'issuerPhone' => $issuerDetails['phone'],
            'issuerEmail' => $issuerDetails['email'],
            'verifiedBadgeDataUri' => $this->verifiedBadgeDataUri(),
            'lines' => $lines,
            'subtotal' => $subtotal,
            'platformPercent' => $platformPercent,
            'platformFee' => $platformFee,
            'totalDue' => $totalDue,
            'bookingStatus' => (string) $booking->booking_status,
            'invoiceCornerTopRightDataUri' => $this->invoiceCornerPngDataUri(public_path('images/pdf/right-top.png')),
            'invoiceCornerBottomLeftDataUri' => $this->invoiceCornerPngDataUri(public_path('images/pdf/left-bot.png')),
            'footerWatermarkIconDataUri' => $footerWatermarkIconDataUri,
            'footerBrandSocialIcons' => $this->footerBrandSocialIcons(),
            'invoiceEmbeddedFontFacesCss' => $this->embeddedFontFacesCss([
                [$this->fontDataUri(public_path('fonts/Lato/Lato-Regular.ttf')), 'Lato', 'normal', '400'],
                [$this->fontDataUri(public_path('fonts/Lato/Lato-Bold.ttf')), 'Lato', 'normal', '700'],
                [$this->fontDataUri(public_path('fonts/Lato/Lato-Italic.ttf')), 'Lato', 'italic', '400'],
                [$this->fontDataUri(public_path('fonts/Lato/Lato-BoldItalic.ttf')), 'Lato', 'italic', '700'],
                [$this->fontDataUri(public_path('fonts/Playfair_Display/static/PlayfairDisplay-Italic.ttf')), 'Playfair Display', 'italic', '400'],
            ]),
        ];
    }

    /**
     * Build @font-face rules for embedded data URIs. Done in PHP so the Blade file avoids @if next to CSS @font-face/@page (breaks editor highlighting).
     *
     * @param  array<int, array{0: string, 1: string, 2: string, 3: string}>  $faces  [dataUri, family, font-style, font-weight]
     */
    private function embeddedFontFacesCss(array $faces): string
    {
        $blocks = [];

        foreach ($faces as [$uri, $family, $fontStyle, $fontWeight]) {
            if ($uri === '') {
                continue;
            }

            $url = str_replace(['\\', '"'], ['/', '\\"'], $uri);
            $familyEsc = str_replace('"', '\\"', $family);

            $blocks[] = <<<CSS
@font-face {
    font-family: "{$familyEsc}";
    src: url("{$url}") format("truetype");
    font-style: {$fontStyle};
    font-weight: {$fontWeight};
}
CSS;
        }

        return implode("\n\n", $blocks);
    }

    /**
     * @return array{name: string, phone: string, email: string}
     */
    private function issuerDisplay(GroomerSpacerProfile $profile): array
    {
        $accountType = (string) ($profile->account_type ?? '');
        $name = trim((string) ($profile->full_name ?? '')) ?: 'Business';
        $email = trim((string) ($profile->email ?? ''));
        $phone = '';

        if ($accountType === 'registered_business') {
            $bd = $profile->business_details ?? [];
            if (! is_array($bd)) {
                $bd = is_string($bd) ? (json_decode($bd, true) ?: []) : [];
            }
            $name = trim((string) ($bd['business_name'] ?? '')) ?: $name;
            $phone = trim((string) ($bd['business_phone'] ?? ''));
            $email = trim((string) ($bd['business_email'] ?? '')) ?: $email;
        } elseif ($accountType === 'freelance') {
            $fd = $profile->freelance_details ?? [];
            if (! is_array($fd)) {
                $fd = is_string($fd) ? (json_decode($fd, true) ?: []) : [];
            }
            $phone = trim((string) ($fd['contact_phone'] ?? ''));
            $email = trim((string) ($fd['contact_email'] ?? '')) ?: $email;
        } else {
            $bd = $profile->business_details ?? [];
            if (! is_array($bd)) {
                $bd = is_string($bd) ? (json_decode($bd, true) ?: []) : [];
            }
            $phone = trim((string) ($bd['business_phone'] ?? ''));
        }

        return [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ];
    }

    /**
     * Pet owner profile address, or first non-empty address from pets on the booking.
     */
    private function billToAddressLine(Booking $booking): string
    {
        $fromUser = trim((string) ($booking->petOwner->address ?? ''));
        if ($fromUser !== '') {
            return $fromUser;
        }

        foreach ($booking->pets as $pet) {
            $fromPet = trim((string) ($pet->address ?? ''));
            if ($fromPet !== '') {
                return $fromPet;
            }
        }

        return '';
    }

    private function visitTypeLabel(?string $visitType): string
    {
        $raw = str_replace('_', ' ', strtolower((string) $visitType));

        return match ($raw) {
            'home', 'home visit' => 'Home visit',
            'salon', 'salon visit' => 'Salon visit',
            default => $raw !== '' ? ucfirst($raw) : 'Visit',
        };
    }

    /**
     * Dompdf only ships DejaVu; register local invoice TTFs so CSS font-family resolves.
     */
    private function registerDompdfInvoiceFonts(DomPdfWrapper $pdf): void
    {
        $options = $pdf->getDomPDF()->getOptions();
        $fontDir = (string) $options->getFontDir();
        if ($fontDir !== '' && ! is_dir($fontDir)) {
            File::makeDirectory($fontDir, 0755, true);
        }
        $fontCache = (string) $options->getFontCache();
        if ($fontCache !== '' && $fontCache !== $fontDir && ! is_dir($fontCache)) {
            File::makeDirectory($fontCache, 0755, true);
        }

        $pairs = [
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'normal', 'weight' => 'normal'], 'Lato-Regular.ttf'],
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'normal', 'weight' => 'bold'], 'Lato-Bold.ttf'],
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'italic', 'weight' => 'normal'], 'Lato-Italic.ttf'],
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'italic', 'weight' => 'bold'], 'Lato-BoldItalic.ttf'],
            [public_path('fonts/Playfair_Display/static'), ['family' => 'Playfair Display', 'style' => 'italic', 'weight' => 'normal'], 'PlayfairDisplay-Italic.ttf'],
        ];

        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();

        foreach ($pairs as [$dir, $style, $filename]) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$filename);
            if ($path !== false && is_readable($path)) {
                $fontMetrics->registerFont($style, $path);
            }
        }
    }

    /**
     * Footer social SVGs as data URIs for Dompdf (inline SVG in HTML is not rendered reliably).
     *
     * @return array<int, array{uri: string, w: int, h: int}>
     */
    private function footerBrandSocialIcons(): array
    {
        $defs = [
            ['images/pdf/footer-brand-social-1.svg', 14, 14],
            ['images/pdf/footer-brand-social-2.svg', 12, 13],
            ['images/pdf/footer-brand-social-3.svg', 9, 14],
        ];
        $out = [];
        foreach ($defs as [$rel, $w, $h]) {
            $path = public_path($rel);
            $uri = is_readable($path)
                ? 'data:image/svg+xml;base64,'.base64_encode((string) file_get_contents($path))
                : '';
            $out[] = ['uri' => $uri, 'w' => $w, 'h' => $h];
        }

        return $out;
    }

    /**
     * Embed a corner PNG for Dompdf (more reliable than remote or relative file paths).
     *
     * @return string data:image/png;base64,... or a 1×1 transparent GIF if missing
     */
    private function invoiceCornerPngDataUri(string $absolutePath): string
    {
        if (! is_readable($absolutePath)) {
            return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        }

        return 'data:image/png;base64,'.base64_encode((string) file_get_contents($absolutePath));
    }

    private function fontDataUri(string $absolutePath): string
    {
        if (! is_readable($absolutePath)) {
            return '';
        }

        return 'data:font/truetype;base64,'.base64_encode((string) file_get_contents($absolutePath));
    }

    private function verifiedBadgeDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none"><path d="M3 0C1.35 0 0 1.35 0 3C0 4.65 1.35 6 3 6C4.65 6 6 4.65 6 3C6 1.35 4.65 0 3 0ZM2.4 4.5L0.9 3L1.323 2.577L2.4 3.651L4.677 1.374L5.1 1.8L2.4 4.5Z" fill="#D8E8B7"/></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
