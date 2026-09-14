<?php

namespace Database\Seeders;

use App\Models\FaqArticle;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->catalog() as $audience => $categories) {
            foreach ($categories as $sortOrder => $categoryData) {
                $articles = $categoryData['articles'];
                unset($categoryData['articles']);

                $category = FaqCategory::updateOrCreate(
                    [
                        'audience' => $audience,
                        'slug' => $categoryData['slug'],
                    ],
                    [
                        'name' => $categoryData['name'],
                        'chip_label' => $categoryData['chip_label'],
                        'subtitle' => $categoryData['subtitle'],
                        'icon' => $categoryData['icon'],
                        'sort_order' => $sortOrder + 1,
                    ]
                );

                $category->articles()->delete();

                foreach ($articles as $articleOrder => $article) {
                    FaqArticle::create([
                        'faq_category_id' => $category->id,
                        'question' => $article['question'],
                        'answer' => $article['answer'],
                        'excerpt' => $article['excerpt'],
                        'is_published' => true,
                        'sort_order' => $articleOrder + 1,
                    ]);
                }
            }
        }
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    private function catalog(): array
    {
        return [
            'pet_owner' => [
                [
                    'slug' => 'bookings',
                    'name' => 'Bookings',
                    'chip_label' => 'Bookings',
                    'subtitle' => 'View booking-related questions.',
                    'icon' => 'bookings',
                    'articles' => [
                        [
                            'question' => 'How do I book a grooming appointment?',
                            'excerpt' => 'Choose your location, pick a service and time, then confirm your booking in a few steps.',
                            'answer' => "Booking through FursGo is straightforward. Choose your location, select the grooming service you need, pick a time that works, and confirm. You'll see the price clearly before you pay.",
                        ],
                        [
                            'question' => 'Can I choose my groomer?',
                            'excerpt' => 'Yes — browse groomer profiles, reviews, and availability before you book.',
                            'answer' => 'Yes. You can browse groomer profiles, read reviews, and choose the professional who is the best fit for your dog before you confirm a booking.',
                        ],
                        [
                            'question' => 'What grooming services are available?',
                            'excerpt' => 'Services vary by groomer and can include baths, haircuts, nail care, and more.',
                            'answer' => 'Available services depend on the groomer. Typical options include baths, haircuts, nail trims, and add-ons. Each listing shows exactly what is offered before you book.',
                        ],
                        [
                            'question' => 'Do you offer in-home dog grooming?',
                            'excerpt' => 'Some groomers offer home visits. Filter by visit type when you search.',
                            'answer' => 'Some groomers offer in-home visits as well as studio appointments. Use visit type filters when searching so you only see groomers who can come to you.',
                        ],
                        [
                            'question' => 'Can I cancel or reschedule my booking?',
                            'excerpt' => 'Yes, from your bookings. Fees depend on notice and the groomer’s cancellation policy.',
                            'answer' => 'You can cancel or reschedule from your bookings. Deadlines and any fees follow the groomer’s cancellation policy, which is shown before you confirm.',
                        ],
                        [
                            'question' => 'What happens after I confirm a booking?',
                            'excerpt' => 'You’ll get a confirmation, reminders, and next steps for the appointment.',
                            'answer' => 'Once confirmed, you will receive a confirmation and reminders. Your booking details stay in your account so you can review time, location, and any notes before the appointment.',
                        ],
                        [
                            'question' => 'Why is my booking still pending?',
                            'excerpt' => 'Pending means the groomer has not accepted yet. You will be notified when they respond.',
                            'answer' => 'A pending booking is waiting for the groomer to accept. You will get a notification when they accept or decline. If it stays pending, you can message support with your booking ID.',
                        ],
                        [
                            'question' => 'What happens if the groomer cancels?',
                            'excerpt' => 'You will be notified and refunded according to the booking policy.',
                            'answer' => 'If a groomer cancels, you will be notified straight away. Refunds follow the booking policy shown at checkout, and you can rebook with another groomer.',
                        ],
                        [
                            'question' => 'What if there’s an issue with a booking?',
                            'excerpt' => 'Contact FursGo support with your booking ID and we will help resolve it.',
                            'answer' => 'If something goes wrong, submit a support request with your booking ID and a short description. Our team will follow up by email, usually within 24 hours.',
                        ],
                    ],
                ],
                [
                    'slug' => 'payments',
                    'name' => 'Payments',
                    'chip_label' => 'Payments',
                    'subtitle' => 'View payment-related questions.',
                    'icon' => 'payments',
                    'articles' => [
                        [
                            'question' => 'How is pricing determined?',
                            'excerpt' => 'Each groomer sets their own prices based on your dog and the service.',
                            'answer' => 'Pricing is set individually by each groomer and reflects factors such as your dog’s size, coat condition, and the type of grooming service selected. All prices are shown clearly before you confirm your booking.',
                        ],
                        [
                            'question' => 'When do I pay?',
                            'excerpt' => 'Payment is taken when you book to secure the appointment.',
                            'answer' => 'Payment is taken at the time of booking to secure your appointment and confirm your chosen time slot. This helps ensure a smooth experience for both you and the groomer.',
                        ],
                        [
                            'question' => 'Will I receive a refund if I cancel?',
                            'excerpt' => 'Refunds depend on notice given and the groomer’s cancellation policy.',
                            'answer' => 'Refunds depend on how much notice is given and the individual groomer’s cancellation policy. Full details are always available before you complete your booking.',
                        ],
                        [
                            'question' => 'How are Space Owners paid?',
                            'excerpt' => 'Space rental fees are paid automatically after completed bookings.',
                            'answer' => 'Space rental fees are paid automatically through the platform once bookings are completed, providing a simple and predictable income stream.',
                        ],
                        [
                            'question' => 'How do groomers get paid?',
                            'excerpt' => 'Groomers are paid automatically after each completed service.',
                            'answer' => 'Payments to groomers are handled automatically after each service is completed, with the platform fee deducted, making payouts simple and reliable.',
                        ],
                    ],
                ],
                [
                    'slug' => 'account',
                    'name' => 'Account',
                    'chip_label' => 'Account',
                    'subtitle' => 'View account-related questions.',
                    'icon' => 'account',
                    'articles' => [
                        [
                            'question' => 'What is FursGo?',
                            'excerpt' => 'FursGo is a dog grooming booking platform for owners, groomers, and spaces.',
                            'answer' => 'FursGo is a dog grooming booking platform designed to make finding and booking quality grooming simple and stress-free. We bring dog owners together with trusted professional groomers and licensed grooming spaces, all through one easy-to-use platform.',
                        ],
                        [
                            'question' => 'Does FursGo provide grooming services?',
                            'excerpt' => 'No — independent professional groomers provide the grooming.',
                            'answer' => 'Grooming services on FursGo are carried out by independent professional groomers. While FursGo manages the booking and payment experience, the grooming itself is always provided by experienced professionals.',
                        ],
                        [
                            'question' => 'What makes FursGo different?',
                            'excerpt' => 'FursGo is built around care, trust, and an easy booking experience.',
                            'answer' => 'FursGo is built around care, trust, and ease of use. Dog owners can book with confidence, groomers have the freedom to work flexibly, and space owners can earn from their facilities without added complexity.',
                        ],
                        [
                            'question' => 'Who is a Space Owner on FursGo?',
                            'excerpt' => 'A Space Owner is a licensed salon or studio listing facilities to groomers.',
                            'answer' => 'A Space Owner is a licensed grooming salon or studio that offers its facilities to independent groomers on a time-slot basis through FursGo.',
                        ],
                        [
                            'question' => 'Are groomers employed by FursGo?',
                            'excerpt' => 'No. Groomers operate independently on the platform.',
                            'answer' => 'Groomers listed on FursGo operate independently. The platform provides booking and payment support, while groomers remain in control of their own services.',
                        ],
                        [
                            'question' => 'How do I contact FursGo?',
                            'excerpt' => 'Use the support section to chat or submit a request.',
                            'answer' => 'Our support team can be reached anytime through the support section on the website or app, and we’ll be happy to assist.',
                        ],
                        [
                            'question' => 'Do I need an account to use FursGo?',
                            'excerpt' => 'An account lets you manage bookings, messages, and support in one place.',
                            'answer' => 'Creating an account allows you to manage bookings, communicate with groomers, and access support easily in one place.',
                        ],
                    ],
                ],
                [
                    'slug' => 'pets',
                    'name' => 'Pets',
                    'chip_label' => 'Pets',
                    'subtitle' => 'View pets-related questions.',
                    'icon' => 'pets',
                    'articles' => [
                        [
                            'question' => 'Is my dog insured during grooming?',
                            'excerpt' => 'Groomers should hold their own insurance. FursGo does not insure pets.',
                            'answer' => 'Groomers are expected to hold their own professional insurance. FursGo supports the booking process but does not directly insure pets.',
                        ],
                        [
                            'question' => 'Do groomers need their own insurance?',
                            'excerpt' => 'Yes. Professional groomers are responsible for their own insurance.',
                            'answer' => 'Professional groomers on FursGo are responsible for maintaining their own appropriate insurance to cover the services they provide.',
                        ],
                        [
                            'question' => 'Are Space Owners responsible for grooming outcomes?',
                            'excerpt' => 'No. Responsibility for grooming stays with the groomer.',
                            'answer' => 'Responsibility for grooming services remains with the groomer, including service quality and client care.',
                        ],
                    ],
                ],
                [
                    'slug' => 'safety',
                    'name' => 'Safety',
                    'chip_label' => 'Policies',
                    'subtitle' => 'View safety-related questions.',
                    'icon' => 'safety',
                    'articles' => [
                        [
                            'question' => 'Are groomers vetted before joining?',
                            'excerpt' => 'Yes. Groomers must meet FursGo onboarding standards before they are listed.',
                            'answer' => 'All groomers must meet FursGo’s onboarding standards before being listed, helping maintain quality and trust across the platform.',
                        ],
                        [
                            'question' => 'Do Space Owners provide grooming services?',
                            'excerpt' => 'No. Space Owners provide the venue; groomers provide the service.',
                            'answer' => 'Space Owners provide the physical space only. Grooming services are always delivered by independent professional groomers.',
                        ],
                    ],
                ],
            ],
            'business' => [
                [
                    'slug' => 'bookings',
                    'name' => 'Bookings',
                    'chip_label' => 'Bookings',
                    'subtitle' => 'Manage appointments, changes, and cancellations',
                    'icon' => 'bookings',
                    'articles' => [
                        [
                            'question' => 'How do I accept a booking request?',
                            'excerpt' => 'Open the request from your dashboard and choose Accept or Decline.',
                            'answer' => "When a new booking request arrives, you will receive a notification.\nOpen the request from your dashboard and choose Accept or Decline.",
                        ],
                    ],
                ],
                [
                    'slug' => 'payments',
                    'name' => 'Payments & Payouts',
                    'chip_label' => 'Pay-outs',
                    'subtitle' => 'Invoices, payouts, and payment issues',
                    'icon' => 'payments',
                    'articles' => [
                        [
                            'question' => 'When do I receive payouts?',
                            'excerpt' => 'Payouts are processed automatically after a booking is completed.',
                            'answer' => "Payouts are processed automatically after a booking is completed.\nFunds are usually transferred to your connected bank account within 3–5 business days.",
                        ],
                    ],
                ],
                [
                    'slug' => 'account',
                    'name' => 'Account & Profile',
                    'chip_label' => 'Profile',
                    'subtitle' => 'Managing your business account and profile',
                    'icon' => 'account',
                    'articles' => [
                        [
                            'question' => 'How do I update my business profile?',
                            'excerpt' => 'Go to Business Hub → Profile Settings to update description, services, pricing, and photos.',
                            'answer' => "Go to: Business Hub → Profile Settings\n\nYou can update your:\n• business description\n• services\n• pricing\n• photos",
                        ],
                    ],
                ],
                [
                    'slug' => 'policies',
                    'name' => 'Policies & Safety',
                    'chip_label' => 'Policies',
                    'subtitle' => 'Platform rules and service policies',
                    'icon' => 'safety',
                    'articles' => [
                        [
                            'question' => 'Where do my service policies appear?',
                            'excerpt' => 'Your policies appear on your business profile and during the booking process.',
                            'answer' => "Your service policies appear on:\n• your business profile\n• during the booking process\n\nThis helps clients understand your cancellation and refund rules before booking.",
                        ],
                    ],
                ],
            ],
        ];
    }
}
