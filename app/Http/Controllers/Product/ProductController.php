<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * عرض قائمة المنتجات
     */
    public function index()
    {
        $products = Product::latest()->paginate(15);
        return view('products.index', compact('products'));
    }

    /**
     * عرض صفحة إضافة منتج جديد
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * حفظ منتج جديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price', // لازم يكون أكبر من السعر
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'is_active' => 'boolean'
        ]);

        // 2. رفع الصورة على Cloudinary (لو موجودة)
        $imageUrl = null;
        $imagePublicId = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // توليد اسم فريد للملف
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('product_') . '_' . Str::slug($originalName) . '.' . $extension;
            
            // رفع الصورة في مجلد 'products' على Cloudinary
            $path = Storage::disk('cloudinary')->putFileAs('products', $file, $filename);
            
            // الحصول على الرابط
            $imageUrl = Storage::disk('cloudinary')->url($path);
            $imagePublicId = $path; // public_id اللي هتحتاجه للتحكم في الصورة
        }

        // 3. حفظ البيانات في قاعدة البيانات
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'quantity' => $validated['quantity'],
            'image' => $imageUrl,
            'image_public_id' => $imagePublicId,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // 4. رسالة نجاح وإعادة توجيه
        return redirect()
            ->route('products.index')
            ->with('success', 'تم إضافة المنتج "' . $product->name . '" بنجاح!');
    }

    /**
     * عرض تفاصيل منتج معين
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * عرض صفحة تعديل المنتج
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * تحديث بيانات المنتج
     */
    public function update(Request $request, Product $product)
    {
        // 1. التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean'
        ]);

        // 2. رفع الصورة الجديدة (لو موجودة)
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة من Cloudinary
            if ($product->image_public_id) {
                Storage::disk('cloudinary')->delete($product->image_public_id);
            }

            $file = $request->file('image');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid('product_') . '_' . Str::slug($originalName) . '.' . $extension;
            
            $path = Storage::disk('cloudinary')->putFileAs('products', $file, $filename);
            $imageUrl = Storage::disk('cloudinary')->url($path);
            $imagePublicId = $path;
        } else {
            // الاحتفاظ بالصورة القديمة
            $imageUrl = $product->image;
            $imagePublicId = $product->image_public_id;
        }

        // 3. تحديث البيانات
        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'quantity' => $validated['quantity'],
            'image' => $imageUrl,
            'image_public_id' => $imagePublicId,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'تم تحديث المنتج "' . $product->name . '" بنجاح!');
    }

    /**
     * حذف المنتج
     */
    public function destroy(Product $product)
    {
        // حذف الصورة من Cloudinary
        if ($product->image_public_id) {
            Storage::disk('cloudinary')->delete($product->image_public_id);
        }

        $productName = $product->name;
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'تم حذف المنتج "' . $productName . '" بنجاح!');
    }

    /**
     * (اختياري) تغيير حالة المنتج (نشط/غير نشط) من خلال AJAX
     */
    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return response()->json([
            'success' => true,
            'is_active' => $product->is_active,
            'message' => 'تم تغيير حالة المنتج بنجاح'
        ]);
    }
}