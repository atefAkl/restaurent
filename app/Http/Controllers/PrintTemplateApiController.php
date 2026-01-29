<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RenderService;

class PrintTemplateApiController extends Controller
{
    public function previewDraft(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'sampleData' => 'nullable|array',
        ]);

        $content = $request->input('content');
        $sampleData = $request->input('sampleData', []);

        $html = RenderService::renderDraft($content, $sampleData);

        return response($html);
    }
}
