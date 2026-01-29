<?php

namespace App\Http\Controllers;

use App\Models\PrintTemplate;
use App\Models\ReportTheme;
use App\Models\TemplateBlock;
use App\Models\ReportElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportTemplateController extends Controller
{
    public function index()
    {
        $templates = PrintTemplate::with(['theme', 'templateBlocks'])
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(20);

        return view('report-templates.index', compact('templates'));
    }

    public function create()
    {
        $themes = ReportTheme::active()->get();
        $types = ['order', 'invoice', 'receipt', 'kitchen_order', 'shift_report', 'expense_report'];

        return view('report-templates.create', compact('themes', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:order,invoice,receipt,kitchen_order,shift_report,expense_report',
            'theme_id' => 'nullable|exists:report_themes,id',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // If this is set as default, remove default from other templates of same type
            if ($request->boolean('is_default')) {
                PrintTemplate::where('type', $request->type)->update(['is_default' => false]);
            }

            $template = PrintTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'theme_id' => $request->theme_id,
                'content' => $request->content,
                'is_active' => $request->boolean('is_active'),
                'is_default' => $request->boolean('is_default'),
                'settings' => $request->settings ?? [],
            ]);

            DB::commit();

            return redirect()->route('report-templates.index')
                ->with('success', 'تم إنشاء القالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(PrintTemplate $reportTemplate)
    {
        $template = $reportTemplate->load(['theme', 'templateBlocks.reportElements']);

        return view('report-templates.show', compact('template'));
    }

    public function edit(PrintTemplate $reportTemplate)
    {
        $template = $reportTemplate->load(['theme', 'templateBlocks.reportElements']);
        $themes = ReportTheme::active()->get();
        $types = ['order', 'invoice', 'receipt', 'kitchen_order', 'shift_report', 'expense_report'];

        return view('report-templates.edit', compact('template', 'themes', 'types'));
    }

    public function update(Request $request, PrintTemplate $reportTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:order,invoice,receipt,kitchen_order,shift_report,expense_report',
            'theme_id' => 'nullable|exists:report_themes,id',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // If this is set as default, remove default from other templates of same type
            if ($request->boolean('is_default')) {
                PrintTemplate::where('type', $request->type)
                    ->where('id', '!=', $reportTemplate->id)
                    ->update(['is_default' => false]);
            }

            $reportTemplate->update([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'theme_id' => $request->theme_id,
                'content' => $request->content,
                'is_active' => $request->boolean('is_active'),
                'is_default' => $request->boolean('is_default'),
                'settings' => $request->settings ?? [],
            ]);

            DB::commit();

            return redirect()->route('report-templates.index')
                ->with('success', 'تم تحديث القالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PrintTemplate $reportTemplate)
    {
        try {
            DB::beginTransaction();

            $reportTemplate->delete();

            DB::commit();

            return redirect()->route('report-templates.index')
                ->with('success', 'تم حذف القالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function duplicate(PrintTemplate $reportTemplate)
    {
        try {
            DB::beginTransaction();

            $newTemplate = $reportTemplate->replicate();
            $newTemplate->name = $reportTemplate->name . ' (نسخة)';
            $newTemplate->is_default = false;
            $newTemplate->save();

            // Duplicate blocks and elements
            foreach ($reportTemplate->templateBlocks as $block) {
                $newBlock = $block->replicate();
                $newBlock->print_template_id = $newTemplate->id;
                $newBlock->save();

                // Duplicate elements
                foreach ($block->reportElements as $element) {
                    $newElement = $element->replicate();
                    $newElement->template_block_id = $newBlock->id;
                    $newElement->save();
                }
            }

            DB::commit();

            return redirect()->route('report-templates.index')
                ->with('success', 'تم نسخ القالب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function customize(PrintTemplate $reportTemplate)
    {
        $template = $reportTemplate->load(['theme', 'templateBlocks.reportElements']);

        return view('report-templates.customize', compact('template'));
    }

    public function preview(PrintTemplate $reportTemplate)
    {
        // Sample data for preview
        $sampleData = $this->getSampleData($reportTemplate->type);

        $content = $reportTemplate->generateContent($sampleData);

        return view('report-templates.preview', [
            'template' => $reportTemplate,
            'content' => $content,
            'sampleData' => $sampleData
        ]);
    }

    private function getSampleData($type)
    {
        switch ($type) {
            case 'order':
            case 'invoice':
            case 'receipt':
                return [
                    'order_number' => 'ORD-20240129-0001',
                    'customer_name' => 'أحمد محمد',
                    'customer_phone' => '0501234567',
                    'date' => now()->format('Y-m-d'),
                    'time' => now()->format('H:i'),
                    'items' => [
                        ['name' => 'وجبة 1', 'quantity' => 2, 'price' => 50, 'total' => 100],
                        ['name' => 'مشروب 1', 'quantity' => 1, 'price' => 15, 'total' => 15],
                    ],
                    'subtotal' => 115,
                    'tax' => 17.25,
                    'total' => 132.25,
                    'cashier' => 'الموظف 1',
                ];

            case 'kitchen_order':
                return [
                    'order_number' => 'ORD-20240129-0001',
                    'table' => 'T5',
                    'items' => [
                        ['name' => 'وجبة 1', 'quantity' => 2, 'notes' => 'بدون بصل'],
                        ['name' => 'مشروب 1', 'quantity' => 1, 'notes' => 'بارد'],
                    ],
                    'time' => now()->format('H:i'),
                ];

            default:
                return [];
        }
    }
}
