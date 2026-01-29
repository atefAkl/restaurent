<?php

namespace App\Http\Controllers;

use App\Models\ReportComponent;
use App\Models\TemplateBlockComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportComponentController extends Controller
{
    public function index()
    {
        $components = ReportComponent::withCount('templateBlockComponents')
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('report-components.index', compact('components'));
    }

    public function create()
    {
        $types = [
            'header' => 'ترويسة',
            'text' => 'نص',
            'image' => 'صورة',
            'table' => 'جدول',
            'barcode' => 'باركود',
            'qr_code' => 'QR Code',
            'line' => 'خط فاصل',
            'logo' => 'شعار',
        ];

        return view('report-components.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys($this->getComponentTypes())),
            'description' => 'nullable|string',
            'content_template' => 'nullable|array',
            'default_properties' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $component = ReportComponent::create([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'content_template' => $request->content_template ?? [],
                'default_properties' => $request->default_properties ?? [],
                'is_active' => $request->boolean('is_active', true),
                'is_system' => false,
            ]);

            DB::commit();

            return redirect()->route('report-components.index')
                ->with('success', 'تم إنشاء المكون بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ReportComponent $reportComponent)
    {
        $component = $reportComponent->load(['templateBlockComponents.templateBlock']);
        
        return view('report-components.show', compact('component'));
    }

    public function edit(ReportComponent $reportComponent)
    {
        if ($reportComponent->is_system) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعديل مكونات النظام');
        }

        $types = $this->getComponentTypes();

        return view('report-components.edit', compact('reportComponent', 'types'));
    }

    public function update(Request $request, ReportComponent $reportComponent)
    {
        if ($reportComponent->is_system) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعديل مكونات النظام');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys($this->getComponentTypes())),
            'description' => 'nullable|string',
            'content_template' => 'nullable|array',
            'default_properties' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $reportComponent->update([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'content_template' => $request->content_template ?? [],
                'default_properties' => $request->default_properties ?? [],
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('report-components.show', $reportComponent)
                ->with('success', 'تم تحديث المكون بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(ReportComponent $reportComponent)
    {
        if ($reportComponent->is_system) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف مكونات النظام');
        }

        try {
            DB::beginTransaction();

            // Check if component is used in any template
            $usageCount = $reportComponent->templateBlockComponents()->count();
            if ($usageCount > 0) {
                return redirect()->back()
                    ->with('error', "لا يمكن حذف المكون لأنه مستخدم في {$usageCount} قالب");
            }

            $reportComponent->delete();

            DB::commit();

            return redirect()->route('report-components.index')
                ->with('success', 'تم حذف المكون بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function duplicate(ReportComponent $reportComponent)
    {
        try {
            DB::beginTransaction();

            $newComponent = $reportComponent->replicate();
            $newComponent->name = $reportComponent->name . ' (نسخة)';
            $newComponent->is_system = false;
            $newComponent->save();

            DB::commit();

            return redirect()->route('report-components.edit', $newComponent)
                ->with('success', 'تم نسخ المكون بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get component preview
     */
    public function preview(Request $request, ReportComponent $reportComponent)
    {
        $content = $request->get('content', []);
        $properties = $request->get('properties', []);

        $html = $reportComponent->generateHTML($content, $properties);

        return response()->json([
            'html' => $html,
            'required_variables' => $reportComponent->getRequiredVariables()
        ]);
    }

    /**
     * API endpoint to get available components for adding to template block
     */
    public function availableComponents(Request $request)
    {
        $type = $request->get('type');
        $search = $request->get('search');

        $query = ReportComponent::active();

        if ($type) {
            $query->byType($type);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $components = $query->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get()
            ->map(function ($component) {
                return [
                    'id' => $component->id,
                    'name' => $component->name,
                    'type' => $component->type,
                    'description' => $component->description,
                    'is_system' => $component->is_system,
                    'required_variables' => $component->getRequiredVariables(),
                    'default_properties' => $component->default_properties,
                    'content_template' => $component->content_template,
                ];
            });

        return response()->json($components);
    }

    private function getComponentTypes()
    {
        return [
            'header' => 'ترويسة',
            'text' => 'نص',
            'image' => 'صورة',
            'table' => 'جدول',
            'barcode' => 'باركود',
            'qr_code' => 'QR Code',
            'line' => 'خط فاصل',
            'logo' => 'شعار',
        ];
    }
}
