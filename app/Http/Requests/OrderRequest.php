<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'products.*' => 'required|exists:products,id',
            'quantities.*' => 'required|integer|min:1',
            'prices.*' => 'required|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'customer_name.required' => 'اسم العميل مطلوب',
            'customer_name.max' => 'اسم العميل يجب ألا يتجاوز 255 حرف',
            'customer_phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 رقم',
            'customer_address.max' => 'العنوان يجب ألا يتجاوز 500 حرف',
            'order_type.required' => 'نوع الطلب مطلوب',
            'order_type.in' => 'نوع الطلب غير صالح',
            'products.*.required' => 'يجب اختيار منتج واحد على الأقل',
            'products.*.exists' => 'المنتج المحدد غير موجود',
            'quantities.*.required' => 'الكمية مطلوبة',
            'quantities.*.integer' => 'الكمية يجب أن تكون رقماً صحيحاً',
            'quantities.*.min' => 'الكمية يجب أن تكون 1 على الأقل',
            'prices.*.required' => 'السعر مطلوب',
            'prices.*.numeric' => 'السعر يجب أن يكون رقماً',
            'prices.*.min' => 'السعر يجب أن يكون 0 أو أكثر',
            'delivery_fee.numeric' => 'رسوم التوصيل يجب أن تكون رقماً',
            'delivery_fee.min' => 'رسوم التوصيل يجب أن تكون 0 أو أكثر',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        ];
    }
}
