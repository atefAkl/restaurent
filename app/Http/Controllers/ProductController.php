<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->when(request('search'), function ($query, $search) {
                $query->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when(request('category_id'), function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when(request('is_active'), function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->latest()
            ->paginate(20);

        $categories = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_seasonal' => 'boolean'
        ]);

        try {
            $data = $request->except('image');

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $imageName, 'public');
                $data['image'] = 'products/' . $imageName;
            }

            // Set boolean values
            $data['track_inventory'] = $request->boolean('track_inventory');
            $data['is_active'] = $request->boolean('is_active');
            $data['is_seasonal'] = $request->boolean('is_seasonal');

            Product::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المنتج بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المنتج'
            ], 400);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'inventoryTransactions' => function ($query) {
            $query->with('user')->latest()->take(20);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'track_inventory' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_seasonal' => 'boolean'
        ]);

        try {
            $data = $request->except('image');

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $imageName, 'public');
                $data['image'] = 'products/' . $imageName;
            }

            // Set boolean values
            $data['track_inventory'] = $request->boolean('track_inventory');
            $data['is_active'] = $request->boolean('is_active');
            $data['is_seasonal'] = $request->boolean('is_seasonal');

            $product->update($data);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المنتج بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المنتج'
            ], 400);
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Check if product has orders
            if ($product->orderItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف المنتج لوجود طلبات مرتبطة به'
                ], 400);
            }

            // Delete image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج بنجاح'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج'
            ], 400);
        }
    }

    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => $product->is_active ? 'تم تفعيل المنتج' : 'تم إلغاء تفعيل المنتج'
        ]);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();

        return response()->json($products);
    }

    public function lowStock()
    {
        $products = Product::where('track_inventory', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->with('category')
            ->get();

        return view('products.low-stock', compact('products'));
    }
}
