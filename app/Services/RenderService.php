<?php

namespace App\Services;

use App\Models\PrintTemplate;
use App\Models\TemplateBlock;
use App\Models\Order;
use Illuminate\Support\Facades\Blade;

class RenderService
{
    /**
     * Render a draft content (string) with sample data.
     * Supports placeholders like {order_number} and block tokens {block:header}
     */
    public static function renderDraft(string $content, array $data = []): string
    {
        $html = $content;

        // Expand block tokens {block:key}
        $html = preg_replace_callback('/\{block:([a-zA-Z0-9_\-]+)\}/', function ($m) {
            $key = $m[1];
            $block = TemplateBlock::where('key', $key)->first();
            return $block ? $block->content : '';
        }, $html);

        // Simple placeholder replacement
        foreach ($data as $k => $v) {
            $html = str_replace('{' . $k . '}', $v, $html);
        }

        return $html;
    }

    /**
     * Render a saved PrintTemplate with an Order model (backwards compatible)
     */
    public static function renderTemplate(PrintTemplate $template, Order $order)
    {
        $vars = [
            'order_number' => $order->order_number ?? '',
            'date' => $order->created_at ? $order->created_at->format('Y-m-d') : '',
            'time' => $order->created_at ? $order->created_at->format('H:i') : '',
            'cashier' => $order->user->name ?? '',
            'customer_name' => $order->customer_name ?? '',
            'customer_phone' => $order->customer_phone ?? '',
            'customer_address' => $order->customer_address ?? '',
            'order_type' => $order->type ?? '',
            'room_number' => $order->room_number ?? '',
            'subtotal' => number_format($order->subtotal ?? 0, 2),
            'tax_amount' => number_format($order->tax_amount ?? 0, 2),
            'discount_amount' => number_format($order->discount_amount ?? 0, 2),
            'total_amount' => number_format($order->total_amount ?? 0, 2),
            'paid_amount' => number_format($order->paid_amount ?? 0, 2),
            'remaining_amount' => number_format($order->remaining_amount ?? 0, 2),
            'payment_method' => $order->payment_method ?? '',
            'notes' => $order->notes ?? '',
            'items_table' => self::renderItemsTable($order),
            'qr_code' => '',
        ];

        return self::renderDraft($template->content, $vars);
    }

    public static function renderItemsTable(Order $order): string
    {
        $html = '<table border="0" width="100%" style="border-collapse:collapse;font-size:12px;">';
        $html .= '<tbody>';
        foreach ($order->orderItems as $i => $item) {
            $html .= '<tr>';
            $html .= '<td>' . ($i + 1) . ' - ' . e($item->product->name) . '</td>';
            $html .= '<td style="text-align:right;">' . $item->quantity . ' x ' . number_format($item->price, 2) . '</td>';
            $html .= '<td style="text-align:right;">' . number_format($item->total_price, 2) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}
