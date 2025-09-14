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
                'key' => 'address',
                'value' => 'EGYPT, Alexandria, al-Ajami',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Address',
                'description' => 'Physical address of the store',
                'is_public' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'business_hours',
                'value' => 'Mon-Fri: 9am-5pm',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Business Hours',
                'description' => 'Business hours for the store',
                'is_public' => true,
                'sort_order' => 5,
            ],
            // contact settings
            [
                'key' => 'contact_email',
                'value' => 'contact@example.com',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'is_public' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+1 (234) 567-8900',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number',
                'is_public' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '+1 (234) 567-8900',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'WhatsApp Number',
                'description' => 'WhatsApp number for customer support',
                'is_public' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'facebook_url',
                'value' => 'https://www.facebook.com/example',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Facebook URL',
                'description' => 'URL to your Facebook page',
                'is_public' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/example',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Twitter URL',
                'description' => 'URL to your Twitter page',
                'is_public' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://www.instagram.com/example',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Instagram URL',
                'description' => 'URL to your Instagram page',
                'is_public' => true,
                'sort_order' => 6,
            ],
            [
                'key' => 'youtube_url',
                'value' => 'https://www.youtube.com/channel/example',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'YouTube URL',
                'description' => 'URL to your YouTube channel',
                'is_public' => true,
                'sort_order' => 7,
            ],
            [
                'key' => 'tiktok_url',
                'value' => 'https://www.tiktok.com/@example',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'TikTok URL',
                'description' => 'URL to your TikTok profile',
                'is_public' => true,
                'sort_order' => 8,
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
                'value' => '/images/default-logo.webp',
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Logo URL',
                'description' => 'URL to your site logo',
                'is_public' => true,
                'sort_order' => 2,
            ],

            // Payment Settings
            [
                'key' => 'currency',
                'value' => 'EGP',
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
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
