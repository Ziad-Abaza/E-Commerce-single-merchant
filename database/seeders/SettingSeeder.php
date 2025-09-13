<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'E-Commerce Store',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of your website displayed in the header and title',
                'is_public' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'site_description',
                'value' => 'Your one-stop shop for quality products',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'Site Description',
                'description' => 'Brief description of your website for SEO and social sharing',
                'is_public' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@example.com',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'is_public' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'label' => 'Maintenance Mode',
                'description' => 'Enable to put the site in maintenance mode',
                'is_public' => false,
                'sort_order' => 4,
            ],

            // Appearance Settings
            [
                'key' => 'theme_color',
                'value' => 'blue',
                'type' => 'select',
                'group' => 'appearance',
                'label' => 'Theme Color',
                'description' => 'Primary color scheme for the website',
                'options' => [
                    ['value' => 'blue', 'label' => 'Blue'],
                    ['value' => 'green', 'label' => 'Green'],
                    ['value' => 'purple', 'label' => 'Purple'],
                    ['value' => 'red', 'label' => 'Red'],
                ],
                'is_public' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'logo_url',
                'value' => '/images/logo.png',
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Logo URL',
                'description' => 'URL to your site logo',
                'is_public' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'products_per_page',
                'value' => '12',
                'type' => 'number',
                'group' => 'appearance',
                'label' => 'Products Per Page',
                'description' => 'Number of products to display per page',
                'is_public' => true,
                'sort_order' => 3,
            ],

            // Email Settings
            [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'type' => 'text',
                'group' => 'email',
                'label' => 'SMTP Host',
                'description' => 'SMTP server hostname',
                'is_public' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'number',
                'group' => 'email',
                'label' => 'SMTP Port',
                'description' => 'SMTP server port',
                'is_public' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Email Notifications',
                'description' => 'Enable email notifications for orders and updates',
                'is_public' => false,
                'sort_order' => 3,
            ],

            // Payment Settings
            [
                'key' => 'currency',
                'value' => 'USD',
                'type' => 'select',
                'group' => 'payment',
                'label' => 'Currency',
                'description' => 'Default currency for the store',
                'options' => [
                    ['value' => 'USD', 'label' => 'US Dollar (USD)'],
                    ['value' => 'EUR', 'label' => 'Euro (EUR)'],
                    ['value' => 'GBP', 'label' => 'British Pound (GBP)'],
                    ['value' => 'EGP', 'label' => 'Egyptian Pound (EGP)'],
                ],
                'is_public' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'tax_rate',
                'value' => '0.10',
                'type' => 'number',
                'group' => 'payment',
                'label' => 'Tax Rate',
                'description' => 'Default tax rate (as decimal, e.g., 0.10 for 10%)',
                'is_public' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '100',
                'type' => 'number',
                'group' => 'payment',
                'label' => 'Free Shipping Threshold',
                'description' => 'Minimum order amount for free shipping',
                'is_public' => true,
                'sort_order' => 3,
            ],

            // Security Settings
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'number',
                'group' => 'security',
                'label' => 'Max Login Attempts',
                'description' => 'Maximum failed login attempts before lockout',
                'is_public' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'number',
                'group' => 'security',
                'label' => 'Session Timeout (minutes)',
                'description' => 'User session timeout in minutes',
                'is_public' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'require_email_verification',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Require Email Verification',
                'description' => 'Require users to verify their email address',
                'is_public' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
