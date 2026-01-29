<?php

namespace App\Services;

use App\Models\PrintTemplate;
use App\Models\Order;

class PrintTemplateService
{
    /**
     * استبدال المتغيرات في القالب بقيم الطلب
     */
    public static function renderTemplate(PrintTemplate $template, Order $order): string
    {
        $vars = [
            '{order_number}' => $order->order_number,
            '{date}' => $order->created_at ? $order->created_at->format('Y-m-d') : '',
            '{time}' => $order->created_at ? $order->created_at->format('H:i') : '',
            '{cashier}' => $order->user->name ?? '',
            '{customer_name}' => $order->customer_name ?? '',
            '{customer_phone}' => $order->customer_phone ?? '',
            '{customer_address}' => $order->customer_address ?? '',
            '{order_type}' => __($order->type ? 'orders.type_' . $order->type : ''),
            '{room_number}' => $order->room_number ?? '',
            '{subtotal}' => number_format($order->subtotal, 2),
            '{tax_amount}' => number_format($order->tax_amount, 2),
            '{discount_amount}' => number_format($order->discount_amount, 2),
            '{total_amount}' => number_format($order->total_amount, 2),
            '{paid_amount}' => number_format($order->paid_amount, 2),
            '{remaining_amount}' => number_format($order->remaining_amount, 2),
            '{payment_method}' => __($order->payment_method ? 'orders.payment_' . $order->payment_method : ''),
            '{notes}' => $order->notes ?? '',
            '{items_table}' => self::renderItemsTable($order),
        ];
        return strtr($template->content, $vars);
    }

    /**
     * توليد جدول المنتجات HTML
     */
    public static function renderItemsTable(Order $order): string
    {
        $html = '<table border="1" width="100%" style="border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr>';
        $html .= '<th>#</th><th>' . __('orders.item') . '</th><th>' . __('orders.qty') . '</th><th>' . __('orders.price') . '</th><th>' . __('orders.total') . '</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($order->orderItems as $i => $item) {
            $html .= '<tr>';
            $html .= '<td>' . ($i + 1) . '</td>';
            $html .= '<td>' . e($item->product->name) . '</td>';
            $html .= '<td>' . $item->quantity . '</td>';
            $html .= '<td>' . number_format($item->price, 2) . '</td>';
            $html .= '<td>' . number_format($item->total_price, 2) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}
