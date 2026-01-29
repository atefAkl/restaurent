@props([
'data' => null,
'showInfo' => true,
'size' => 'default', // sm, default, lg
'align' => 'center' // start, center, end
])

@if($data && $data->hasPages())
<div class="pagination-wrapper {{ $size == 'sm' ? 'pagination-sm' : ($size == 'lg' ? 'pagination-lg' : '') }}">
    @if($showInfo)
    <div class="pagination-info d-flex justify-content-between align-items-center mb-3">
        <div class="pagination-text text-muted">
            <i class="bi bi-list-ul me-2"></i>
            عرض
            <span class="fw-semibold text-primary">{{ $data->firstItem() }}</span>
            إلى
            <span class="fw-semibold text-primary">{{ $data->lastItem() }}</span>
            من
            <span class="fw-semibold">{{ $data->total() }}</span>
            مكون
        </div>
        <div class="pagination-per-page">
            <div class="btn-group btn-group-sm" role="group">
                <a href="?per_page=12" class="btn btn-outline-secondary">12</a>
                <a href="?per_page=24" class="btn btn-outline-secondary">24</a>
                <a href="?per_page=48" class="btn btn-outline-secondary">48</a>
            </div>
        </div>
    </div>
    @endif

    <nav aria-label="Page navigation">
        <ul class="pagination {{ $align == 'start' ? 'justify-content-start' : ($align == 'end' ? 'justify-content-end' : 'justify-content-center') }} flex-wrap">
            <!-- Previous Button -->
            @if($data->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-right"></i>
                    <span class="d-none d-sm-inline ms-1">السابق</span>
                </span>
            </li>
            @else
            <li class="page-item">
                <a class="page-link" href="{{ $data->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-right"></i>
                    <span class="d-none d-sm-inline ms-1">السابق</span>
                </a>
            </li>
            @endif

            <!-- First Page -->
            @if($data->currentPage() > 3)
            <li class="page-item">
                <a class="page-link" href="{{ $data->url(1) }}">1</a>
            </li>

            @if($data->currentPage() > 4)
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
            @endif
            @endif

            <!-- Page Numbers -->
            @php
            $start = max(1, $data->currentPage() - 2);
            $end = min($data->lastPage(), $data->currentPage() + 2);
            @endphp

            @for($i = $start; $i <= $end; $i++)
                @if($i==$data->currentPage())
                <li class="page-item active" aria-current="page">
                    <span class="page-link">
                        {{ $i }}
                        <span class="sr-only">(current)</span>
                    </span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link" href="{{ $data->url($i) }}">{{ $i }}</a>
                </li>
                @endif
                @endfor

                <!-- Last Page -->
                @if($data->currentPage() < $data->lastPage() - 2)
                    @if($data->currentPage() < $data->lastPage() - 3)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        @endif

                        <li class="page-item">
                            <a class="page-link" href="{{ $data->url($data->lastPage()) }}">{{ $data->lastPage() }}</a>
                        </li>
                        @endif

                        <!-- Next Button -->
                        @if($data->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $data->nextPageUrl() }}" rel="next">
                                <span class="d-none d-sm-inline me-1">التالي</span>
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <span class="d-none d-sm-inline me-1">التالي</span>
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        </li>
                        @endif
        </ul>
    </nav>

    @if($showInfo)
    <div class="pagination-jump mt-3">
        <div class="d-flex justify-content-center align-items-center gap-2">
            <span class="text-muted small">الانتقال إلى صفحة:</span>
            <input type="number"
                class="form-control form-control-sm"
                style="width: 80px;"
                min="1"
                max="{{ $data->lastPage() }}"
                value="{{ $data->currentPage() }}"
                id="pageJumpInput">
            <button class="btn btn-sm btn-outline-primary" onclick="jumpToPage()">
                <i class="bi bi-box-arrow-up-right"></i>
            </button>
        </div>
    </div>
    @endif
</div>

<style>
    .pagination-wrapper {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .pagination-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .pagination-text {
        font-size: 0.9rem;
    }

    .pagination-per-page .btn-group .btn {
        border-color: #dee2e6;
        background: white;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .pagination-per-page .btn-group .btn:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        border: 1px solid #dee2e6;
        color: #007bff;
        background: white;
        padding: 4px 10px;
        margin: 0 2px;
        border-radius: 0px;
        transition: all 0.2s ease;
        font-weight: 500;
        min-width: 40px;
        text-align: center;
    }

    [dir="rtl"] .pagination .page-link {
        border-radius: 0 !important;
    }

    [dir="rtl"] .pagination .page-link:first-of-type {
        border-top-right-radius: 6px !important;
        border-bottom-right-radius: 6px !important;
    }

    [dir="rtl"] .page-link:last-of-type {
        border-top-left-radius: 6px !important;
        border-bottom-left-radius: 6px !important;
    }

    .page-link:hover {
        background: #e3f2fd;
        border-color: #007bff;
        color: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-color: #007bff;
        color: white;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }

    .page-item.disabled .page-link {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
        cursor: not-allowed;
    }

    .pagination-sm .page-link {
        padding: 6px 10px;
        font-size: 0.875rem;
        min-width: 32px;
    }

    .pagination-lg .page-link {
        padding: 12px 16px;
        font-size: 1.1rem;
        min-width: 48px;
    }

    .pagination-jump {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    #pageJumpInput {
        text-align: center;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    #pageJumpInput:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* RTL Support */
    [dir="rtl"] .page-link {
        margin: 0 2px;
    }

    [dir="rtl"] .pagination-per-page .btn-group .btn {
        margin-left: -1px;
    }

    [dir="rtl"] .pagination-per-page .btn-group .btn:first-child {
        margin-left: 0;
    }

    [dir="rtl"] .pagination-per-page .btn-group .btn:last-child {
        margin-left: 0;
    }

    /* Hover effects */
    .pagination-wrapper:hover .page-link {
        transition: all 0.2s ease;
    }

    /* Loading state */
    .pagination.loading .page-link {
        opacity: 0.6;
        pointer-events: none;
    }

    .pagination.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    function changePerPage(perPage) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function jumpToPage() {
        const input = document.getElementById('pageJumpInput');
        const page = parseInt(input.value);
        const maxPage = {
            {
                $data - > lastPage()
            }
        };

        if (page >= 1 && page <= maxPage) {
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        } else {
            input.classList.add('is-invalid');
            setTimeout(() => input.classList.remove('is-invalid'), 2000);
        }
    }

    // Enter key support for jump input
    document.getElementById('pageJumpInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            jumpToPage();
        }
    });
</script>
@endif