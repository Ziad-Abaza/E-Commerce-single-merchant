<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to avoid issues with truncating
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the policies table
        Policy::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $policies = [
            [
                'type' => 'privacy',
                'title' => 'Information Collection',
                'content' => '<div><h3>Information Collection</h3><p>We collect personal information such as name, email, phone number, and payment details when you use our services or make a purchase.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Use of Information',
                'content' => '<div><h3>Use of Information</h3><p>The information collected is used to process orders, improve customer experience, and provide personalized offers and recommendations.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Data Sharing',
                'content' => '<div><h3>Data Sharing</h3><p>We do not sell personal data. We may share information with trusted partners for order fulfillment, shipping, and payment processing.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Cookies and Tracking',
                'content' => '<div><h3>Cookies and Tracking</h3><p>Our website uses cookies and tracking technologies to enhance user experience, analyze traffic, and customize content.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Third-Party Services',
                'content' => '<div><h3>Third-Party Services</h3><p>We may use third-party services such as payment gateways and analytics providers that have their own privacy policies.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'User Rights',
                'content' => '<div><h3>User Rights</h3><p>Users have the right to access, update, or delete their personal information by contacting our support team.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Data Security',
                'content' => '<div><h3>Data Security</h3><p>We implement appropriate technical and organizational measures to protect personal data from unauthorized access or disclosure.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Data Retention',
                'content' => '<div><h3>Data Retention</h3><p>Personal data is retained only as long as necessary to provide services, comply with legal obligations, or resolve disputes.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Children’s Privacy',
                'content' => '<div><h3>Children’s Privacy</h3><p>Our services are not directed to children under 13. We do not knowingly collect personal information from children.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Email and Marketing',
                'content' => '<div><h3>Email and Marketing</h3><p>By subscribing to our newsletter, you consent to receive promotional emails. Users can unsubscribe at any time.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Policy Updates',
                'content' => '<div><h3>Policy Updates</h3><p>We may update this privacy policy from time to time. Changes will be posted on the website with the updated date.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'privacy',
                'title' => 'Contact Information',
                'content' => '<div><h3>Contact Information</h3><p>For questions about privacy, users can contact us via email or phone. We will respond promptly to privacy-related inquiries.</p></div>',
                'is_active' => true,
            ],

            [
                'type' => 'faq',
                'title' => 'How can I place an order?',
                'content' => '<div><h3>How can I place an order?</h3><p>You can browse our products, add items to your cart, and follow the checkout process to complete your order securely.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'What payment methods are accepted?',
                'content' => '<div><h3>What payment methods are accepted?</h3><p>We accept major credit/debit cards, PayPal, and other local payment options depending on your location.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'Can I track my order?',
                'content' => '<div><h3>Can I track my order?</h3><p>Yes, once your order is shipped, you will receive a tracking number via email to monitor the delivery status.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'What is the return policy?',
                'content' => '<div><h3>What is the return policy?</h3><p>You can return eligible items within 14 days of delivery. Products must be in original condition and packaging.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'How long does delivery take?',
                'content' => '<div><h3>How long does delivery take?</h3><p>Delivery time depends on your location. Typically, orders are delivered within 3-7 business days.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'Do you ship internationally?',
                'content' => '<div><h3>Do you ship internationally?</h3><p>Yes, we ship to selected countries. International shipping costs and times may vary based on the destination.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'How can I cancel or modify my order?',
                'content' => '<div><h3>How can I cancel or modify my order?</h3><p>You can request to cancel or modify your order by contacting our support team within 24 hours of placing it.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'Are my personal details secure?',
                'content' => '<div><h3>Are my personal details secure?</h3><p>Yes, we use advanced security measures including SSL encryption to protect your personal and payment information.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'Do you offer discounts or promotions?',
                'content' => '<div><h3>Do you offer discounts or promotions?</h3><p>Yes, we regularly offer promotions and discounts. Subscribe to our newsletter to stay updated on the latest offers.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'Can I change my shipping address?',
                'content' => '<div><h3>Can I change my shipping address?</h3><p>You can update your shipping address before the order is shipped by contacting our support team.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'How do I contact customer support?',
                'content' => '<div><h3>How do I contact customer support?</h3><p>You can reach our support team via email, phone, or live chat available on our website.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'faq',
                'title' => 'What if I receive a damaged or wrong item?',
                'content' => '<div><h3>What if I receive a damaged or wrong item?</h3><p>If your order arrives damaged or incorrect, please contact our support immediately to resolve the issue and arrange a replacement or refund.</p></div>',
                'is_active' => true,
            ],


            [
                'type' => 'warranty',
                'title' => 'Warranty Coverage',
                'content' => '<div><h3>Warranty Coverage</h3><p>All products come with a manufacturer\'s warranty covering defects in materials and workmanship under normal use.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Warranty Period',
                'content' => '<div><h3>Warranty Period</h3><p>The warranty period varies by product type and is clearly indicated on the product page or packaging.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Proof of Purchase',
                'content' => '<div><h3>Proof of Purchase</h3><p>To claim warranty service, customers must provide a valid proof of purchase, such as a receipt or invoice.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Exclusions',
                'content' => '<div><h3>Exclusions</h3><p>The warranty does not cover damage caused by misuse, accidents, unauthorized repairs, or normal wear and tear.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Repair or Replacement',
                'content' => '<div><h3>Repair or Replacement</h3><p>During the warranty period, defective products will be repaired or replaced at no additional cost, depending on the assessment.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Warranty Claim Process',
                'content' => '<div><h3>Warranty Claim Process</h3><p>Customers must contact our support team to initiate a warranty claim. Instructions for returning the product will be provided.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Shipping Costs',
                'content' => '<div><h3>Shipping Costs</h3><p>Shipping costs for warranty service may be covered by the store or the customer, depending on the situation and policy.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Non-Transferable Warranty',
                'content' => '<div><h3>Non-Transferable Warranty</h3><p>The warranty is valid only for the original purchaser and cannot be transferred to another person.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'International Warranty',
                'content' => '<div><h3>International Warranty</h3><p>Some products may offer international warranty. Coverage and conditions depend on the manufacturer and region.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Warranty Limitations',
                'content' => '<div><h3>Warranty Limitations</h3><p>The warranty does not cover damages caused by natural disasters, misuse, or unauthorized modifications.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Extended Warranty',
                'content' => '<div><h3>Extended Warranty</h3><p>Customers may purchase extended warranty plans for additional coverage beyond the standard warranty period.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'warranty',
                'title' => 'Contact for Warranty Support',
                'content' => '<div><h3>Contact for Warranty Support</h3><p>For any warranty-related questions or claims, customers can contact our support team via email, phone, or live chat.</p></div>',
                'is_active' => true,
            ],


            [
                'type' => 'shipping',
                'title' => 'Shipping Methods',
                'content' => '<div><h3>Shipping Methods</h3><p>We offer multiple shipping options including standard, express, and same-day delivery depending on your location.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Shipping Charges',
                'content' => '<div><h3>Shipping Charges</h3><p>Shipping fees are calculated based on the product weight, dimensions, and delivery location. Some promotions may include free shipping.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Order Processing Time',
                'content' => '<div><h3>Order Processing Time</h3><p>Orders are typically processed within 1-2 business days after payment confirmation before shipment.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Delivery Time',
                'content' => '<div><h3>Delivery Time</h3><p>Estimated delivery times vary by shipping method and destination, usually ranging from 3-7 business days for standard shipping.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Order Tracking',
                'content' => '<div><h3>Order Tracking</h3><p>Once your order is shipped, a tracking number will be provided so you can monitor the delivery status online.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'International Shipping',
                'content' => '<div><h3>International Shipping</h3><p>We offer international shipping to selected countries. Delivery time and charges vary based on the destination.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Shipping Restrictions',
                'content' => '<div><h3>Shipping Restrictions</h3><p>Some products may have shipping restrictions due to size, weight, or legal regulations in certain regions.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Lost or Damaged Shipments',
                'content' => '<div><h3>Lost or Damaged Shipments</h3><p>If your shipment is lost or damaged during transit, please contact our support team immediately for assistance.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Change of Shipping Address',
                'content' => '<div><h3>Change of Shipping Address</h3><p>Shipping addresses can be updated before the order is shipped. Contact support promptly to make changes.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Signature on Delivery',
                'content' => '<div><h3>Signature on Delivery</h3><p>Some shipments may require a signature upon delivery to ensure the package is received by the intended recipient.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Shipping Promotions',
                'content' => '<div><h3>Shipping Promotions</h3><p>We occasionally offer free or discounted shipping during promotional periods. Details will be available on the website.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'shipping',
                'title' => 'Contact for Shipping Inquiries',
                'content' => '<div><h3>Contact for Shipping Inquiries</h3><p>For any shipping-related questions, customers can contact our support team via email, phone, or live chat.</p></div>',
                'is_active' => true,
            ],


            [
                'type' => 'return',
                'title' => 'Return Eligibility',
                'content' => '<div><h3>Return Eligibility</h3><p>Products are eligible for return if they are unused, in original packaging, and within the return period indicated on the product page.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Return Period',
                'content' => '<div><h3>Return Period</h3><p>Customers can return eligible products within 14 days of delivery. Certain items may have different return windows, as specified.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Non-Returnable Items',
                'content' => '<div><h3>Non-Returnable Items</h3><p>Some products, such as perishable goods, personal care items, or customized products, cannot be returned.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Return Process',
                'content' => '<div><h3>Return Process</h3><p>To initiate a return, contact our support team. You will receive instructions for returning the product safely.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Return Shipping',
                'content' => '<div><h3>Return Shipping</h3><p>Return shipping costs may be covered by the store or the customer, depending on the reason for the return.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Refunds',
                'content' => '<div><h3>Refunds</h3><p>Once the returned product is received and inspected, a refund will be issued to the original payment method within 5-7 business days.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Exchanges',
                'content' => '<div><h3>Exchanges</h3><p>Customers can exchange products for the same item in a different size or color if available. Contact support for exchange instructions.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Damaged or Defective Items',
                'content' => '<div><h3>Damaged or Defective Items</h3><p>If you receive a damaged or defective product, contact our support immediately for a replacement or refund.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Partial Refunds',
                'content' => '<div><h3>Partial Refunds</h3><p>Partial refunds may be granted for products that are not returned in their original condition, damaged, or missing parts.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Return Approval',
                'content' => '<div><h3>Return Approval</h3><p>All returns require prior approval from our support team. Unauthorized returns may not be accepted.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Refund Exceptions',
                'content' => '<div><h3>Refund Exceptions</h3><p>Refunds are not provided for items returned outside the return window or for non-returnable products.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'return',
                'title' => 'Contact for Returns',
                'content' => '<div><h3>Contact for Returns</h3><p>For any return-related questions or assistance, customers can reach our support team via email, phone, or live chat.</p></div>',
                'is_active' => true,
            ],

            [
                'type' => 'terms',
                'title' => 'Acceptance of Terms',
                'content' => '<div><h3>Acceptance of Terms</h3><p>By using our website, you agree to comply with and be bound by these Terms and Conditions.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Eligibility',
                'content' => '<div><h3>Eligibility</h3><p>Users must be at least 18 years old or have parental consent to use our services.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Account Responsibilities',
                'content' => '<div><h3>Account Responsibilities</h3><p>Users are responsible for maintaining the confidentiality of their account information and for all activities under their account.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Product Information',
                'content' => '<div><h3>Product Information</h3><p>We strive to provide accurate product information. However, we do not guarantee that all descriptions, images, or specifications are error-free.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Pricing',
                'content' => '<div><h3>Pricing</h3><p>All prices are subject to change without notice. Discounts and promotions may be applied according to specific terms.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Order Acceptance',
                'content' => '<div><h3>Order Acceptance</h3><p>We reserve the right to accept or reject any order for any reason, including availability, errors in pricing, or suspected fraudulent activity.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Payment Terms',
                'content' => '<div><h3>Payment Terms</h3><p>Payment must be completed through the available payment methods before order processing.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Intellectual Property',
                'content' => '<div><h3>Intellectual Property</h3><p>All content on the website, including images, text, and logos, are the property of the store and protected by copyright laws.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'User Conduct',
                'content' => '<div><h3>User Conduct</h3><p>Users agree not to engage in any unlawful, abusive, or harmful activities on our website.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Limitation of Liability',
                'content' => '<div><h3>Limitation of Liability</h3><p>We are not liable for any direct, indirect, or consequential damages arising from the use of our website or products.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Termination',
                'content' => '<div><h3>Termination</h3><p>We reserve the right to suspend or terminate user accounts and access to the website for violations of these terms.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'terms',
                'title' => 'Governing Law',
                'content' => '<div><h3>Governing Law</h3><p>These terms are governed by the laws of the country in which the store operates. Disputes will be resolved in the appropriate legal jurisdiction.</p></div>',
                'is_active' => true,
            ],

            [
                'type' => 'cookies',
                'title' => 'What Are Cookies?',
                'content' => '<div><h3>What Are Cookies?</h3><p>Cookies are small text files stored on your device to enhance your browsing experience and provide personalized content.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Types of Cookies',
                'content' => '<div><h3>Types of Cookies</h3><p>We use essential, performance, functionality, and targeting cookies to improve site performance and provide relevant content.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Essential Cookies',
                'content' => '<div><h3>Essential Cookies</h3><p>These cookies are necessary for the website to function properly, such as maintaining login sessions and shopping cart contents.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Performance Cookies',
                'content' => '<div><h3>Performance Cookies</h3><p>Performance cookies collect anonymous data on site usage to help us improve website performance and user experience.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Functionality Cookies',
                'content' => '<div><h3>Functionality Cookies</h3><p>These cookies allow the website to remember your preferences, such as language or region settings.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Targeting Cookies',
                'content' => '<div><h3>Targeting Cookies</h3><p>Targeting cookies track browsing habits to deliver personalized advertisements and offers relevant to your interests.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Third-Party Cookies',
                'content' => '<div><h3>Third-Party Cookies</h3><p>We may use third-party services that place cookies on your device to provide analytics or advertising services.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Cookie Consent',
                'content' => '<div><h3>Cookie Consent</h3><p>By using our website, you consent to the use of cookies as described in this policy. You can manage your cookie preferences at any time.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Managing Cookies',
                'content' => '<div><h3>Managing Cookies</h3><p>You can control or disable cookies through your browser settings. Note that some features of the website may not function properly.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Data Collection',
                'content' => '<div><h3>Data Collection</h3><p>Cookies may collect information such as IP address, device type, and browsing behavior to analyze trends and improve our services.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Retention of Cookies',
                'content' => '<div><h3>Retention of Cookies</h3><p>Cookies may remain on your device for varying periods depending on their type. Session cookies are deleted when you close your browser.</p></div>',
                'is_active' => true,
            ],
            [
                'type' => 'cookies',
                'title' => 'Contact for Cookie Questions',
                'content' => '<div><h3>Contact for Cookie Questions</h3><p>If you have questions about our cookie policy or wish to manage your preferences, please contact our support team.</p></div>',
                'is_active' => true,
            ],
        ];

        foreach ($policies as $policy) {
            Policy::create($policy);
        }

        $this->command->info('تم إنشاء سياسات الموقع بنجاح!');
    }
}
