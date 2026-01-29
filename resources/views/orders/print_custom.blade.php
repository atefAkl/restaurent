<!DOCTYPE html>
<html dir="{{ session('locale') == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة الطلب</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 15px;
                font-family: 'Courier New', Arial, sans-serif;
                font-size: 12px;
            }

            .no-print {
                display: none !important;
            }
        }

        @media screen {
            body {
                margin: 20px;
                padding: 20px;
                font-family: 'Courier New', Arial, sans-serif;
                font-size: 12px;
                background: #f5f5f5;
            }

            .print-container {
                background: white;
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        {!! $html !!}
    </div>
    <div class="no-print" style="text-align: center; margin: 20px 0;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">طباعة</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">إغلاق</button>
    </div>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>
</body>

</html>