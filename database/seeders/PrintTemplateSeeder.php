<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrintTemplate;

class PrintTemplateSeeder extends Seeder
{
    public function run(): void
    {
        PrintTemplate::create([
            'name' => 'فاتورة ضريبية A4',
            'type' => 'invoice',
            'content' => file_get_contents(resource_path('views/print_templates/default_invoice_a4.blade.php')),
            'is_active' => true,
            'is_default' => true,
        ]);
        PrintTemplate::create([
            'name' => 'فاتورة حرارية مبسطة',
            'type' => 'order',
            'content' => file_get_contents(resource_path('views/print_templates/default_receipt_thermal.blade.php')),
            'is_active' => true,
            'is_default' => true,
        ]);
    }
}
