<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportComponent;

class ReportComponentsSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء المكونات الافتراضية
        ReportComponent::createSystemComponents();
        
        // إضافة بعض المكونات المخصصة
        ReportComponent::create([
            'name' => 'عنوان مخصص',
            'type' => 'header',
            'description' => 'عنوان قابل للتخصيص',
            'is_system' => false,
            'content_template' => [
                'text' => '{{title}}'
            ],
            'default_properties' => [
                'level' => 2,
                'text_align' => 'center',
                'font_size' => '20px',
                'color' => '#333333',
                'margin' => '15px 0'
            ]
        ]);

        ReportComponent::create([
            'name' => 'نص وصفي',
            'type' => 'text',
            'description' => 'نص وصفي قابل للتخصيص',
            'is_system' => false,
            'content_template' => [
                'text' => '{{description}}'
            ],
            'default_properties' => [
                'font_size' => '14px',
                'line_height' => '1.5',
                'color' => '#666666',
                'margin' => '10px 0'
            ]
        ]);

        ReportComponent::create([
            'name' => 'صورة المنتج',
            'type' => 'image',
            'description' => 'صورة للمنتج',
            'is_system' => false,
            'content_template' => [
                'src' => '{{product_image}}',
                'alt' => '{{product_name}}'
            ],
            'default_properties' => [
                'width' => '100px',
                'height' => '100px',
                'object_fit' => 'cover',
                'border_radius' => '5px',
                'margin' => '5px'
            ]
        ]);

        ReportComponent::create([
            'name' => 'باركود المنتج',
            'type' => 'barcode',
            'description' => 'باركود للمنتج',
            'is_system' => false,
            'content_template' => [
                'value' => '{{product_barcode}}',
                'type' => 'code128'
            ],
            'default_properties' => [
                'width' => '150px',
                'height' => '50px',
                'margin' => '5px 0'
            ]
        ]);

        ReportComponent::create([
            'name' => 'QR Code',
            'type' => 'qr_code',
            'description' => 'QR Code للطلب',
            'is_system' => false,
            'content_template' => [
                'value' => '{{order_url}}'
            ],
            'default_properties' => [
                'width' => '100px',
                'height' => '100px',
                'margin' => '10px auto',
                'display' => 'block'
            ]
        ]);

        ReportComponent::create([
            'name' => 'معلومات الاتصال',
            'type' => 'text',
            'description' => 'معلومات الاتصال بالشركة',
            'is_system' => false,
            'content_template' => [
                'text' => 'هاتف: {{phone}} | الإيميل: {{email}} | العنوان: {{address}}'
            ],
            'default_properties' => [
                'font_size' => '12px',
                'text_align' => 'center',
                'color' => '#666666',
                'margin' => '10px 0'
            ]
        ]);

        ReportComponent::create([
            'name' => 'توقيع',
            'type' => 'text',
            'description' => 'توقيع أو ختم',
            'is_system' => false,
            'content_template' => [
                'text' => '{{signature}}'
            ],
            'default_properties' => [
                'font_size' => '14px',
                'text_align' => 'right',
                'font_style' => 'italic',
                'margin' => '20px 0 0 0'
            ]
        ]);

        $this->command->info('Report components seeded successfully!');
    }
}
