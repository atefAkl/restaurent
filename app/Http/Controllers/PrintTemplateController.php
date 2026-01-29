<?php

namespace App\Http\Controllers;

use App\Models\PrintTemplate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrintTemplateController extends Controller
{
    public function index()
    {
        $templates = PrintTemplate::all();
        return view('print_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('print_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'content' => 'required|string',
        ]);
        PrintTemplate::create($request->only(['name', 'type', 'content', 'is_active', 'default']));
        return redirect()->route('print-templates.index')->with('success', 'تم إضافة القالب بنجاح');
    }

    public function edit(PrintTemplate $printTemplate)
    {
        return view('print_templates.edit', compact('printTemplate'));
    }

    public function update(Request $request, PrintTemplate $printTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'content' => 'required|string',
        ]);

        // تخصيص المكونات
        if ($request->has('show_customer') || $request->has('show_items') || $request->has('show_totals') || $request->has('show_payment') || $request->has('show_notes')) {
            $content = $printTemplate->content;
            $map = [
                'show_customer' => '{customer_name}',
                'show_items' => '{items_table}',
                'show_totals' => '{total_amount}',
                'show_payment' => '{payment_method}',
                'show_notes' => '{notes}',
            ];
            foreach ($map as $field => $var) {
                if ($request->has($field)) {
                    if (strpos($content, $var) === false) {
                        $content .= "\n$var\n";
                    }
                } else {
                    $content = str_replace($var, '', $content);
                }
            }
            $printTemplate->update([
                'content' => $content,
            ] + $request->only(['name', 'type', 'is_active', 'default']));
            return redirect()->route('print-templates.index')->with('success', 'تم تحديث القالب بنجاح');
        }

        $printTemplate->update($request->only(['name', 'type', 'content', 'is_active', 'default']));
        return redirect()->route('print-templates.index')->with('success', 'تم تحديث القالب بنجاح');
    }

    public function destroy(PrintTemplate $printTemplate)
    {
        $printTemplate->delete();
        return redirect()->route('print-templates.index')->with('success', 'تم حذف القالب بنجاح');
    }

    public function components(PrintTemplate $printTemplate)
    {
        return view('print_templates.components', compact('printTemplate'));
    }

    public function preview(PrintTemplate $printTemplate)
    {
        // استخدم نموذج Order حقيقي مع بيانات وهمية
        $order = new \App\Models\Order([
            'order_number' => 'ORD-20260129-0001',
            'created_at' => Carbon::now(),
            'customer_name' => 'محمد',
            'customer_phone' => '0500000000',
            'customer_address' => 'الرياض',
            'type' => 'local',
            'room_number' => '5',
            'subtotal' => 200,
            'tax_amount' => 30,
            'discount_amount' => 10,
            'total_amount' => 220,
            'paid_amount' => 100,
            'remaining_amount' => 120,
            'payment_method' => 'cash',
            'notes' => 'بدون بصل',
        ]);
        $order->setRelation('user', (new \App\Models\User(['name' => 'أحمد علي'])));
        $order->setRelation('orderItems', collect([
            (new \App\Models\OrderItem([
                'quantity' => 2,
                'price' => 50,
                'total_price' => 100
            ]))->setRelation('product', new \App\Models\Product(['name' => 'وجبة 1'])),
            (new \App\Models\OrderItem([
                'quantity' => 1,
                'price' => 100,
                'total_price' => 100
            ]))->setRelation('product', new \App\Models\Product(['name' => 'وجبة 2'])),
        ]));
        $html = \App\Services\PrintTemplateService::renderTemplate($printTemplate, $order);
        return view('orders.print_custom', compact('html', 'order'));
    }
}
