@extends('layouts.app')
@section('title', 'إظهار/إخفاء أجزاء القالب')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">إدارة أجزاء القالب: {{ $template->name }}</h5>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('report-templates.blocks.update', $template) }}">
                @csrf
                <div class="mb-3">
                    <p>اختر الأجزاء التي تريد إظهارها في هذا القالب:</p>
                    <div class="list-group">
                        @foreach($template->templateBlocks as $block)
                        <label class="list-group-item d-flex align-items-center">
                            <input type="checkbox" name="visible[]" value="{{ $block->id }}" {{ $block->is_visible ? 'checked' : '' }} class="form-check-input me-2">
                            <div>
                                <strong>{{ $block->name }}</strong>
                                <div class="text-muted small">نوع: {{ $block->type }} — مفتاح: {{ $block->key ?? '-' }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">حفظ الحالة</button>
                    <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">العودة</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
