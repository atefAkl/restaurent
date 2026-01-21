<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,webp,png,gif|max:2048',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name_ar.required' => 'اسم الفئة بالعربية مطلوب',
            'name_ar.max' => 'اسم الفئة بالعربية يجب ألا يتجاوز 255 حرف',
            'sort_order.integer' => 'ترتيب العرض يجب أن يكون رقماً صحيحاً',
            'sort_order.min' => 'ترتيب العرض يجب أن يكون 0 أو أكثر',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'الصيغ المسموح بها: jpeg, jpg, png, gif',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ];
    }
}
