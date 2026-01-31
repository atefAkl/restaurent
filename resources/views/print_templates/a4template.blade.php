<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة ضريبية - Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Arabic', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        @media print {
            body { background-color: white; padding: 0; }
            .no-print { display: none; }
            .invoice-container { 
                box-shadow: none !important; 
                margin: 0 !important; 
                width: 100% !important;
                padding: 10mm !important;
            }
        }
        .invoice-container {
            background: white;
            max-width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border: 1px solid #e2e8f0;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th { 
            background-color: #f1f5f9; 
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 0.75rem;
            text-align: center;
        }
        td { 
            border: 1px solid #e2e8f0; 
            padding: 10px 8px; 
            font-size: 0.85rem; 
            text-align: center;
        }
        .label-cell { background-color: #f8fafc; font-weight: bold; width: 150px; text-align: right; }
        .value-cell { text-align: right; }
    </style>
</head>
<body>

    <div class="no-print flex justify-center py-4 gap-4">
        <button onclick="window.print()" class="bg-slate-800 text-white px-6 py-2 rounded shadow hover:bg-slate-900 transition flex items-center gap-2">
            <span>طباعة الفاتورة</span>
        </button>
    </div>

    <div class="invoice-container">
        <!-- Header Section -->
        <div class="flex justify-between items-start mb-8 border-b pb-6">
            <div class="text-right">
                <h1 class="text-3xl font-bold text-slate-800">کوداکستریم</h1>
                <p class="text-sm text-gray-500">المملكة العربية السعودية</p>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-tighter">Kingdom of Saudi Arabia</p>
            </div>
            <div class="text-left">
                <h2 class="text-2xl font-bold text-slate-700">Invoice فاتورة</h2>
            </div>
        </div>

        <!-- Details Grid (Client & Invoice Info) -->
        <div class="grid grid-cols-2 gap-x-12 gap-y-1 mb-8">
            <!-- Client Info Column -->
            <div class="space-y-0.5">
                <div class="flex border-b border-gray-100 py-1">
                    <span class="w-32 text-xs text-gray-500">العميل Customer</span>
                    <span class="font-bold text-sm">على محمد علي</span>
                </div>
                <div class="flex border-b border-gray-100 py-1">
                    <span class="w-32 text-xs text-gray-500">العنوان Address</span>
                    <span class="text-sm">Kingdom of Saudi Arabia</span>
                </div>
                <div class="flex border-b border-gray-100 py-1">
                    <span class="w-32 text-xs text-gray-500">رقم التسجيل الضريبي VAT number</span>
                    <span class="text-sm font-mono">30012456780003</span>
                </div>
            </div>

            <!-- Invoice Info Column -->
            <div class="space-y-0.5">
                <div class="flex border-b border-gray-100 py-1">
                    <span class="w-40 text-xs text-gray-500">رقم الفاتورة Invoice number</span>
                    <span class="font-bold text-sm">INV-000100</span>
                </div>
                <div class="flex border-b border-gray-100 py-1">
                    <span class="w-40 text-xs text-gray-500">التاريخ Date</span>
                    <span class="text-sm">2026-01-30</span>
                </div>
                <div class="flex border-b border-gray-100 py-1">
                    <span class="w-40 text-xs text-gray-500">تاريخ الاستحقاق Due date</span>
                    <span class="text-sm font-medium text-red-600">2026-01-30</span>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <table>
            <thead>
                <tr>
                    <th class="w-10">#</th>
                    <th class="text-right">الوصف Description</th>
                    <th>الكمية Qty</th>
                    <th>السعر Price</th>
                    <th>المبلغ الخاضع للضريبة Taxable amount</th>
                    <th>القيمة المضافة VAT amount</th>
                    <th>المجموع Line amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td class="text-right">
                        <div class="font-medium text-slate-900">منتج جديد رقم 1</div>
                        <div class="text-[10px] text-gray-400">منتج جديد للتجربة</div>
                    </td>
                    <td>5.00</td>
                    <td>15.00</td>
                    <td>75.00</td>
                    <td>
                        <div>11.25</div>
                        <div class="text-[9px] text-gray-400 font-bold">15%</div>
                    </td>
                    <td class="font-bold">86.25</td>
                </tr>
                <!-- Empty rows for spacing to mimic typical layouts -->
                <tr class="h-12"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                <tr class="h-12"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
        </table>

        <!-- Summary & QR Code Section -->
        <div class="mt-8 flex justify-between items-start">
            <!-- QR Code and ZATCA text -->
            <div class="flex flex-col items-center gap-2">
                <div class="p-2 border border-slate-200 rounded bg-white">
                    <!-- Placeholder QR (Simulated as per your PDF layout) -->
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5">
                        <path d="M3 3h4v4H3zM17 3h4v4h-4zM17 17h4v4h-4zM3 17h4v4H3zM9 3h2M13 3h2M3 9v2M3 13v2M21 9v2M21 13v2M9 21h2M13 21h2M9 9h6v6H9z" />
                    </svg>
                </div>
                <div class="max-w-[200px] text-center">
                    <p class="text-[9px] text-gray-500 leading-tight">تم ترميز هذا الرمز وفقاً لمتطلبات هيئة الزكاة والضريبة والجمارك للفوترة الإلكترونية</p>
                    <p class="text-[8px] text-gray-400 leading-tight mt-1">This QR code is encoded as per ZATCA e-invoicing requirements</p>
                </div>
            </div>

            <!-- Totals Column -->
            <div class="w-72">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">المجموع الفرعي Subtotal</span>
                    <span class="text-sm font-medium">75.00 ر.س</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">إجمالي الضريبة VAT Total</span>
                    <span class="text-sm font-medium">11.25 ر.س</span>
                </div>
                <div class="flex justify-between py-3 bg-slate-50 px-2 mt-2 rounded">
                    <span class="text-base font-bold text-slate-800">إجمالي المجموع Total</span>
                    <span class="text-lg font-bold text-slate-900">86.25 ر.س</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-10 left-0 right-0 px-[20mm] flex justify-between items-end text-xs text-gray-400">
            <div>
                <p>کوداکستریم</p>
                <p>رقم الفاتورة: INV-000100</p>
            </div>
            <div class="text-left">
                <p>Page 1 of 1</p>
                <p dir="ltr">www.codextreme.com</p>
            </div>
        </div>
    </div>

</body>
</html>