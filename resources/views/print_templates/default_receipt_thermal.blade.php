<div style="width: 8cm; min-height: 10cm; margin: auto; font-family: 'Courier New', Arial, sans-serif; font-size: 11px; background: #fff; border: 1px solid #eee; padding: 0.5cm;">
    <div style="text-align:center;">
        <h3 style="margin:0;">اسم المنشأة</h3>
        <p style="margin:0;">العنوان: ...</p>
        <p style="margin:0;">الهاتف: ...</p>
    </div>
    <hr>
    <div>
        رقم الطلب: {order_number}<br>
        التاريخ: {date} {time}<br>
        الكاشير: {cashier}<br>
        اسم العميل: {customer_name}<br>
    </div>
    <hr>
    <table width="100%" style="font-size:10px;">
        <thead>
            <tr>
                <th>الصنف</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            {items_table}
        </tbody>
    </table>
    <hr>
    <div>
        الإجمالي: {subtotal}<br>
        الضريبة: {tax_amount}<br>
        الإجمالي النهائي: {total_amount}<br>
        المدفوع: {paid_amount}<br>
        المتبقي: {remaining_amount}<br>
        طريقة الدفع: {payment_method}<br>
    </div>
    <div style="text-align:center; margin-top:1em;">
        <img src="{qr_code}" alt="QR Code" style="width:80px;">
        <p>شكراً لزيارتكم</p>
    </div>
</div>