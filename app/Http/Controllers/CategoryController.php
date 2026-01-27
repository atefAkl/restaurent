<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('activeProducts')
            ->when(request('search'), function ($query, $search) {
                $query->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            })
            ->withCount('products')
            ->orderBy('sort_order')
            ->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        try {
            $data = $request->except('image');

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('categories', $imageName, 'public');
                $data['image'] = 'categories/' . $imageName;
            }

            $data['is_active'] = $request->boolean('is_active');

            Category::create($data);

            return redirect()->back()->with('success', 'تم إضافة الفئة بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الفئة' . $e->getMessage());
        }
    }

    public function show(Category $category)
    {
        $category->load('activeProducts');
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        try {
            $data = $request->except('image');

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('categories', $imageName, 'public');
                $data['image'] = 'categories/' . $imageName;
            }

            $data['is_active'] = $request->boolean('is_active');

            $category->update($data);

            return redirect()->back()->with('success', 'تم تحديث الفئة بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الفئة' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            // Check if category has products
            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف الفئة لوجود منتجات مرتبطة بها'
                ], 400);
            }

            // Delete image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return redirect()->route('categories.index')->with('success', 'تم حذف الفئة بنجاح');
        } catch (Exception $e) {
            return redirect()->route('categories.index')->with('error', 'حدث خطأ أثناء حذف الفئة' . $e->getMessage());
        }
    }

    public function toggleStatus(Category $category)
    {
        $toggle = ['is_active' => !$category->is_active];
        try {
            $category->update($toggle);
            return redirect()->back()->with('success', $toggle['is_active'] ? 'تم تفعيل الفئة' : 'تم إلغاء تفعيل الفئة');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تبديل حالة الفئة' . $e->getMessage());
        }
    }

    public function getActiveCategories()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name_ar', 'name_en', 'image')
            ->get();

        return response()->json($categories);
    }
}
