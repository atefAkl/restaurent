<?php

namespace App\Http\Controllers;

use App\Models\PrintTemplate;
use App\Models\ReportTheme;
use App\Models\TemplateBlock;
use App\Models\ReportElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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

    /**
     * Simple blocks visibility manager (show/hide).
     */
    public function blocks(PrintTemplate $reportTemplate)
    {
        $template = $reportTemplate->load(['templateBlocks']);
        return view('report-templates.blocks', compact('template'));
    }

    public function updateBlocks(Request $request, PrintTemplate $reportTemplate)
    {
        $visible = $request->input('visible', []);

        DB::beginTransaction();
        try {
            // Set all to false first
            $reportTemplate->templateBlocks()->update(['is_visible' => false]);

            if (is_array($visible) && !empty($visible)) {
                $ids = array_map('intval', $visible);
                $reportTemplate->templateBlocks()->whereIn('id', $ids)->update(['is_visible' => true]);
            }

            DB::commit();

            return redirect()->route('report-templates.blocks', $reportTemplate)->with('success', 'تم تحديث إظهار الأجزاء');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('updateBlocks error', ['template_id' => $reportTemplate->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ الحالة');
        }
    }

    /**
     * Import a4 blade file into a PrintTemplate with TemplateBlock fragments.
     * This creates a template and splits the file into named blocks that can be shown/hidden.
     */
    public function importA4(Request $request)
    {
        $path = resource_path('views/print_templates/a4template.blade.php');
        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'ملف القالب A4 غير موجود');
        }

        $content = File::get($path);

        // Check if a template with this name already exists
        $existing = PrintTemplate::where('name', 'A4 Template (file)')->first();
        if ($existing) {
            return redirect()->back()->with('info', 'قالب A4 مُسجّل بالفعل.');
        }

        DB::beginTransaction();
        try {
            // Create the template
            $template = PrintTemplate::create([
                'name' => 'A4 Template (file)',
                'description' => 'Imported from resources/views/print_templates/a4template.blade.php',
                'type' => 'invoice',
                'content' => null,
                'is_active' => true,
                'is_default' => true,
            ]);

            // Define markers to extract blocks
            $markers = [
                'Header Section' => 'header',
                'Details Grid (Client & Invoice Info)' => 'details',
                'Products Table' => 'products_table',
                'Summary & QR Code Section' => 'summary',
                'Footer' => 'footer',
            ];

            // For each marker, find the section starting at the marker until next marker
            $positions = [];
            foreach ($markers as $label => $key) {
                $needle = "<!-- $label -->";
                $pos = strpos($content, $needle);
                if ($pos !== false) {
                    $positions[$pos] = ['key' => $key, 'label' => $label, 'needle' => $needle];
                }
            }

            ksort($positions);
            $posKeys = array_keys($positions);

            for ($i = 0; $i < count($posKeys); $i++) {
                $startPos = $posKeys[$i];
                $meta = $positions[$startPos];
                $endPos = ($i + 1 < count($posKeys)) ? $posKeys[$i + 1] : strlen($content);
                $fragment = substr($content, $startPos, $endPos - $startPos);

                TemplateBlock::create([
                    'print_template_id' => $template->id,
                    'key' => $meta['key'],
                    'name' => $meta['label'],
                    'content' => $fragment,
                    'type' => 'content',
                    'position_x' => 0,
                    'position_y' => 0,
                    'width' => 0,
                    'height' => 0,
                    'is_visible' => true,
                    'order' => $i,
                ]);
            }

            DB::commit();

            return redirect()->route('report-templates.index')->with('success', 'تم استيراد قالب A4 وحفظه كقالب افتراضي');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('importA4 error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'فشل استيراد القالب: ' . $e->getMessage());
        }
    }

    public function saveCustomize(Request $request, PrintTemplate $reportTemplate)
    {
        $data = $request->validate([
            'blocks' => 'required|array'
        ]);

        $blocks = $request->input('blocks', []);

        \Log::info('ReportTemplate::saveCustomize called', ['template_id' => $reportTemplate->id, 'blocks_count' => count($blocks)]);
        DB::beginTransaction();
        try {
            $existingBlockIds = $reportTemplate->templateBlocks()->pluck('id')->toArray();
            $incomingBlockIds = [];

            foreach ($blocks as $b) {
                $blockId = $b['id'] ?? null;

                if (is_numeric($blockId)) {
                    $block = TemplateBlock::where('print_template_id', $reportTemplate->id)->find($blockId);
                    if (!$block) {
                        // skip invalid
                        continue;
                    }
                    $block->update([
                        'name' => $b['name'] ?? $block->name,
                        'position_x' => $b['position_x'] ?? $block->position_x,
                        'position_y' => $b['position_y'] ?? $block->position_y,
                        'width' => $b['width'] ?? $block->width,
                        'height' => $b['height'] ?? $block->height,
                    ]);
                } else {
                    $block = TemplateBlock::create([
                        'print_template_id' => $reportTemplate->id,
                        'key' => $b['key'] ?? ('block_' . Str::random(8)),
                        'name' => $b['name'] ?? 'Block',
                        'type' => $b['type'] ?? 'content',
                        'position_x' => $b['position_x'] ?? 0,
                        'position_y' => $b['position_y'] ?? 0,
                        'width' => $b['width'] ?? 200,
                        'height' => $b['height'] ?? 100,
                        'is_visible' => true,
                    ]);
                }

                $incomingBlockIds[] = $block->id;

                // process elements
                $incomingElementIds = [];
                $elements = $b['elements'] ?? [];
                foreach ($elements as $e) {
                    $elemId = $e['id'] ?? null;
                    if (is_numeric($elemId)) {
                        $element = ReportElement::where('template_block_id', $block->id)->find($elemId);
                        if (!$element) continue;
                        $element->update([
                            'name' => $e['name'] ?? $element->name,
                            'content' => $e['content'] ?? $element->content,
                            'position_x' => $e['position_x'] ?? $element->position_x,
                            'position_y' => $e['position_y'] ?? $element->position_y,
                            'width' => $e['width'] ?? $element->width,
                            'height' => $e['height'] ?? $element->height,
                            'properties' => $e['properties'] ?? $element->properties,
                            'is_visible' => true,
                        ]);
                    } else {
                        $element = ReportElement::create([
                            'template_block_id' => $block->id,
                            'type' => $e['type'] ?? 'text',
                            'name' => $e['name'] ?? 'Element',
                            'content' => $e['content'] ?? '',
                            'position_x' => $e['position_x'] ?? 0,
                            'position_y' => $e['position_y'] ?? 0,
                            'width' => $e['width'] ?? 100,
                            'height' => $e['height'] ?? 30,
                            'properties' => $e['properties'] ?? [],
                            'is_visible' => true,
                        ]);
                    }

                    $incomingElementIds[] = $element->id;
                }

                // delete removed elements
                $existingElementIds = $block->reportElements()->pluck('id')->toArray();
                $toDeleteElements = array_diff($existingElementIds, $incomingElementIds);
                if (!empty($toDeleteElements)) {
                    ReportElement::whereIn('id', $toDeleteElements)->delete();
                }
            }

            // delete removed blocks and their elements
            $toDeleteBlocks = array_diff($existingBlockIds, $incomingBlockIds);
            if (!empty($toDeleteBlocks)) {
                ReportElement::whereIn('template_block_id', $toDeleteBlocks)->delete();
                TemplateBlock::whereIn('id', $toDeleteBlocks)->delete();
            }

            DB::commit();

            \Log::info('ReportTemplate::saveCustomize saved', ['template_id' => $reportTemplate->id, 'incoming_block_ids' => $incomingBlockIds]);

            return response()->json(['success' => true, 'message' => 'تم حفظ التغييرات بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ReportTemplate::saveCustomize error', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
