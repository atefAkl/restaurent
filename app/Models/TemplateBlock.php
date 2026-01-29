<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateBlock extends Model
{
    protected $fillable = [
        'name',
        'key',
        'content',
        'meta',
        'print_template_id',
        'type', // header, footer, content, sidebar
        'position_x',
        'position_y',
        'width',
        'height',
        'is_visible',
        'order',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_visible' => 'boolean',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'order' => 'integer',
    ];

    public function printTemplate(): BelongsTo
    {
        return $this->belongsTo(PrintTemplate::class);
    }

    public function reportElements(): HasMany
    {
        return $this->hasMany(ReportElement::class)->orderBy('order');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function generateContent($data = [])
    {
        // If block has elements, generate from elements
        if ($this->reportElements->isNotEmpty()) {
            return $this->generateFromElements($data);
        }

        // Fallback to static content
        return $this->processStaticContent($data);
    }

    private function generateFromElements($data)
    {
        $html = '';

        foreach ($this->reportElements as $element) {
            if ($element->is_visible) {
                $html .= $element->generateHTML($data);
            }
        }

        return $html;
    }

    private function processStaticContent($data)
    {
        $content = $this->content;

        // Replace placeholders with data
        foreach ($data as $key => $value) {
            // Convert value to string if it's not already
            $stringValue = is_string($value) ? $value : (is_array($value) || is_object($value) ? json_encode($value) : (string) $value);
            $content = str_replace('{{' . $key . '}}', $stringValue, $content);
        }

        return $content;
    }

    public function getStyleAttribute()
    {
        $styles = [];

        if ($this->position_x) {
            $styles[] = 'left: ' . $this->position_x . 'px';
        }

        if ($this->position_y) {
            $styles[] = 'top: ' . $this->position_y . 'px';
        }

        if ($this->width) {
            $styles[] = 'width: ' . $this->width . 'px';
        }

        if ($this->height) {
            $styles[] = 'height: ' . $this->height . 'px';
        }

        return implode('; ', $styles);
    }
}
