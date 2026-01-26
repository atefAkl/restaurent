<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => 'يجب قبول :attribute عندما يكون :other هو :value.',
    'active_url' => ':attribute ليس رابط صحيح.',
    'after' => 'يجب أن يكون :attribute بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute بعد أو يساوي :date.',
    'alpha' => 'يجب أن يحتوي :attribute على حروف فقط.',
    'alpha_dash' => 'يجب أن يحتوي :attribute على حروف، أرقام، وشرطات.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروف وأرقام فقط.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'ascii' => 'يجب أن يحتوي :attribute على حروف ASCII فقط.',
    'before' => 'يجب أن يكون :attribute قبل :date.',
    'before_or_equal' => 'يجب أن يكون :attribute قبل أو يساوي :date.',
    'between' => [
        'array' => 'يجب أن يحتوي :attribute على بين :min و :max عناصر.',
        'file' => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
        'string' => 'يجب أن يحتوي :attribute على بين :min و :max حرف.',
    ],
    'boolean' => 'يجب أن يكون :attribute صحيح أو خطأ.',
    'can' => 'حقل :attribute يحتوي على قيمة غير مصرح بها.',
    'confirmed' => 'تأكيد :attribute لا يتطابق.',
    'current_password' => 'كلمة المرور الحالية غير صحيحة.',
    'date' => ':attribute ليس تاريخ صحيح.',
    'date_equals' => 'يجب أن يكون :attribute تاريخ يساوي :date.',
    'date_format' => 'صيغة :attribute لا تتطابق مع :format.',
    'decimal' => 'يجب أن يحتوي :attribute على :decimal رقم عشري.',
    'declined' => 'يجب رفض :attribute.',
    'declined_if' => 'يجب رفض :attribute عندما يكون :other هو :value.',
    'different' => ':attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'يجب أن يحتوي :attribute على :digits أرقام.',
    'digits_between' => 'يجب أن يحتوي :attribute على بين :min و :max أرقام.',
    'dimensions' => 'أبعاد :attribute غير صالحة.',
    'distinct' => 'حقل :attribute مكرر.',
    'doesnt_end_with' => 'لا يمكن أن ينتهي :attribute بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'لا يمكن أن يبدأ :attribute بأحد القيم التالية: :values.',
    'email' => 'يجب أن يكون :attribute بريد إلكتروني صحيح.',
    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'enum' => 'حقل :attribute المحدد غير صالح.',
    'exists' => ':attribute المحدد غير صالح.',
    'file' => 'يجب أن يكون :attribute ملف.',
    'filled' => 'يجب ملء حقل :attribute.',
    'gt' => [
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر.',
        'file' => 'يجب أن يكون حجم :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute أكبر من :value.',
        'string' => 'يجب أن يحتوي :attribute على أكثر من :value حرف.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي :attribute على :value عناصر أو أكثر.',
        'file' => 'يجب أن يكون حجم :attribute على الأقل :value كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute أكبر من أو يساوي :value.',
        'string' => 'يجب أن يحتوي :attribute على :value حرف أو أكثر.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => 'حقل :attribute المحدد غير صالح.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عدد صحيح.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صحيح.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صحيح.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صحيح.',
    'json' => 'يجب أن يكون :attribute سلسلة JSON صحيحة.',
    'lowercase' => 'يجب أن يكون :attribute أحرف صغيرة.',
    'lt' => [
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر.',
        'file' => 'يجب أن يكون حجم :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute أقل من :value.',
        'string' => 'يجب أن يحتوي :attribute على أقل من :value حرف.',
    ],
    'lte' => [
        'array' => 'يجب أن يحتوي :attribute على :value عناصر أو أقل.',
        'file' => 'يجب أن يكون حجم :attribute على الأكثر :value كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute أقل من أو يساوي :value.',
        'string' => 'يجب أن يحتوي :attribute على :value حرف أو أقل.',
    ],
    'mac_address' => 'يجب أن يكون :attribute عنوان MAC صحيح.',
    'max' => [
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر.',
        'file' => 'يجب أن لا يتجاوز حجم :attribute :max كيلوبايت.',
        'numeric' => 'يجب أن لا يتجاوز :attribute :max.',
        'string' => 'يجب أن لا يتجاوز :attribute :max حرف.',
    ],
    'max_digits' => 'يجب أن لا يحتوي :attribute على أكثر من :max أرقام.',
    'mimes' => 'يجب أن يكون :attribute ملف من نوع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملف من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي :attribute على الأقل :min عناصر.',
        'file' => 'يجب أن يكون حجم :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute على الأقل :min.',
        'string' => 'يجب أن يحتوي :attribute على الأقل :min حرف.',
    ],
    'min_digits' => 'يجب أن يحتوي :attribute على الأقل :min أرقام.',
    'missing' => 'حقل :attribute يجب أن يكون مفقوداً.',
    'missing_if' => 'حقل :attribute يجب أن يكون مفقوداً عندما يكون :other هو :value.',
    'missing_unless' => 'حقل :attribute يجب أن يكون مفقوداً إلا إذا كان :other هو :value.',
    'missing_with' => 'حقل :attribute يجب أن يكون مفقوداً عندما يكون :present.',
    'missing_with_all' => 'حقل :attribute يجب أن يكون مفقوداً عندما يكون :present.',
    'multiple_of' => 'يجب أن يكون :attribute مضاعفاً من :value.',
    'not_in' => 'حقل :attribute المحدد غير صالح.',
    'not_regex' => 'صيغة :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون :attribute رقم.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون حقل :attribute موجوداً.',
    'prohibited' => 'حقل :attribute ممنوع.',
    'prohibited_if' => 'حقل :attribute ممنوع عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute ممنوع إلا إذا كان :other في :values.',
    'prohibits' => 'حقل :attribute يمنع وجود :other.',
    'regex' => 'صيغة :attribute غير صالحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'حقل :attribute يجب أن يحتوي على إدخالات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless' => 'حقل :attribute مطلوب إلا إذا كان :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما يكون :present.',
    'required_with_all' => 'حقل :attribute مطلوب عندما يكون :present.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يكون :present.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا يكون أي من :present.',
    'same' => ':attribute و :other يجب أن يكونا متطابقين.',
    'size' => [
        'array' => 'يجب أن يحتوي :attribute على :size عناصر.',
        'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute :size.',
        'string' => 'يجب أن يحتوي :attribute على :size حرف.',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون :attribute نص.',
    'timezone' => 'يجب أن يكون :attribute منطقة زمنية صالحة.',
    'unique' => 'قيمة :attribute مستخدمة من قبل.',
    'uploaded' => 'فشل رفع :attribute.',
    'uppercase' => 'يجب أن يكون :attribute أحرف كبيرة.',
    'url' => 'يجب أن يكون :attribute رابط صحيح.',
    'ulid' => 'يجب أن يكون :attribute ULID صالح.',
    'uuid' => 'يجب أن يكون :attribute UUID صالح.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'الاسم',
        'name_ar' => 'الاسم بالعربية',
        'name_en' => 'الاسم بالإنجليزية',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'phone' => 'رقم الهاتف',
        'address' => 'العنوان',
        'description' => 'الوصف',
        'description_ar' => 'الوصف بالعربية',
        'description_en' => 'الوصف بالإنجليزية',
        'price' => 'السعر',
        'cost' => 'التكلفة',
        'quantity' => 'الكمية',
        'stock_quantity' => 'الكمية في المخزون',
        'min_stock_alert' => 'حد التنبيه الأدنى',
        'category_id' => 'الفئة',
        'product_id' => 'المنتج',
        'order_id' => 'الطلب',
        'user_id' => 'المستخدم',
        'image' => 'الصورة',
        'barcode' => 'الباركود',
        'sku' => 'رمز المنتج',
        'sort_order' => 'ترتيب العرض',
        'is_active' => 'الحالة',
        'track_inventory' => 'تتبع المخزون',
        'is_seasonal' => 'منتج موسمي',
        'amount' => 'المبلغ',
        'expense_number' => 'رقم المصروف',
        'expense_category' => 'فئة المصروف',
        'expense_date' => 'تاريخ المصروف',
        'shift_number' => 'رقم الشيفت',
        'opening_balance' => 'الرصيد الافتتاحي',
        'closing_balance' => 'الرصيد الختامي',
        'cash_sales' => 'المبيعات النقدية',
        'visa_sales' => 'المبيعات بالبطاقة',
        'notes' => 'ملاحظات',
        'remember' => 'تذكرني',
        'role' => 'الدور',
    ],

];
