<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'expense_number' => 'required|string|max:50|unique:expenses,expense_number,' . $this->expense,
            'expense_category' => 'required|in:rent,salaries,utilities,supplies,maintenance,marketing,other',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'expense_number.required' => 'رقم المصروف مطلوب',
            'expense_number.max' => 'رقم المصروف يجب ألا يتجاوز 50 حرف',
            'expense_number.unique' => 'رقم المصروف مستخدم من قبل',
            'expense_category.required' => 'فئة المصروف مطلوبة',
            'expense_category.in' => 'فئة المصروف غير صالحة',
            'description.required' => 'وصف المصروف مطلوب',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
            'amount.required' => 'مبلغ المصروف مطلوب',
            'amount.numeric' => 'مبلغ المصروف يجب أن يكون رقماً',
            'amount.min' => 'مبلغ المصروف يجب أن يكون 0.01 ريال على الأقل',
            'expense_date.required' => 'تاريخ المصروف مطلوب',
            'expense_date.date' => 'تاريخ المصروف غير صالح',
            'receipt.mimes' => 'صيغة الإيصال يجب أن تكون: jpeg, jpg, png, pdf',
            'receipt.max' => 'حجم الإيصال يجب ألا يتجاوز 2 ميجابايت',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        ];
    }

    public function attributes()
    {
        return [
            'expense_number' => 'رقم المصروف',
            'expense_category' => 'فئة المصروف',
            'description' => 'وصف المصروف',
            'amount' => 'مبلغ المصروف',
            'expense_date' => 'تاريخ المصروف',
            'receipt' => 'إيصال المصروف',
            'notes' => 'ملاحظات',
        ];
    }
}
