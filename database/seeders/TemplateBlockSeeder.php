<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateBlock;

class TemplateBlockSeeder extends Seeder
{
    public function run(): void
    {
        TemplateBlock::updateOrCreate([
            'key' => 'header'
        ], [
            'name' => 'Header',
            'content' => '<div style="text-align:center;"><h3>اسم المنشأة</h3><p>العنوان: ... | الهاتف: ...</p></div>'
        ]);

        TemplateBlock::updateOrCreate([
            'key' => 'items_row'
        ], [
            'name' => 'Items Table',
            'content' => '{items_table}'
        ]);

        TemplateBlock::updateOrCreate([
            'key' => 'footer'
        ], [
            'name' => 'Footer',
            'content' => '<div style="text-align:center;margin-top:10px;"><p>شكراً لزيارتكم</p></div>'
        ]);
    }
}
