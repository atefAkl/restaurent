<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateBlockComponent extends Model
{
    protected $fillable = [
        'template_block_id',
        'report_component_id',
        'name', // اسم المثيل في هذا الجزء
        'content', // JSON: المحتوى الفعلي (متغيرات أو ثابت)
        'properties', // JSON: خصائص الظهور (موقع، حجم، ألوان، خطوط)
        'is_visible',
        'order',
    ];

    protected $casts = [
        'content' => 'array',
        'properties' => 'array',
        'is_visible' => 'boolean',
        'order' => 'integer',
    ];

    public function templateBlock(): BelongsTo
    {
        return $this->belongsTo(TemplateBlock::class);
    }

    public function reportComponent(): BelongsTo
    {
        return $this->belongsTo(ReportComponent::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * توليد HTML للمكون في هذا الجزء
     */
    public function generateHTML($data = [])
    {
        if (!$this->is_visible) {
            return '';
        }

        return $this->reportComponent->generateHTML(
            $this->content ?? [],
            $this->properties ?? []
        );
    }

    /**
     * الحصول على المحتوى مع استبدال المتغيرات
     */
    public function getProcessedContent($data = [])
    {
        $processed = [];
        $content = $this->content ?? [];

        foreach ($content as $key => $value) {
            if (is_string($value)) {
                // استبدال المتغيرات
                $processed[$key] = $this->replaceVariables($value, $data);
            } else {
                $processed[$key] = $value;
            }
        }

        return $processed;
    }

    /**
     * استبدال المتغيرات في النص
     */
    private function replaceVariables($text, $data)
    {
        foreach ($data as $key => $value) {
            // تحويل القيمة إلى نص
            $stringValue = is_string($value) ? $value : 
                          (is_array($value) || is_object($value) ? json_encode($value) : (string) $value);
            $text = str_replace('{{' . $key . '}}', $stringValue, $text);
        }

        return $text;
    }

    /**
     * الحصول على خصائص CSS كنص
     */
    public function getStyleAttribute()
    {
        $properties = $this->properties ?? [];
        $styles = [];

        // Position
        if (isset($properties['position_x'])) {
            $styles[] = 'left: ' . $properties['position_x'] . 'px';
        }
        if (isset($properties['position_y'])) {
            $styles[] = 'top: ' . $properties['position_y'] . 'px';
        }
        if (isset($properties['position'])) {
            $styles[] = 'position: ' . $properties['position'];
        }

        // Size
        if (isset($properties['width'])) {
            $styles[] = 'width: ' . $properties['width'];
        }
        if (isset($properties['height'])) {
            $styles[] = 'height: ' . $properties['height'];
        }

        // Colors
        if (isset($properties['color'])) {
            $styles[] = 'color: ' . $properties['color'];
        }
        if (isset($properties['background'])) {
            $styles[] = 'background-color: ' . $properties['background'];
        }

        // Typography
        if (isset($properties['font_size'])) {
            $styles[] = 'font-size: ' . $properties['font_size'];
        }
        if (isset($properties['font_weight'])) {
            $styles[] = 'font-weight: ' . $properties['font_weight'];
        }
        if (isset($properties['font_family'])) {
            $styles[] = 'font-family: ' . $properties['font_family'];
        }

        // Spacing
        if (isset($properties['margin'])) {
            $styles[] = 'margin: ' . $properties['margin'];
        }
        if (isset($properties['padding'])) {
            $styles[] = 'padding: ' . $properties['padding'];
        }

        // Borders
        if (isset($properties['border'])) {
            $styles[] = 'border: ' . $properties['border'];
        }
        if (isset($properties['border_radius'])) {
            $styles[] = 'border-radius: ' . $properties['border_radius'];
        }

        // Alignment
        if (isset($properties['text_align'])) {
            $styles[] = 'text-align: ' . $properties['text_align'];
        }

        return implode('; ', $styles);
    }

    /**
     * نسخ المكون إلى جزء آخر
     */
    public function duplicateToBlock($templateBlockId)
    {
        return self::create([
            'template_block_id' => $templateBlockId,
            'report_component_id' => $this->report_component_id,
            'name' => $this->name . ' (نسخة)',
            'content' => $this->content,
            'properties' => $this->properties,
            'is_visible' => $this->is_visible,
            'order' => $this->order,
        ]);
    }

    /**
     * الحصول على جميع المتغيرات المطلوبة لهذا المكون
     */
    public function getRequiredVariables()
    {
        return $this->reportComponent->getRequiredVariables();
    }

    /**
     * التحقق من أن جميع المتغيرات المطلوبة متوفرة في البيانات
     */
    public function validateContent($data)
    {
        $required = $this->getRequiredVariables();
        $missing = [];

        foreach ($required as $variable) {
            if (!isset($data[$variable])) {
                $missing[] = $variable;
            }
        }

        return $missing;
    }
}
