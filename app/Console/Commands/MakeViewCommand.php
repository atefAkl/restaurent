<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeViewCommand extends Command
{
    protected $signature = 'make:view {view : The name of the view}';
    protected $description = 'Create a new Blade view file';

    public function handle()
    {
        $view = $this->argument('view');
        
        // Convert dot notation to directory structure
        $path = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');
        
        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        // Create the view file with basic template
        $template = $this->getViewTemplate($view);
        
        if (File::exists($path)) {
            $this->error("View already exists: {$path}");
            return 1;
        }
        
        File::put($path, $template);
        
        $this->info("View created successfully: {$path}");
        return 0;
    }
    
    protected function getViewTemplate($view)
    {
        $viewName = Str::kebab(basename($view));
        $title = Str::title(str_replace('-', ' ', $viewName));
        
        return <<<BLADE
@extends('layouts.app')

@section('title', '{$title}')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{$title}</h1>
            <p class="text-muted mb-0">Page description here</p>
        </div>
    </div>

    <!-- Add your content here -->
    
</div>
@endsection
BLADE;
    }
}
