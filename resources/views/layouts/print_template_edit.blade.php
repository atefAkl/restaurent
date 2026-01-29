<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'تعديل قالب الطباعة')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .edit-layout-main {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }

        .edit-layout-vars {
            width: 32%;
            min-width: 320px;
            background: #fff;
            border-left: 1px solid #eee;
            padding: 2rem 1.5rem;
            overflow-y: auto;
        }

        .edit-layout-preview {
            flex: 1;
            background: #f9f9f9;
            padding: 2rem 1.5rem;
            overflow-y: auto;
        }

        @media (max-width: 900px) {
            .edit-layout-main {
                flex-direction: column;
            }

            .edit-layout-vars,
            .edit-layout-preview {
                width: 100%;
                min-width: unset;
                padding: 1rem;
            }
        }
    </style>
    @yield('head')
</head>

<body>
    <div class="edit-layout-main">
        <div class="edit-layout-vars">
            @yield('vars')
        </div>
        <div class="edit-layout-preview">
            @yield('preview')
        </div>
    </div>
    @yield('scripts')
</body>

</html>