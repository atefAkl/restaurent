<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->when(request('search'), function ($query) {
                $query->where('name_ar', 'like', '%' . request('search') . '%')
                    ->orWhere('name_en', 'like', '%' . request('search') . '%')
                    ->orWhere('barcode', 'like', '%' . request('search') . '%')
                    ->orWhere('sku', 'like', '%' . request('search') . '%');
            })
            ->when(request('category_id'), function ($query) {
                $query->where('category_id', request('category_id'));
            })
            ->when(request('status'), function ($query) {
                $query->where('is_active', request('status') === '1');
            })
            ->orderBy('s_number', 'asc')
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
        try {
            $data = $request->validated();

            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image          = $request->file('image');
                $imageName      = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $imageName, 'public');
                $data['image']  = 'products/' . $imageName;
            }

            // Set s_number if not provided
            if (empty($request->s_number)) {
                $lastProduct = Product::orderBy('id', 'desc')->first();
                $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
                $data['s_number'] = str_pad($nextId, 5, '0', STR_PAD_LEFT);
            } else {
                $data['s_number'] = $request->s_number;
            }

            // Set default values and handle booleans
            $data['track_inventory'] = $request->boolean('track_inventory');
            $data['is_active']       = $request->boolean('is_active', true);
            $data['is_seasonal']     = $request->boolean('is_seasonal');
            $data['is_featured']     = $request->boolean('is_featured');

            $data['stock_quantity']  = $request->input('stock_quantity', 0);
            $data['min_stock_alert'] = $request->input('min_stock_alert', 10);

            Product::create($data);
            DB::commit();

            return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة المنتج: ' . $e->getMessage())->withInput();
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

    public function update(UpdateProductRequest $request, Product $product)
    {
        //return $request->all();
        try {
            $data = $request->validated();

            DB::beginTransaction();

            // Set boolean values
            $data['track_inventory'] = $request->boolean('track_inventory');
            $data['is_seasonal']     = $request->boolean('is_seasonal');
            $data['is_featured']     = $request->boolean('is_featured');

            // Set values for numbers
            $data['min_stock_alert'] = $request->input('min_stock_alert', $product->min_stock_alert);

            $product->update($data);
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث المنتج بنجاح'
                ]);
            }

            return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage()
                ], 400);
            }
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Check if product has related orders
            $hasOrders = $product->orderItems()->exists();

            if ($hasOrders) {
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
                'message' => 'حدث خطأ أثناء حذف المنتج: ' . $e->getMessage()
            ], 400);
        }
    }

    public function toggleStatus(Product $product)
    {
        try {
            $product->is_active = !$product->is_active;
            $product->save();
            return redirect()->back()->with(['success' => 'Status has been switched']);
        } catch (Exception $err) {
            return redirect()->back()->with(['error' => 'Status has not been switched due to ' . $err->getMessage()]);
        }
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
