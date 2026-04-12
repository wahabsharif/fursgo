<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class GroomerSpacerPrivateFileController extends Controller
{
    /**
     * Serve a business-owner ID file from private authorization (avoids direct /storage 403 on some setups).
     */
    public function businessOwnerIdImage(Request $request)
    {
        $token = $request->query('t');
        if (! is_string($token) || $token === '') {
            abort(404);
        }

        try {
            $path = Crypt::decryptString($token);
        } catch (\Throwable $e) {
            abort(404);
        }

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/')) {
            abort(403);
        }

        $user = Auth::guard('groomer_spacer')->user();
        if (! $user) {
            abort(403);
        }

        $businessDetails = $user->business_details ?? [];
        if (! is_array($businessDetails)) {
            $businessDetails = is_string($businessDetails) ? (json_decode($businessDetails, true) ?: []) : [];
        }

        $freelanceDetails = $user->freelance_details ?? [];
        if (! is_array($freelanceDetails)) {
            $freelanceDetails = is_string($freelanceDetails) ? (json_decode($freelanceDetails, true) ?: []) : [];
        }

        $allowed = $businessDetails['business_owner_id_images'] ?? [];
        if (! is_array($allowed)) {
            $allowed = [];
        }

        $fromFreelance = $freelanceDetails['id_verification_images'] ?? [];
        if (is_array($fromFreelance) && $fromFreelance !== []) {
            $allowed = array_merge($allowed, $fromFreelance);
        }

        $idPathsRaw = $user->id_document_paths ?? null;
        $idPaths = [];
        if (is_array($idPathsRaw)) {
            $idPaths = $idPathsRaw;
        } elseif (is_string($idPathsRaw) && $idPathsRaw !== '') {
            $idPaths = json_decode($idPathsRaw, true) ?: [];
        }

        $allowedList = array_values(array_filter(array_merge($allowed, $idPaths), fn ($p) => is_string($p) && $p !== ''));

        if (! in_array($path, $allowedList, true)) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }

    /**
     * Serve business basics profile / gallery files for the logged-in groomer spacer (bypasses direct /storage 403).
     */
    public function businessBasicsFile(Request $request)
    {
        $token = $request->query('t');
        if (! is_string($token) || $token === '') {
            abort(404);
        }

        try {
            $path = Crypt::decryptString($token);
        } catch (\Throwable $e) {
            abort(404);
        }

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/')) {
            abort(403);
        }

        $path = $this->normalizePublicDiskRelativePath($path);

        $user = Auth::guard('groomer_spacer')->user();
        if (! $user) {
            abort(403);
        }

        $bb = $user->business_basics ?? [];
        if (! is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }

        $allowed = [];
        $photo = $this->normalizePublicDiskRelativePath((string) ($bb['profile_photo_path'] ?? ''));
        if ($photo !== '') {
            $allowed[] = $photo;
        }

        $gallery = $bb['gallery_paths'] ?? [];
        if (is_string($gallery) && $gallery !== '') {
            $gallery = json_decode($gallery, true) ?: [];
        }
        if (is_array($gallery)) {
            foreach ($gallery as $p) {
                if (! is_string($p) || $p === '') {
                    continue;
                }
                $n = $this->normalizePublicDiskRelativePath($p);
                if ($n !== '') {
                    $allowed[] = $n;
                }
            }
        }

        if (! in_array($path, $allowed, true)) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }

    private function normalizePublicDiskRelativePath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));
        $path = ltrim($path, '/');
        foreach (['public/', 'storage/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        return $path;
    }
}
