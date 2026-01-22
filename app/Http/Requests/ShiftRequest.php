<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'shift_number' => 'required|string|max:50',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'shift_number.required' => 'رقم الوردية مطلوب',
            'shift_number.max' => 'رقم الوردية يجب ألا يتجاوز 50 حرف',
            'opening_balance.required' => 'الرصيد الافتتاحي مطلوب',
            'opening_balance.numeric' => 'الرصيد الافتتاحي يجب أن يكون رقماً',
            'opening_balance.min' => 'الرصيد الافتتاحي يجب أن يكون 0 أو أكثر',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        ];
    }
}
