<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type', // order, invoice, receipt, etc
        'content', // HTML content (fallback)
        'is_active',
        'is_default',
        'theme_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(ReportTheme::class);
    }

    public function templateBlocks(): HasMany
    {
        return $this->hasMany(TemplateBlock::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function generateContent($data = [])
    {
        // If template has blocks, generate from blocks
        if ($this->templateBlocks->isNotEmpty()) {
            return $this->generateFromBlocks($data);
        }

        // Fallback to static content
        return $this->processStaticContent($data);
    }

    private function generateFromBlocks($data)
    {
        $html = '';

        foreach ($this->templateBlocks as $block) {
            if ($block->is_visible) {
                $html .= $block->generateContent($data);
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
}
