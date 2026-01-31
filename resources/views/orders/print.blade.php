@extends('layouts.app')
@section('title', 'طباعة')

@section('content')
<div class="container-fluid">
    <style>
        /* Print: hide all page chrome except the printArea */
        @page { size: A4 portrait; margin: 8mm; }

        @media print {
            /* Hide everything */
            body * {
                visibility: hidden !important;
            }

            /* Except the print area and its children */
            #printArea, #printArea * {
                visibility: visible !important;
            }

            /* Position the print area at the top-left for printing and fit to A4 */
            html, body {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
            }

            #printArea {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 210mm !important;
                /* Allow content to determine height to avoid clipping */
                max-height: 297mm !important;
                box-sizing: border-box !important;
                overflow: visible !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                page-break-inside: avoid !important;
                padding: 8mm !important;
                background: white !important;
            }

            /* Remove shadows/borders for clean print */
            .card, .card-body {
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
            }

            /* Hide interactive controls explicitly */
            .no-print, a, button { display: none !important; }
        }

        /* Helper class to hide elements in all prints */
        .no-print { display: none; }

        /* A wrapper that can be scaled to fit the page if needed */
        .print-scale {
            transform-origin: top left;
            width: 100%;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h5>طباعة الطلب: {{ $order->order_number }}</h5>
                </div>
                <div>
                    <a href="javascript:window.print()" class="btn btn-primary">طباعة</a>
                </div>
            </div>

            <div id="printArea">
                <div class="print-scale" id="printScale">
                    @if(!empty($content))
                        {{-- Render generated HTML from template (already contains full HTML or fragment) --}}
                        {!! $content !!}
                    @else
                        {{-- Fallback to static blade file which expects $order --}}
                        @include('print_templates.a4template')
                    @endif
                </div>
            </div>
            <script>
                // If this window was opened as part of the save-and-print flow (name 'printWindow'), auto-trigger print.
                document.addEventListener('DOMContentLoaded', function() {
                    try {
                        // Scale content to fit page if needed
                        fitPrintToPage();

                        if (window.name === 'printWindow') {
                            // Give a small delay to ensure content/styles load and scaling applied
                            setTimeout(function() {
                                window.print();
                            }, 350);
                        }
                    } catch (e) {
                        console.error('Auto-print failed', e);
                    }
                });

                // Scale #printScale to fit A4 page if it overflows
                function fitPrintToPage() {
                    try {
                        const scaleWrapper = document.getElementById('printScale');
                        if (!scaleWrapper) return;

                        // Available print dimensions (in pixels) based on mm->px approx at 96dpi
                        // 1mm ~ 3.78px at 96dpi. We'll compute based on page size and margins.
                        const mmToPx = mm => mm * 3.78;
                        const pageWidthPx = mmToPx(210 - 16); // subtract margins (8mm each side)
                        const pageHeightPx = mmToPx(297 - 16);

                        // Measure content size
                        const rect = scaleWrapper.getBoundingClientRect();
                        const contentWidth = rect.width;
                        const contentHeight = rect.height;

                        // Compute required scale
                        const scaleX = pageWidthPx / contentWidth;
                        const scaleY = pageHeightPx / contentHeight;
                        const scale = Math.min(1, scaleX, scaleY);

                        scaleWrapper.style.transform = 'scale(' + scale + ')';
                    } catch (err) {
                        console.error('fitPrintToPage error', err);
                    }
                }
            </script>
        </div>
    </div>
</div>
@endsection
