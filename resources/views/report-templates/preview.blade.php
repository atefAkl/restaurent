@extends('layouts.order')
@section('title', 'معاينة القالب')

@section('content')
<style>
    .preview-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: white;
        border: 1px solid #ddd;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .preview-header {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #eee;
    }
    
    .preview-footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        text-align: center;
        font-size: 12px;
        color: #666;
    }
    
    .template-info {
        background: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        .preview-container {
            border: none;
            box-shadow: none;
            margin: 0;
            padding: 0;
        }
    }
</style>

<div class="container-fluid py-3">
    <!-- Preview Controls -->
    <div class="card mb-3 no-print">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">معاينة القالب: {{ $template->name }}</h5>
                    <p class="text-muted mb-0">نوع: {{ $template->type }}</p>
                </div>
                <div class="btn-group">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bi bi-printer me-2"></i>طباعة
                    </button>
                    <a href="{{ route('report-templates.show', $template) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-2"></i>رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Info -->
    <div class="template-info no-print">
        <h6>معلومات القالب:</h6>
        <div class="row">
            <div class="col-md-3"><strong>الاسم:</strong> {{ $template->name }}</div>
            <div class="col-md-3"><strong>النوع:</strong> {{ $template->type }}</div>
            <div class="col-md-3"><strong>الثيم:</strong> {{ $template->theme ? $template->theme->name : 'بدون ثيم' }}</div>
            <div class="col-md-3"><strong>البلوكات:</strong> {{ $template->templateBlocks->count() }}</div>
        </div>
    </div>

    <!-- Preview Content -->
    <div class="preview-container">
        @if($template->theme)
        <style>
            {{ $template->theme->generateCSS() }}
        </style>
        @endif
        
        {!! $content !!}
        
        <div class="preview-footer">
            <p>هذه معاينة للقالب "{{ $template->name }}"</p>
            <small>تم إنشاؤها في {{ now()->format('Y-m-d H:i:s') }}</small>
        </div>
    </div>
</div>
@endsection
