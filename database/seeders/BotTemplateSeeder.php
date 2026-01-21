<?php

namespace Database\Seeders;

use App\Models\BotTemplate;
use Illuminate\Database\Seeder;

class BotTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Customer Support Templates
            [
                'name' => 'Customer Support Basic',
                'description' => 'Essential auto-replies for handling common customer inquiries, complaints, and support requests.',
                'category' => 'customer_support',
                'icon' => '🎧',
                'is_premium' => false,
                'rules' => [
                    [
                        'keyword' => 'help',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "👋 Hi there! I'm here to help. What can I assist you with today?\n\n1️⃣ Product information\n2️⃣ Order status\n3️⃣ Returns & refunds\n4️⃣ Technical support\n5️⃣ Speak to an agent\n\nPlease reply with the number of your choice.",
                    ],
                    [
                        'keyword' => 'order status',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "📦 To check your order status, please provide your order number.\n\nFormat: ORD-XXXXX\n\nIf you don't have your order number, please check your email confirmation.",
                    ],
                    [
                        'keyword' => 'refund',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "💰 We're sorry to hear you want a refund.\n\nOur refund policy:\n• Returns within 30 days\n• Original packaging required\n• Processing time: 5-7 business days\n\nTo start a refund, please provide your order number.",
                    ],
                    [
                        'keyword' => 'complaint',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "😔 We're truly sorry to hear about your experience.\n\nYour feedback is important to us. Please describe the issue and a customer service representative will respond within 24 hours.\n\nThank you for your patience.",
                    ],
                    [
                        'keyword' => 'agent',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "👤 I'll connect you with a human agent.\n\n⏰ Our support hours:\nMon-Fri: 9AM - 6PM\nSat: 10AM - 4PM\nSun: Closed\n\nCurrent wait time: ~5 minutes\n\nAn agent will be with you shortly!",
                    ],
                ],
            ],

            // FAQ Templates
            [
                'name' => 'E-commerce FAQ',
                'description' => 'Common questions and answers for online stores including shipping, payments, and returns.',
                'category' => 'faq',
                'icon' => '❓',
                'is_premium' => false,
                'rules' => [
                    [
                        'keyword' => 'shipping',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🚚 *Shipping Information*\n\n📍 We ship worldwide!\n\n*Delivery Times:*\n• Standard: 5-7 business days\n• Express: 2-3 business days\n• Same Day (local): Order before 2PM\n\n*Shipping Costs:*\n• Orders over \$50: FREE\n• Standard: \$5.99\n• Express: \$12.99",
                    ],
                    [
                        'keyword' => 'payment',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "💳 *Payment Methods*\n\nWe accept:\n✅ Credit/Debit Cards (Visa, Mastercard, Amex)\n✅ PayPal\n✅ Mobile Money\n✅ Bank Transfer\n✅ Cash on Delivery (selected areas)\n\nAll payments are secure and encrypted 🔒",
                    ],
                    [
                        'keyword' => 'return',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "↩️ *Return Policy*\n\n• 30-day return window\n• Items must be unused & in original packaging\n• Free returns for defective items\n• Refund processed within 5-7 days\n\nTo start a return, reply with your order number.",
                    ],
                    [
                        'keyword' => 'track',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "📍 *Track Your Order*\n\nTo track your package:\n1. Check your email for tracking number\n2. Visit our tracking page\n3. Or reply with your order number here\n\nExample: ORD-12345",
                    ],
                    [
                        'keyword' => 'discount',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🎉 *Current Promotions*\n\n💰 Use code *WELCOME10* for 10% off your first order!\n\n📧 Subscribe to our newsletter for exclusive deals\n\n⭐ Join our loyalty program and earn points on every purchase!",
                    ],
                ],
            ],

            // Welcome Messages
            [
                'name' => 'Welcome & Greeting',
                'description' => 'Friendly welcome messages for new conversations with business hours and quick menu.',
                'category' => 'welcome',
                'icon' => '👋',
                'is_premium' => false,
                'rules' => [
                    [
                        'keyword' => 'hi',
                        'match_type' => 'exact',
                        'reply_type' => 'text',
                        'reply_content' => "👋 Hello and welcome!\n\nThank you for reaching out to us. How can I assist you today?\n\n📌 Quick Menu:\n1️⃣ Products & Services\n2️⃣ Pricing\n3️⃣ Support\n4️⃣ Contact Us\n\nJust reply with a number or type your question!",
                    ],
                    [
                        'keyword' => 'hello',
                        'match_type' => 'exact',
                        'reply_type' => 'text',
                        'reply_content' => "👋 Hello and welcome!\n\nThank you for reaching out to us. How can I assist you today?\n\n📌 Quick Menu:\n1️⃣ Products & Services\n2️⃣ Pricing\n3️⃣ Support\n4️⃣ Contact Us\n\nJust reply with a number or type your question!",
                    ],
                    [
                        'keyword' => 'good morning',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "☀️ Good morning! Hope you're having a great day!\n\nWelcome to our business. I'm here to help you with any questions.\n\nWhat can I do for you today?",
                    ],
                    [
                        'keyword' => 'good evening',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🌙 Good evening! Thanks for reaching out.\n\nOur team is here to assist you. How may I help?",
                    ],
                    [
                        'keyword' => 'thank',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🙏 You're very welcome!\n\nIs there anything else I can help you with?\n\nFeel free to message us anytime. Have a wonderful day! 😊",
                    ],
                    [
                        'keyword' => 'bye',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "👋 Goodbye! Thank you for chatting with us.\n\nRemember, we're always here when you need us.\n\nHave a great day! 🌟",
                    ],
                ],
            ],

            // Order Status
            [
                'name' => 'Order Management',
                'description' => 'Automated responses for order tracking, status updates, and delivery inquiries.',
                'category' => 'order_status',
                'icon' => '📦',
                'is_premium' => false,
                'rules' => [
                    [
                        'keyword' => 'order',
                        'match_type' => 'exact',
                        'reply_type' => 'text',
                        'reply_content' => "📦 *Order Services*\n\nHow can I help with your order?\n\n1️⃣ Place a new order\n2️⃣ Track existing order\n3️⃣ Modify my order\n4️⃣ Cancel order\n5️⃣ Report an issue\n\nReply with the number of your choice.",
                    ],
                    [
                        'keyword' => 'new order',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🛒 Ready to place an order?\n\nGreat! Here's how:\n\n1. Browse our catalog\n2. Add items to your cart\n3. Proceed to checkout\n4. Select payment method\n5. Confirm order\n\nOr simply tell me what you'd like to order!",
                    ],
                    [
                        'keyword' => 'where is my order',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "📍 Let me help you track your order!\n\nPlease provide:\n• Order number (ORD-XXXXX)\n• OR email used for order\n• OR phone number\n\nI'll look up the status right away!",
                    ],
                    [
                        'keyword' => 'cancel order',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "⚠️ *Order Cancellation*\n\nI can help you cancel your order.\n\n📝 Note: Orders can only be cancelled if not yet shipped.\n\nPlease provide your order number and reason for cancellation.",
                    ],
                    [
                        'keyword' => 'delivery',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🚚 *Delivery Information*\n\n⏰ Standard: 3-5 business days\n⚡ Express: 1-2 business days\n🏃 Same Day: Within 6 hours (local)\n\nTracking link sent via SMS/Email after dispatch.\n\nNeed to change delivery address? Let me know!",
                    ],
                ],
            ],

            // Marketing Template
            [
                'name' => 'Marketing & Promotions',
                'description' => 'Promotional messages with discount codes, special offers, and campaign responses.',
                'category' => 'marketing',
                'icon' => '📢',
                'is_premium' => true,
                'rules' => [
                    [
                        'keyword' => 'deals',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🔥 *HOT DEALS ALERT!* 🔥\n\n💰 Up to 50% OFF selected items\n🎁 Buy 2 Get 1 FREE\n🚚 FREE shipping over \$50\n\n⏰ Limited time only!\n\nUse code: *DEAL2024*\n\n👉 Shop now at [link]",
                    ],
                    [
                        'keyword' => 'subscribe',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "📧 *Join Our VIP List!*\n\n✨ Benefits:\n• Exclusive discounts\n• Early access to sales\n• Birthday rewards\n• Free samples\n\nReply with your email to subscribe!\n\n🎁 *Get 15% OFF your first order!*",
                    ],
                    [
                        'keyword' => 'new arrival',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "✨ *NEW ARRIVALS* ✨\n\nCheck out what's new:\n\n🆕 [Product 1] - \$XX\n🆕 [Product 2] - \$XX\n🆕 [Product 3] - \$XX\n\n🎉 First 50 buyers get 20% OFF!\n\nShop now before they're gone!",
                    ],
                ],
            ],

            // Appointment Booking
            [
                'name' => 'Appointment Booking',
                'description' => 'Handle appointment scheduling, modifications, and reminders for service businesses.',
                'category' => 'appointment',
                'icon' => '📅',
                'is_premium' => true,
                'rules' => [
                    [
                        'keyword' => 'appointment',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "📅 *Appointment Booking*\n\nHow can I help?\n\n1️⃣ Book new appointment\n2️⃣ Reschedule appointment\n3️⃣ Cancel appointment\n4️⃣ View my appointments\n\n⏰ Available hours:\nMon-Fri: 9AM - 7PM\nSat: 10AM - 5PM",
                    ],
                    [
                        'keyword' => 'book',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "📝 *Book Appointment*\n\nPlease provide:\n\n1️⃣ Service type\n2️⃣ Preferred date\n3️⃣ Preferred time\n4️⃣ Your name\n5️⃣ Contact number\n\nOr reply with: [Service] on [Date] at [Time]",
                    ],
                    [
                        'keyword' => 'reschedule',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "🔄 *Reschedule Appointment*\n\nTo reschedule, please provide:\n• Current appointment date/time\n• New preferred date/time\n\n⚠️ Rescheduling must be done 24hrs in advance.",
                    ],
                    [
                        'keyword' => 'confirm',
                        'match_type' => 'contains',
                        'reply_type' => 'text',
                        'reply_content' => "✅ *Appointment Confirmed!*\n\n📅 Date: [DATE]\n⏰ Time: [TIME]\n📍 Location: [ADDRESS]\n\n📝 Please arrive 10 minutes early.\n\n❌ To cancel, reply CANCEL",
                    ],
                ],
            ],
        ];

        foreach ($templates as $template) {
            BotTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
