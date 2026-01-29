<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTheme extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'is_active',
        'styles',
    ];

    protected $casts = [
        'styles' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function printTemplates(): HasMany
    {
        return $this->hasMany(PrintTemplate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function generateCSS()
    {
        $styles = $this->styles ?? [];
        
        $css = '';
        
        // Generate CSS from theme styles
        if (isset($styles['colors'])) {
            $css .= $this->generateColorCSS($styles['colors']);
        }
        
        if (isset($styles['fonts'])) {
            $css .= $this->generateFontCSS($styles['fonts']);
        }
        
        if (isset($styles['borders'])) {
            $css .= $this->generateBorderCSS($styles['borders']);
        }
        
        if (isset($styles['shadows'])) {
            $css .= $this->generateShadowCSS($styles['shadows']);
        }
        
        return $css;
    }

    private function generateColorCSS($colors)
    {
        $css = '';
        
        foreach ($colors as $key => $color) {
            $css .= "--{$key}: {$color};\n";
        }
        
        return $css;
    }

    private function generateFontCSS($fonts)
    {
        $css = '';
        
        if (isset($fonts['family'])) {
            $css .= "--font-family: {$fonts['family']};\n";
        }
        
        if (isset($fonts['size'])) {
            $css .= "--font-size: {$fonts['size']};\n";
        }
        
        return $css;
    }

    private function generateBorderCSS($borders)
    {
        $css = '';
        
        if (isset($borders['radius'])) {
            $css .= "--border-radius: {$borders['radius']};\n";
        }
        
        if (isset($borders['width'])) {
            $css .= "--border-width: {$borders['width']};\n";
        }
        
        return $css;
    }

    private function generateShadowCSS($shadows)
    {
        $css = '';
        
        if (isset($shadows['box'])) {
            $css .= "--box-shadow: {$shadows['box']};\n";
        }
        
        return $css;
    }

    public static function getDefaultTheme()
    {
        return self::active()->default()->first() ?? self::create([
            'name' => 'Default Theme',
            'description' => 'Default theme for reports',
            'is_default' => true,
            'is_active' => true,
            'styles' => [
                'colors' => [
                    'primary' => '#007bff',
                    'secondary' => '#6c757d',
                    'success' => '#28a745',
                    'danger' => '#dc3545',
                    'warning' => '#ffc107',
                    'info' => '#17a2b8',
                    'light' => '#f8f9fa',
                    'dark' => '#343a40',
                    'text' => '#212529',
                    'background' => '#ffffff',
                ],
                'fonts' => [
                    'family' => 'Arial, sans-serif',
                    'size' => '14px',
                ],
                'borders' => [
                    'radius' => '4px',
                    'width' => '1px',
                ],
                'shadows' => [
                    'box' => '0 2px 4px rgba(0,0,0,0.1)',
                ],
            ],
        ]);
    }
}
