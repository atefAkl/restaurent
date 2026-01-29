@extends('layouts.app')
@section('title', 'المتغيرات المتاحة للقوالب')
@section('content')
<div class="container">
    <h2 class="mb-4">المتغيرات المتاحة في القوالب</h2>
    <div class="alert alert-info">
        يمكنك استخدام المتغيرات التالية داخل محتوى القالب وسيتم استبدالها بالقيم الفعلية عند الطباعة.
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>المتغير</th>
                <th>الوصف</th>
                <th>مثال</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{order_number}</td>
                <td>رقم الطلب</td>
                <td>ORD-20260129-0001</td>
            </tr>
            <tr>
                <td>{date}</td>
                <td>تاريخ الطلب</td>
                <td>2026-01-29</td>
            </tr>
            <tr>
                <td>{time}</td>
                <td>وقت الطلب</td>
                <td>14:30</td>
            </tr>
            <tr>
                <td>{cashier}</td>
                <td>اسم الكاشير</td>
                <td>أحمد علي</td>
            </tr>
            <tr>
                <td>{customer_name}</td>
                <td>اسم العميل</td>
                <td>محمد</td>
            </tr>
            <tr>
                <td>{customer_phone}</td>
                <td>هاتف العميل</td>
                <td>0500000000</td>
            </tr>
            <tr>
                <td>{customer_address}</td>
                <td>عنوان العميل</td>
                <td>الرياض</td>
            </tr>
            <tr>
                <td>{order_type}</td>
                <td>نوع الطلب</td>
                <td>محلي/سفري/توصيل</td>
            </tr>
            <tr>
                <td>{room_number}</td>
                <td>رقم الطاولة/الغرفة</td>
                <td>5</td>
            </tr>
            <tr>
                <td>{subtotal}</td>
                <td>الإجمالي قبل الضريبة</td>
                <td>200.00</td>
            </tr>
            <tr>
                <td>{tax_amount}</td>
                <td>قيمة الضريبة</td>
                <td>30.00</td>
            </tr>
            <tr>
                <td>{discount_amount}</td>
                <td>قيمة الخصم</td>
                <td>10.00</td>
            </tr>
            <tr>
                <td>{total_amount}</td>
                <td>الإجمالي النهائي</td>
                <td>220.00</td>
            </tr>
            <tr>
                <td>{paid_amount}</td>
                <td>المدفوع</td>
                <td>100.00</td>
            </tr>
            <tr>
                <td>{remaining_amount}</td>
                <td>المتبقي</td>
                <td>120.00</td>
            </tr>
            <tr>
                <td>{payment_method}</td>
                <td>طريقة الدفع</td>
                <td>كاش/بطاقة/تحويل</td>
            </tr>
            <tr>
                <td>{notes}</td>
                <td>ملاحظات الطلب</td>
                <td>بدون بصل</td>
            </tr>
            <tr>
                <td>{items_table}</td>
                <td>جدول المنتجات (يتم توليده تلقائياً)</td>
                <td>جدول HTML</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection