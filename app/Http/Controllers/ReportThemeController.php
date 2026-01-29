<?php

namespace App\Http\Controllers;

use App\Models\ReportTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportThemeController extends Controller
{
    public function index()
    {
        $themes = ReportTheme::withCount('printTemplates')
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate(20);
            
        return view('report-themes.index', compact('themes'));
    }

    public function create()
    {
        return view('report-themes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'styles' => 'required|array',
            'styles.colors' => 'required|array',
            'styles.fonts' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // If this is set as default, remove default from other themes
            if ($request->boolean('is_default')) {
                ReportTheme::where('is_default', true)->update(['is_default' => false]);
            }

            $theme = ReportTheme::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'is_default' => $request->boolean('is_default'),
                'styles' => $request->styles,
            ]);

            DB::commit();

            return redirect()->route('report-themes.index')
                ->with('success', 'تم إنشاء الثيم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ReportTheme $reportTheme)
    {
        $theme = $reportTheme->load('printTemplates');
        
        return view('report-themes.show', compact('theme'));
    }

    public function edit(ReportTheme $reportTheme)
    {
        $theme = $reportTheme->load('printTemplates');
        
        return view('report-themes.edit', compact('theme'));
    }

    public function update(Request $request, ReportTheme $reportTheme)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'styles' => 'required|array',
            'styles.colors' => 'required|array',
            'styles.fonts' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // If this is set as default, remove default from other themes
            if ($request->boolean('is_default')) {
                ReportTheme::where('is_default', true)
                    ->where('id', '!=', $reportTheme->id)
                    ->update(['is_default' => false]);
            }

            $reportTheme->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'is_default' => $request->boolean('is_default'),
                'styles' => $request->styles,
            ]);

            DB::commit();

            return redirect()->route('report-themes.index')
                ->with('success', 'تم تحديث الثيم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(ReportTheme $reportTheme)
    {
        try {
            DB::beginTransaction();
            
            // Check if theme is being used by templates
            if ($reportTheme->printTemplates()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الثيم لأنه مستخدم في قوالب');
            }
            
            $reportTheme->delete();
            
            DB::commit();
            
            return redirect()->route('report-themes.index')
                ->with('success', 'تم حذف الثيم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function duplicate(ReportTheme $reportTheme)
    {
        try {
            DB::beginTransaction();
            
            $newTheme = $reportTheme->replicate();
            $newTheme->name = $reportTheme->name . ' (نسخة)';
            $newTheme->is_default = false;
            $newTheme->save();
            
            DB::commit();
            
            return redirect()->route('report-themes.index')
                ->with('success', 'تم نسخ الثيم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function preview(ReportTheme $reportTheme)
    {
        return view('report-themes.preview', compact('theme'));
    }
}
