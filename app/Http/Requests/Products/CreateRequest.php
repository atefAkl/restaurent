<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name_ar'           => 'required|string|max:255',
            'name_en'           => 'nullable|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            's_number'          => 'nullable|unique:products,s_number,' . $this->id,
            'price'             => 'required|numeric|min:0',
            'cost'              => 'nullable|numeric|min:0',
            'description_ar'    => 'nullable|string',
            'description_en'    => 'nullable|string',
            'barcode'           => 'nullable|string|max:255|unique:products,barcode,' . $this->product,
            'sku'               => 'nullable|string|max:255|unique:products,sku,' . $this->product,
            'min_stock_alert'   => 'nullable|integer|min:0',
            'image'             => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name_ar.required' => 'اسم المنتج بالعربية مطلوب',
            'name_ar.max' => 'اسم المنتج بالعربية يجب ألا يتجاوز 255 حرف',
            'category_id.required' => 'يجب اختيار الفئة',
            'category_id.exists' => 'الفئة المحددة غير موجودة',
            'price.required' => 'سعر المنتج مطلوب',
            'price.numeric' => 'السعر يجب أن يكون رقماً',
            'price.min' => 'السعر يجب أن يكون 0 أو أكثر',
            'cost.numeric' => 'التكلفة يجب أن تكون رقماً',
            'cost.min' => 'التكلفة يجب أن تكون 0 أو أكثر',
            'barcode.unique' => 'الباركود مستخدم من قبل',
            'sku.unique' => 'رمز المنتج مستخدم من قبل',
            'stock_quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً',
            'stock_quantity.min' => 'الكمية يجب أن تكون 0 أو أكثر',
            'min_stock_alert.integer' => 'حد التنبيه يجب أن يكون رقماً صحيحاً',
            'min_stock_alert.min' => 'حد التنبيه يجب أن يكون 0 أو أكثر',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'الصيغ المسموح بها: jpeg, jpg, png, gif',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ];
    }
}
