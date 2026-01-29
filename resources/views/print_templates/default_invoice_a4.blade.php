<div style="width: 21cm; min-height: 29.7cm; margin: auto; padding: 2cm; font-family: Arial, sans-serif; font-size: 14px; background: #fff; border: 1px solid #eee;">
    <div style="text-align:center;">
        <h2>فاتورة ضريبية</h2>
        <h3>اسم المنشأة</h3>
        <p>العنوان: ... | الرقم الضريبي: ... | الهاتف: ...</p>
    </div>
    <hr>
    <div style="margin-bottom: 1em;">
        <strong>رقم الفاتورة:</strong> {order_number}<br>
        <strong>التاريخ:</strong> {date} {time}<br>
        <strong>الكاشير:</strong> {cashier}<br>
        <strong>اسم العميل:</strong> {customer_name}<br>
        <strong>هاتف العميل:</strong> {customer_phone}<br>
        <strong>العنوان:</strong> {customer_address}<br>
    </div>
    <table width="100%" border="1" style="border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="background:#f5f5f5;">
                <th>#</th>
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
    <div style="margin-top:1em;">
        <strong>الإجمالي قبل الضريبة:</strong> {subtotal}<br>
        <strong>الضريبة:</strong> {tax_amount}<br>
        <strong>الخصم:</strong> {discount_amount}<br>
        <strong>الإجمالي النهائي:</strong> {total_amount}<br>
        <strong>المدفوع:</strong> {paid_amount}<br>
        <strong>المتبقي:</strong> {remaining_amount}<br>
        <strong>طريقة الدفع:</strong> {payment_method}<br>
    </div>
    <div style="margin-top:1em;">
        <strong>ملاحظات:</strong> {notes}
    </div>
    <div style="margin-top:2em; text-align:center;">
        <img src="{qr_code}" alt="QR Code" style="width:120px;">
        <p>شكراً لزيارتكم</p>
    </div>
</div>