<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportComponent extends Model
{
    protected $fillable = [
        'name',
        'type', // header, text, image, table, barcode, line, qr_code, etc
        'description',
        'default_properties', // JSON: الخصائص الافتراضية
        'content_template', // JSON: قالب المحتوى مع المتغيرات
        'is_active',
        'is_system',
    ];

    protected $casts = [
        'default_properties' => 'array',
        'content_template' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function templateBlockComponents(): HasMany
    {
        return $this->hasMany(TemplateBlockComponent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * توليد HTML للمكون بناءً على المحتوى والخصائص
     */
    public function generateHTML($content = [], $properties = [])
    {
        // دمج الخصائص الافتراضية مع الخصائص المخصصة
        $mergedProperties = array_merge(
            $this->default_properties ?? [],
            $properties ?? []
        );

        // معالجة المحتوى
        $processedContent = $this->processContent($content);

        return $this->renderByType($processedContent, $mergedProperties);
    }

    /**
     * توليد HTML للمكون بناءً على المحتوى والخصائص
     */
    public function generateContent($content = [], $properties = [])
    {
        // دمج الخصائص الافتراضية مع الخصائص المخصصة
        $mergedProperties = array_merge(
            $this->default_properties ?? [],
            $properties ?? []
        );

        // معالجة المحتوى
        $processedContent = $this->processContent($content);

        return $this->renderByType($processedContent, $mergedProperties);
    }

    /**
     * معالجة المحتوى واستبدال المتغيرات
     */
    private function processContent($content)
    {
        if (empty($content)) {
            return $this->getDefaultContent();
        }

        $processed = [];
        foreach ($content as $key => $value) {
            // تحويل القيمة إلى نص
            $stringValue = is_string($value) ? $value : (is_array($value) || is_object($value) ? json_encode($value) : (string) $value);
            $processed[$key] = $stringValue;
        }

        return $processed;
    }

    /**
     * الحصول على المحتوى الافتراضي للمكون
     */
    private function getDefaultContent()
    {
        return $this->content_template ?? [];
    }

    /**
     * عرض المكون بناءً على نوعه
     */
    private function renderByType($content, $properties)
    {
        switch ($this->type) {
            case 'header':
                return $this->renderHeader($content, $properties);
            case 'text':
                return $this->renderText($content, $properties);
            case 'image':
                return $this->renderImage($content, $properties);
            case 'table':
                return $this->renderTable($content, $properties);
            case 'barcode':
                return $this->renderBarcode($content, $properties);
            case 'qr_code':
                return $this->renderQRCode($content, $properties);
            case 'line':
                return $this->renderLine($content, $properties);
            case 'logo':
                return $this->renderLogo($content, $properties);
            default:
                return $this->renderText($content, $properties);
        }
    }

    private function renderHeader($content, $properties)
    {
        $text = $content['text'] ?? $this->name;
        $level = $properties['level'] ?? 1;
        $class = $properties['class'] ?? '';
        $style = $this->generateStyle($properties);

        return "<h{$level} class=\"{$class}\" style=\"{$style}\">{$text}</h{$level}>";
    }

    private function renderText($content, $properties)
    {
        $text = $content['text'] ?? '';
        $tag = $properties['tag'] ?? 'p';
        $class = $properties['class'] ?? '';
        $style = $this->generateStyle($properties);

        return "<{$tag} class=\"{$class}\" style=\"{$style}\">{$text}</{$tag}>";
    }

    private function renderImage($content, $properties)
    {
        $src = $content['src'] ?? '';
        $alt = $content['alt'] ?? '';
        $class = $properties['class'] ?? '';
        $style = $this->generateStyle($properties);

        return "<img src=\"{$src}\" alt=\"{$alt}\" class=\"{$class}\" style=\"{$style}\">";
    }

    private function renderTable($content, $properties)
    {
        $headers = $content['headers'] ?? [];
        $rows = $content['rows'] ?? [];
        $class = $properties['class'] ?? 'table';
        $style = $this->generateStyle($properties);

        $html = "<table class=\"{$class}\" style=\"{$style}\">";

        if (!empty($headers)) {
            $html .= "<thead><tr>";
            foreach ($headers as $header) {
                $html .= "<th>{$header}</th>";
            }
            $html .= "</tr></thead>";
        }

        if (!empty($rows)) {
            $html .= "<tbody>";
            foreach ($rows as $row) {
                $html .= "<tr>";
                foreach ($row as $cell) {
                    $html .= "<td>{$cell}</td>";
                }
                $html .= "</tr>";
            }
            $html .= "</tbody>";
        }

        $html .= "</table>";
        return $html;
    }

    private function renderBarcode($content, $properties)
    {
        $value = $content['value'] ?? '';
        $type = $content['type'] ?? 'code128';
        $class = $properties['class'] ?? '';
        $style = $this->generateStyle($properties);

        return "<div class=\"barcode {$class}\" style=\"{$style}\" data-value=\"{$value}\" data-type=\"{$type}\">{$value}</div>";
    }

    private function renderQRCode($content, $properties)
    {
        $value = $content['value'] ?? '';
        $size = $properties['size'] ?? '150x150';
        $class = $properties['class'] ?? '';
        $style = $this->generateStyle($properties);

        return "<div class=\"qrcode {$class}\" style=\"{$style}\" data-value=\"{$value}\" data-size=\"{$size}\">QR: {$value}</div>";
    }

    private function renderLine($content, $properties)
    {
        $class = $properties['class'] ?? '';
        $style = $this->generateStyle($properties);

        return "<hr class=\"{$class}\" style=\"{$style}\">";
    }

    private function renderLogo($content, $properties)
    {
        $src = $content['src'] ?? '/images/logo.png';
        $alt = $content['alt'] ?? 'Logo';
        $width = $properties['width'] ?? '150px';
        $height = $properties['height'] ?? 'auto';
        $class = $properties['class'] ?? '';
        $style = $this->generateStyle($properties);

        return "<img src=\"{$src}\" alt=\"{$alt}\" class=\"logo {$class}\" style=\"{$style}; width: {$width}; height: {$height};\">";
    }

    /**
     * توليد CSS من الخصائص
     */
    private function generateStyle($properties)
    {
        $styles = [];

        // Position
        if (isset($properties['position'])) {
            $styles[] = "position: {$properties['position']}";
        }
        if (isset($properties['left'])) {
            $styles[] = "left: {$properties['left']}";
        }
        if (isset($properties['top'])) {
            $styles[] = "top: {$properties['top']}";
        }
        if (isset($properties['right'])) {
            $styles[] = "right: {$properties['right']}";
        }
        if (isset($properties['bottom'])) {
            $styles[] = "bottom: {$properties['bottom']}";
        }

        // Size
        if (isset($properties['width'])) {
            $styles[] = "width: {$properties['width']}";
        }
        if (isset($properties['height'])) {
            $styles[] = "height: {$properties['height']}";
        }

        // Colors
        if (isset($properties['color'])) {
            $styles[] = "color: {$properties['color']}";
        }
        if (isset($properties['background'])) {
            $styles[] = "background-color: {$properties['background']}";
        }

        // Typography
        if (isset($properties['font_size'])) {
            $styles[] = "font-size: {$properties['font_size']}";
        }
        if (isset($properties['font_weight'])) {
            $styles[] = "font-weight: {$properties['font_weight']}";
        }
        if (isset($properties['font_family'])) {
            $styles[] = "font-family: {$properties['font_family']}";
        }

        // Spacing
        if (isset($properties['margin'])) {
            $styles[] = "margin: {$properties['margin']}";
        }
        if (isset($properties['padding'])) {
            $styles[] = "padding: {$properties['padding']}";
        }

        // Borders
        if (isset($properties['border'])) {
            $styles[] = "border: {$properties['border']}";
        }
        if (isset($properties['border_radius'])) {
            $styles[] = "border-radius: {$properties['border_radius']}";
        }

        // Alignment
        if (isset($properties['text_align'])) {
            $styles[] = "text-align: {$properties['text_align']}";
        }

        return implode('; ', $styles);
    }

    /**
     * الحصول على قائمة المتغيرات المطلوبة للمكون
     */
    public function getRequiredVariables()
    {
        $template = $this->content_template ?? [];
        $variables = [];

        foreach ($template as $key => $value) {
            if (is_string($value) && preg_match_all('/\{\{(\w+)\}\}/', $value, $matches)) {
                $variables = array_merge($variables, $matches[1]);
            }
        }

        return array_unique($variables);
    }

    /**
     * إنشاء مكونات النظام الافتراضية
     */
    public static function createSystemComponents()
    {
        $components = [
            [
                'name' => 'شعار الشركة',
                'type' => 'logo',
                'description' => 'شعار الشركة في رأس الصفحة',
                'is_system' => true,
                'content_template' => [
                    'src' => '/images/logo.png',
                    'alt' => '{{company_name}}'
                ],
                'default_properties' => [
                    'width' => '150px',
                    'height' => 'auto',
                    'text_align' => 'center',
                    'margin' => '10px 0'
                ]
            ],
            [
                'name' => 'اسم الشركة',
                'type' => 'header',
                'description' => 'اسم الشركة كعنوان رئيسي',
                'is_system' => true,
                'content_template' => [
                    'text' => '{{company_name}}'
                ],
                'default_properties' => [
                    'level' => 1,
                    'text_align' => 'center',
                    'font_size' => '24px',
                    'font_weight' => 'bold',
                    'margin' => '10px 0'
                ]
            ],
            [
                'name' => 'رقم الطلب',
                'type' => 'text',
                'description' => 'رقم الطلب',
                'is_system' => true,
                'content_template' => [
                    'text' => 'رقم الطلب: {{order_number}}'
                ],
                'default_properties' => [
                    'font_size' => '16px',
                    'font_weight' => 'bold',
                    'margin' => '5px 0'
                ]
            ],
            [
                'name' => 'التاريخ والوقت',
                'type' => 'text',
                'description' => 'تاريخ ووقت الطلب',
                'is_system' => true,
                'content_template' => [
                    'text' => 'التاريخ: {{date}} - الوقت: {{time}}'
                ],
                'default_properties' => [
                    'font_size' => '14px',
                    'margin' => '5px 0'
                ]
            ],
            [
                'name' => 'بيانات العميل',
                'type' => 'text',
                'description' => 'اسم العميل ورقم الهاتف',
                'is_system' => true,
                'content_template' => [
                    'text' => 'العميل: {{customer_name}} - {{customer_phone}}'
                ],
                'default_properties' => [
                    'font_size' => '14px',
                    'margin' => '5px 0'
                ]
            ],
            [
                'name' => 'جدول المنتجات',
                'type' => 'table',
                'description' => 'جدول منتجات الطلب',
                'is_system' => true,
                'content_template' => [
                    'headers' => ['المنتج', 'الكمية', 'السعر', 'الإجمالي'],
                    'rows' => '{{items}}'
                ],
                'default_properties' => [
                    'width' => '100%',
                    'border' => '1px solid #ddd',
                    'margin' => '10px 0'
                ]
            ],
            [
                'name' => 'الإجمالي',
                'type' => 'text',
                'description' => 'الإجمالي النهائي للطلب',
                'is_system' => true,
                'content_template' => [
                    'text' => 'الإجمالي: {{total}} ريال'
                ],
                'default_properties' => [
                    'font_size' => '18px',
                    'font_weight' => 'bold',
                    'text_align' => 'right',
                    'margin' => '10px 0'
                ]
            ],
            [
                'name' => 'خط فاصل',
                'type' => 'line',
                'description' => 'خط فاصل بين الأقسام',
                'is_system' => true,
                'default_properties' => [
                    'border' => '1px solid #ccc',
                    'margin' => '15px 0'
                ]
            ],
            [
                'name' => 'شكر وتقدير',
                'type' => 'text',
                'description' => 'رسالة شكر في نهاية الفاتورة',
                'is_system' => true,
                'content_template' => [
                    'text' => 'شكراً لزيارتكم!'
                ],
                'default_properties' => [
                    'font_size' => '14px',
                    'text_align' => 'center',
                    'font_style' => 'italic',
                    'margin' => '10px 0'
                ]
            ]
        ];

        foreach ($components as $component) {
            self::create($component);
        }
    }
}
