<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportElement extends Model
{
    protected $fillable = [
        'template_block_id',
        'type', // text, logo, image, table, barcode, line, etc.
        'name',
        'content', // text content, image path, etc.
        'position_x',
        'position_y',
        'width',
        'height',
        'properties', // additional properties like font-size, color, etc.
        'is_visible',
        'order',
    ];

    protected $casts = [
        'properties' => 'array',
        'is_visible' => 'boolean',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'order' => 'integer',
    ];

    public function templateBlock(): BelongsTo
    {
        return $this->belongsTo(TemplateBlock::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function generateHTML($data = [])
    {
        switch ($this->type) {
            case 'text':
                return $this->generateTextElement($data);
            case 'logo':
                return $this->generateLogoElement($data);
            case 'image':
                return $this->generateImageElement($data);
            case 'table':
                return $this->generateTableElement($data);
            case 'barcode':
                return $this->generateBarcodeElement($data);
            case 'line':
                return $this->generateLineElement($data);
            case 'qr_code':
                return $this->generateQRCodeElement($data);
            default:
                return '';
        }
    }

    private function generateTextElement($data)
    {
        $content = $this->processPlaceholders($this->content, $data);
        $properties = $this->properties ?? [];

        $style = $this->generateStyle($properties);
        $class = $properties['class'] ?? '';

        return "<span class=\"{$class}\" style=\"{$style}\">{$content}</span>";
    }

    private function generateLogoElement($data)
    {
        $properties = $this->properties ?? [];
        $style = $this->generateStyle($properties);
        $class = $properties['class'] ?? '';
        $alt = $properties['alt'] ?? 'Logo';

        return "<img src=\"{$this->content}\" alt=\"{$alt}\" class=\"{$class}\" style=\"{$style}\" />";
    }

    private function generateImageElement($data)
    {
        $properties = $this->properties ?? [];
        $style = $this->generateStyle($properties);
        $class = $properties['class'] ?? '';
        $alt = $properties['alt'] ?? 'Image';

        return "<img src=\"{$this->content}\" alt=\"{$alt}\" class=\"{$class}\" style=\"{$style}\" />";
    }

    private function generateTableElement($data)
    {
        $properties = $this->properties ?? [];
        $style = $this->generateStyle($properties);
        $class = $properties['class'] ?? '';

        // For table elements, content should contain table structure
        $content = $this->processPlaceholders($this->content, $data);

        return "<table class=\"{$class}\" style=\"{$style}\">{$content}</table>";
    }

    private function generateBarcodeElement($data)
    {
        $properties = $this->properties ?? [];
        $style = $this->generateStyle($properties);
        $class = $properties['class'] ?? '';
        $content = $this->processPlaceholders($this->content, $data);

        return "<div class=\"barcode {$class}\" style=\"{$style}\">{$content}</div>";
    }

    private function generateLineElement($data)
    {
        $properties = $this->properties ?? [];
        $style = $this->generateStyle($properties);
        $class = $properties['class'] ?? '';

        return "<hr class=\"{$class}\" style=\"{$style}\" />";
    }

    private function generateQRCodeElement($data)
    {
        $properties = $this->properties ?? [];
        $style = $this->generateStyle($properties);
        $class = $properties['class'] ?? '';
        $content = $this->processPlaceholders($this->content, $data);

        return "<div class=\"qrcode {$class}\" style=\"{$style}\">{$content}</div>";
    }

    private function processPlaceholders($content, $data)
    {
        foreach ($data as $key => $value) {
            // Convert value to string if it's not already
            $stringValue = is_string($value) ? $value : (is_array($value) || is_object($value) ? json_encode($value) : (string) $value);
            $content = str_replace('{{' . $key . '}}', $stringValue, $content);
        }

        return $content;
    }

    private function generateStyle($properties)
    {
        $styles = [];

        // Position
        if ($this->position_x) {
            $styles[] = 'left: ' . $this->position_x . 'px';
        }

        if ($this->position_y) {
            $styles[] = 'top: ' . $this->position_y . 'px';
        }

        // Size
        if ($this->width) {
            $styles[] = 'width: ' . $this->width . 'px';
        }

        if ($this->height) {
            $styles[] = 'height: ' . $this->height . 'px';
        }

        // Text properties
        if (isset($properties['font_size'])) {
            $styles[] = 'font-size: ' . $properties['font_size'];
        }

        if (isset($properties['font_family'])) {
            $styles[] = 'font-family: ' . $properties['font_family'];
        }

        if (isset($properties['color'])) {
            $styles[] = 'color: ' . $properties['color'];
        }

        if (isset($properties['background_color'])) {
            $styles[] = 'background-color: ' . $properties['background_color'];
        }

        if (isset($properties['text_align'])) {
            $styles[] = 'text-align: ' . $properties['text_align'];
        }

        if (isset($properties['font_weight'])) {
            $styles[] = 'font-weight: ' . $properties['font_weight'];
        }

        // Border properties
        if (isset($properties['border'])) {
            $styles[] = 'border: ' . $properties['border'];
        }

        if (isset($properties['border_radius'])) {
            $styles[] = 'border-radius: ' . $properties['border_radius'];
        }

        // Other properties
        if (isset($properties['margin'])) {
            $styles[] = 'margin: ' . $properties['margin'];
        }

        if (isset($properties['padding'])) {
            $styles[] = 'padding: ' . $properties['padding'];
        }

        return implode('; ', $styles);
    }

    public function getStyleAttribute()
    {
        return $this->generateStyle($this->properties ?? []);
    }
}
