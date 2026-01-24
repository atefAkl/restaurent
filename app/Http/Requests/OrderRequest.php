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
            'customer_id' => 'nullable|exists:customers,id',
            'address_id' => 'nullable|exists:addresses,id',
            'room_id' => 'nullable|exists:rooms,id',
            'type' => 'required|in:dine_in,takeaway,delivery,catering,subscription',
            'products' => 'required|array|min:1',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'prices' => 'required|array|min:1',
            'prices.*' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,on_account,subscription,mixed',
            'payment_reference' => 'nullable|string|max:255',
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
