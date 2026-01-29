@extends('layouts.print_template_edit')

@section('title', 'تخصيص قالب الطباعة')

@section('vars')
<h4 class="mb-4">خيارات القالب</h4>
<form id="customizeForm">
    <div class="mb-3">
        <label class="form-label">اختر نوع القالب</label>
        <select id="templateType" class="form-control">
            <option value="a4">فاتورة ضريبية (A4)</option>
            <option value="thermal">فاتورة حرارية (8 سم)</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">المكونات الظاهرة</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showCompany" checked>
            <label class="form-check-label">بيانات المنشأة</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showCustomer" checked>
            <label class="form-check-label">بيانات العميل</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showItems" checked>
            <label class="form-check-label">جدول المنتجات</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showTotals" checked>
            <label class="form-check-label">الإجماليات</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showNotes" checked>
            <label class="form-check-label">ملاحظات</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showQR" checked>
            <label class="form-check-label">رمز QR</label>
        </div>
    </div>
</form>
<div class="mt-3">
    <button id="copyHtmlBtn" class="btn btn-outline-primary">نسخ HTML الحالي</button>
    <a href="{{ route('print-templates.create') }}" class="btn btn-primary ms-2">إضافة قالب جديد</a>
    <button id="saveTemplateBtn" class="btn btn-success ms-2">حفظ كقالب</button>
</div>
@endsection

@section('preview')
<h4 class="mb-3">المعاينة المباشرة</h4>
<div id="previewArea" style="background:#fff; border:1px solid #ddd; padding:20px; min-height:400px;"></div>
@endsection

@section('scripts')
<script>
    // عينات بيانات
    const data = {
        order_number: 'ORD-20260129-0001',
        date: '2026-01-29',
        time: '14:30',
        cashier: 'أحمد علي',
        customer_name: 'محمد',
        customer_phone: '0500000000',
        customer_address: 'الرياض',
        order_type: 'محلي',
        room_number: '5',
        subtotal: '200.00',
        tax_amount: '30.00',
        discount_amount: '10.00',
        total_amount: '220.00',
        paid_amount: '100.00',
        remaining_amount: '120.00',
        payment_method: 'كاش',
        notes: 'بدون بصل',
        items_table: '<tr><td>1</td><td>وجبة 1</td><td>2</td><td>50</td><td>100</td></tr><tr><td>2</td><td>وجبة 2</td><td>1</td><td>100</td><td>100</td></tr>',
        qr_code: 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=شكراً'
    };

    // جلب القالب من السيرفر (بلايد) عبر AJAX
    async function fetchTemplate(type) {
        const res = await fetch(`/print-templates/default-template?type=${type}`);
        return await res.text();
    }

    // تحديث المعاينة عبر الخادم (POST preview)
    async function updatePreview() {
        const type = document.getElementById('templateType').value;
        let template = await fetchTemplate(type);
        // إظهار/إخفاء المكونات بطريقة مبسطة قبل الإرسال
        if (!document.getElementById('showCompany').checked) template = template.replace(/\bاسم المنشأة\b|<h3>اسم المنشأة<\/h3>/g, '');
        if (!document.getElementById('showCustomer').checked) template = template.replace(/اسم العميل:.*?<br>/g, '');
        if (!document.getElementById('showItems').checked) template = template.replace(/{items_table}/g, '');
        if (!document.getElementById('showTotals').checked) template = template.replace(/الإجمالي[\s\S]*?طريقة الدفع:.*?<br>/, '');
        if (!document.getElementById('showNotes').checked) template = template.replace(/ملاحظات:.*?(<br>|<\/div>)/, '');
        if (!document.getElementById('showQR').checked) template = template.replace(/<img[^>]*qr_code[^>]*>/g, '');

        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

        const res = await fetch('/print-templates/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                content: template,
                sampleData: data
            })
        });

        if (res.ok) {
            const html = await res.text();
            document.getElementById('previewArea').innerHTML = html;
        } else {
            document.getElementById('previewArea').innerText = 'فشل تحميل المعاينة من الخادم';
        }
    }

    document.querySelectorAll('#customizeForm input, #customizeForm select').forEach(el => {
        el.addEventListener('change', updatePreview);
    });

    // زر نسخ HTML المعروض
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'copyHtmlBtn') {
            const html = document.getElementById('previewArea').innerHTML;
            navigator.clipboard.writeText(html).then(() => {
                alert('تم نسخ HTML إلى الحافظة');
            }).catch(() => alert('فشل النسخ'));
        }
        if (e.target && e.target.id === 'saveTemplateBtn') {
            const html = document.getElementById('previewArea').innerHTML;
            const name = prompt('اسم القالب الذي تريد حفظه:');
            if (!name) return alert('إلغاء الحفظ: يجب إدخال اسم');
            const type = document.getElementById('templateType').value === 'a4' ? 'invoice' : 'order';
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            fetch('/print-templates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    name: name,
                    type: type,
                    content: html,
                    is_active: true
                })
            }).then(r => {
                if (r.redirected) {
                    window.location = r.url;
                } else if (r.ok) {
                    alert('تم حفظ القالب بنجاح');
                } else {
                    r.text().then(t => alert('فشل الحفظ: ' + t));
                }
            }).catch(err => alert('فشل الطلب: ' + err.message));
        }
    });

    // تحميل أولي
    updatePreview();
</script>
@endsection
*** End Patch