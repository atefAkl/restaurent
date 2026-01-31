@extends('layouts.order')
@section('title', 'تخصيص القالب')

@section('content')
<style>
    .customize-container {
        height: calc(100vh - 120px);
        overflow: hidden;
    }
    
    .preview-section {
        background: #f8f9fa;
        border-left: 1px solid #dee2e6;
        overflow-y: auto;
        position: relative;
    }
    
    .controls-section {
        overflow-y: auto;
        background: white;
    }
    
    .preview-canvas {
        background: white;
        margin: 20px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-height: 400px;
        position: relative;
    }
    
    .template-block {
        border: 2px dashed #007bff;
        background: rgba(0, 123, 255, 0.05);
        position: absolute;
        cursor: move;
        transition: all 0.2s ease;
    }
    
    .template-block:hover {
        border-color: #0056b3;
        background: rgba(0, 123, 255, 0.1);
        z-index: 10;
    }
    
    .template-block.selected {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.1);
        z-index: 20;
    }
    
    .template-block-header {
        position: absolute;
        top: -25px;
        right: 0;
        background: #007bff;
        color: white;
        padding: 2px 8px;
        font-size: 11px;
        border-radius: 3px;
        white-space: nowrap;
    }
    
    .template-element {
        border: 1px dashed #6c757d;
        background: rgba(108, 117, 125, 0.05);
        position: absolute;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
        padding: 2px 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .template-element:hover {
        border-color: #495057;
        background: rgba(108, 117, 125, 0.1);
        z-index: 30;
    }
    
    .template-element.selected {
        border-color: #ffc107;
        background: rgba(255, 193, 7, 0.1);
        z-index: 40;
    }
    
    .resize-handle {
        position: absolute;
        width: 8px;
        height: 8px;
        background: #007bff;
        border: 1px solid white;
        border-radius: 50%;
        cursor: nwse-resize;
    }
    
    .resize-handle.se {
        bottom: -4px;
        right: -4px;
    }
    
    .control-panel {
        border-bottom: 1px solid #dee2e6;
        padding: 15px;
    }
    
    .control-group {
        margin-bottom: 20px;
    }
    
    .control-group h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .element-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .element-item {
        padding: 8px 12px;
        border-bottom: 1px solid #f8f9fa;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    
    .element-item:hover {
        background: #f8f9fa;
    }
    
    .element-item.active {
        background: #e3f2fd;
        border-left: 3px solid #2196f3;
    }
    
    .color-picker-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    
    .no-sidebar {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
    
    @media (max-width: 768px) {
        .customize-container {
            flex-direction: column;
            height: auto;
        }
        
        .preview-section {
            border-left: none;
            border-top: 1px solid #dee2e6;
            min-height: 400px;
        }
    }
</style>

<div class="container-fluid p-0 customize-container">
    <!-- Header -->
    <div class="card border-0 border-bottom rounded-0">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">تخصيص القالب: {{ $template->name }}</h5>
                    <p class="text-muted mb-0">{{ $template->type }}</p>
                </div>
                <div class="btn-group">
                    <button onclick="saveTemplate()" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>حفظ التغييرات
                    </button>
                    <button onclick="resetTemplate()" class="btn btn-outline-warning">
                        <i class="bi bi-arrow-clockwise me-2"></i>إعادة تعيين
                    </button>
                    <a href="{{ route('report-templates.show', $template) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-2"></i>إلغاء
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-row h-100">
        <!-- Controls Section -->
        <div class="controls-section col-md-4 p-0">
            <!-- Block Controls -->
            <div class="control-panel">
                <div class="control-group">
                    <h6>إدارة الأجزاء</h6>
                    <div class="mb-3">
                        <button onclick="addBlock()" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-plus-circle me-2"></i>إضافة جزء جديد
                        </button>
                    </div>
                    <div class="element-list" id="blocksList">
                        @foreach($template->templateBlocks as $block)
                        <div class="element-item" data-block-id="{{ $block->id }}" onclick="selectBlock({{ $block->id }})">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-layers me-2"></i>{{ $block->name }}
                                </span>
                                <div>
                                    <span class="badge bg-secondary">{{ $block->type }}</span>
                                    <button onclick="event.stopPropagation(); deleteBlock({{ $block->id }})" class="btn btn-sm btn-outline-danger ms-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Element Controls -->
            <div class="control-panel" id="elementControls" style="display: none;">
                <div class="control-group">
                    <h6>إدارة العناصر</h6>
                    <div class="mb-3">
                        <button onclick="addElement()" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-plus-circle me-2"></i>إضافة عنصر جديد
                        </button>
                    </div>
                    <div class="element-list" id="elementsList">
                        <!-- Elements will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Available Components -->
            <div class="control-panel">
                <div class="control-group">
                    <h6>المكونات المتاحة</h6>
                    <div class="mb-3">
                        <input id="componentSearch" class="form-control form-control-sm" placeholder="ابحث عن مكون..." oninput="searchComponents(this.value)">
                    </div>
                    <div class="element-list" id="componentsList">
                        <!-- Components will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Properties Panel -->
            <div class="control-panel" id="propertiesPanel" style="display: none;">
                <div class="control-group">
                    <h6>خصائص العنصر</h6>
                    <div id="propertiesForm">
                        <!-- Properties will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="preview-section col-md-8">
            <div class="preview-canvas" id="previewCanvas">
                @if($template->theme)
                <style>
                    {{ $template->theme->generateCSS() }}
                </style>
                @endif
                
                <!-- Render template blocks -->
                @foreach($template->templateBlocks as $block)
                <div class="template-block" 
                     data-block-id="{{ $block->id }}"
                     style="left: {{ $block->position_x }}px; 
                            top: {{ $block->position_y }}px; 
                            width: {{ $block->width }}px; 
                            height: {{ $block->height }}px;">
                    <div class="template-block-header">{{ $block->name }}</div>
                    
                    @foreach($block->reportElements as $element)
                    <div class="template-element" 
                         data-element-id="{{ $element->id }}"
                         data-block-id="{{ $block->id }}"
                         style="left: {{ $element->position_x }}px; 
                                top: {{ $element->position_y }}px; 
                                width: {{ $element->width }}px; 
                                height: {{ $element->height }}px;
                                {{ $element->getStyleAttribute() }}">
                        {{ $element->name }}
                        <div class="resize-handle se"></div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Add Block Modal -->
<div class="modal fade" id="addBlockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة جزء جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addBlockForm">
                    <div class="mb-3">
                        <label class="form-label">اسم الجزء</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select class="form-select" name="type" required>
                            <option value="header">هيدر</option>
                            <option value="content">محتوى</option>
                            <option value="footer">فوتر</option>
                            <option value="sidebar">شريط جانبي</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">العرض (px)</label>
                                <input type="number" class="form-control" name="width" value="200">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">الارتفاع (px)</label>
                                <input type="number" class="form-control" name="height" value="100">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveNewBlock()">إضافة</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Element Modal -->
<div class="modal fade" id="addElementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة عنصر جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addElementForm">
                    <div class="mb-3">
                        <label class="form-label">اسم العنصر</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select class="form-select" name="type" required>
                            <option value="text">نص</option>
                            <option value="logo">شعار</option>
                            <option value="image">صورة</option>
                            <option value="table">جدول</option>
                            <option value="barcode">باركود</option>
                            <option value="line">خط</option>
                            <option value="qr_code">QR Code</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المحتوى</label>
                        <textarea class="form-control" name="content" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">العرض (px)</label>
                                <input type="number" class="form-control" name="width" value="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">الارتفاع (px)</label>
                                <input type="number" class="form-control" name="height" value="30">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveNewElement()">إضافة</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedBlock = null;
let selectedElement = null;
let isDragging = false;
let isResizing = false;
let dragElement = null;
let dragOffset = { x: 0, y: 0 };

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeCanvas();
    setupEventListeners();
    loadAvailableComponents();
});

let availableComponents = {};

function initializeCanvas() {
    const canvas = document.getElementById('previewCanvas');
    
    // Make blocks draggable
    document.querySelectorAll('.template-block').forEach(block => {
        makeDraggable(block);
    });
    
    // Make elements draggable and resizable
    document.querySelectorAll('.template-element').forEach(element => {
        makeDraggable(element);
        makeResizable(element);
    });
}

function setupEventListeners() {
    // Click on canvas to deselect
    document.getElementById('previewCanvas').addEventListener('click', function(e) {
        if (e.target === this) {
            deselectAll();
        }
    });
}

function makeDraggable(element) {
    element.addEventListener('mousedown', function(e) {
        if (e.target.classList.contains('resize-handle')) return;
        
        isDragging = true;
        dragElement = element;
        
        const rect = element.getBoundingClientRect();
        const canvasRect = document.getElementById('previewCanvas').getBoundingClientRect();
        
        dragOffset.x = e.clientX - rect.left;
        dragOffset.y = e.clientY - rect.top;
        
        e.preventDefault();
    });
}

function makeResizable(element) {
    const handle = element.querySelector('.resize-handle');
    if (!handle) return;
    
    handle.addEventListener('mousedown', function(e) {
        isResizing = true;
        dragElement = element;
        e.stopPropagation();
        e.preventDefault();
    });
}

document.addEventListener('mousemove', function(e) {
    if (isDragging && dragElement) {
        const canvas = document.getElementById('previewCanvas');
        const canvasRect = canvas.getBoundingClientRect();
        
        let newX = e.clientX - canvasRect.left - dragOffset.x;
        let newY = e.clientY - canvasRect.top - dragOffset.y;
        
        // Constrain to canvas
        newX = Math.max(0, Math.min(newX, canvasRect.width - dragElement.offsetWidth));
        newY = Math.max(0, Math.min(newY, canvasRect.height - dragElement.offsetHeight));
        
        dragElement.style.left = newX + 'px';
        dragElement.style.top = newY + 'px';
        
        // Update position in data
        updateElementPosition(dragElement);
    }
    
    if (isResizing && dragElement) {
        const canvas = document.getElementById('previewCanvas');
        const canvasRect = canvas.getBoundingClientRect();
        
        let newWidth = e.clientX - dragElement.getBoundingClientRect().left;
        let newHeight = e.clientY - dragElement.getBoundingClientRect().top;
        
        // Minimum size
        newWidth = Math.max(50, newWidth);
        newHeight = Math.max(20, newHeight);
        
        dragElement.style.width = newWidth + 'px';
        dragElement.style.height = newHeight + 'px';
        
        // Update size in data
        updateElementSize(dragElement);
    }
});

document.addEventListener('mouseup', function() {
    isDragging = false;
    isResizing = false;
    dragElement = null;
});

function selectBlock(blockId) {
    deselectAll();
    
    const block = document.querySelector(`[data-block-id="${blockId}"]`);
    if (block) {
        block.classList.add('selected');
        selectedBlock = blockId;
        
        // Show elements for this block
        showElementsForBlock(blockId);
        document.getElementById('elementControls').style.display = 'block';
    }
}

function selectElement(elementId) {
    deselectAll();
    
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) {
        element.classList.add('selected');
        selectedElement = elementId;
        
        // Show properties for this element
        showElementProperties(elementId);
        document.getElementById('propertiesPanel').style.display = 'block';
    }
}

function deselectAll() {
    document.querySelectorAll('.template-block, .template-element').forEach(el => {
        el.classList.remove('selected');
    });
    selectedBlock = null;
    selectedElement = null;
    document.getElementById('elementControls').style.display = 'none';
    document.getElementById('propertiesPanel').style.display = 'none';
}

function showElementsForBlock(blockId) {
    const elementsList = document.getElementById('elementsList');
    elementsList.innerHTML = '';
    
    const elements = document.querySelectorAll(`[data-block-id="${blockId}"].template-element`);
    elements.forEach(element => {
        const elementId = element.dataset.elementId;
        const elementName = element.textContent.trim();
        
        const item = document.createElement('div');
        item.className = 'element-item';
        item.onclick = () => selectElement(elementId);
        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-square me-2"></i>${elementName}
                </span>
                <button onclick="event.stopPropagation(); deleteElement(${elementId})" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        elementsList.appendChild(item);
    });
}

function showElementProperties(elementId) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    const propertiesForm = document.getElementById('propertiesForm');
    
    propertiesForm.innerHTML = `
        <div class="mb-3">
            <label class="form-label">اسم العنصر</label>
            <input type="text" class="form-control" value="${element.textContent.trim()}" onchange="updateElementName(${elementId}, this.value)">
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">الموضع X</label>
                    <input type="number" class="form-control" value="${parseInt(element.style.left)}" onchange="updateElementPositionX(${elementId}, this.value)">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">الموضع Y</label>
                    <input type="number" class="form-control" value="${parseInt(element.style.top)}" onchange="updateElementPositionY(${elementId}, this.value)">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">العرض</label>
                    <input type="number" class="form-control" value="${parseInt(element.style.width)}" onchange="updateElementWidth(${elementId}, this.value)">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">الارتفاع</label>
                    <input type="number" class="form-control" value="${parseInt(element.style.height)}" onchange="updateElementHeight(${elementId}, this.value)">
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">لون النص</label>
            <div class="color-picker-group">
                <input type="color" class="form-control form-control-color" onchange="updateElementColor(${elementId}, this.value)">
                <div class="color-preview" style="background: ${element.style.color || '#000000'}"></div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">حجم الخط</label>
            <select class="form-select" onchange="updateElementFontSize(${elementId}, this.value)">
                <option value="10px">10px</option>
                <option value="12px">12px</option>
                <option value="14px" selected>14px</option>
                <option value="16px">16px</option>
                <option value="18px">18px</option>
                <option value="20px">20px</option>
                <option value="24px">24px</option>
            </select>
        </div>
    `;
}

function addBlock() {
    const modal = new bootstrap.Modal(document.getElementById('addBlockModal'));
    modal.show();
}

function saveNewBlock() {
    const form = document.getElementById('addBlockForm');
    const formData = new FormData(form);
    
    // This would normally save to backend
    const blockData = {
        name: formData.get('name'),
        type: formData.get('type'),
        width: parseInt(formData.get('width')),
        height: parseInt(formData.get('height')),
        position_x: 50,
        position_y: 50
    };
    
    // Add to canvas (temporary - would be saved to backend)
    addBlockToCanvas(blockData);
    
    bootstrap.Modal.getInstance(document.getElementById('addBlockModal')).hide();
    form.reset();
}

function addBlockToCanvas(blockData) {
    const canvas = document.getElementById('previewCanvas');
    const block = document.createElement('div');
    block.className = 'template-block';
    block.dataset.blockId = 'new-' + Date.now();
    block.style.cssText = `
        left: ${blockData.position_x}px; 
        top: ${blockData.position_y}px; 
        width: ${blockData.width}px; 
        height: ${blockData.height}px;
    `;
    block.innerHTML = `<div class="template-block-header">${blockData.name}</div>`;
    
    canvas.appendChild(block);
    makeDraggable(block);
}

function addElement() {
    if (!selectedBlock) {
        alert('الرجاء اختيار جزء أولاً');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('addElementModal'));
    modal.show();
}

function saveNewElement() {
    const form = document.getElementById('addElementForm');
    const formData = new FormData(form);
    
    const elementData = {
        name: formData.get('name'),
        type: formData.get('type'),
        content: formData.get('content'),
        width: parseInt(formData.get('width')),
        height: parseInt(formData.get('height')),
        position_x: 10,
        position_y: 10
    };
    
    addElementToCanvas(elementData);
    
    bootstrap.Modal.getInstance(document.getElementById('addElementModal')).hide();
    form.reset();
}

function addElementToCanvas(elementData) {
    const block = document.querySelector(`[data-block-id="${selectedBlock}"]`);
    if (!block) return;
    
    const element = document.createElement('div');
    element.className = 'template-element';
    element.dataset.elementId = 'new-' + Date.now();
    element.dataset.blockId = selectedBlock;
    element.style.cssText = `
        left: ${elementData.position_x}px; 
        top: ${elementData.position_y}px; 
        width: ${elementData.width}px; 
        height: ${elementData.height}px;
    `;
    element.textContent = elementData.name;
    element.innerHTML += '<div class="resize-handle se"></div>';
    // store metadata for server
    element.dataset.type = elementData.type || 'text';
    element.dataset.content = elementData.content || '';
    
    block.appendChild(element);
    makeDraggable(element);
    makeResizable(element);
    
    showElementsForBlock(selectedBlock);
}

function loadAvailableComponents() {
    (async () => {
        try {
            const url = '{{ route("report-components.available") }}';
            const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!resp.ok) return;
            const comps = await resp.json();
            const list = document.getElementById('componentsList');
            list.innerHTML = '';
            comps.forEach(c => {
                availableComponents[c.id] = c;
                const item = document.createElement('div');
                item.className = 'element-item';
                item.onclick = () => addComponentToSelectedBlock(c.id);
                item.innerHTML = `<div class="d-flex justify-content-between align-items-center"><span><i class="bi bi-puzzle me-2"></i>${c.name}</span><small class="text-muted">${c.type}</small></div>`;
                list.appendChild(item);
            });
        } catch (e) {
            console.error('Failed load components', e);
        }
    })();
}

function searchComponents(q) {
    const lower = q.trim().toLowerCase();
    const list = document.getElementById('componentsList');
    Array.from(list.children).forEach(child => {
        const text = child.textContent.trim().toLowerCase();
        child.style.display = text.includes(lower) ? '' : 'none';
    });
}

function addComponentToSelectedBlock(componentId) {
    if (!selectedBlock) {
        alert('الرجاء اختيار جزء أولاً');
        return;
    }

    const comp = availableComponents[componentId];
    if (!comp) return;

    const elementData = {
        name: comp.name,
        type: comp.type,
        content: JSON.stringify(comp.content_template || comp.content_template || ''),
        width: 100,
        height: 30,
        position_x: 10,
        position_y: 10
    };

    addElementToCanvas(elementData);
    showElementsForBlock(selectedBlock);
}

function updateElementPosition(element) {
    const elementId = element.dataset.elementId;
    const x = parseInt(element.style.left);
    const y = parseInt(element.style.top);
    
    // Update in backend
    console.log(`Updating element ${elementId} position to ${x}, ${y}`);
}

function updateElementSize(element) {
    const elementId = element.dataset.elementId;
    const width = parseInt(element.style.width);
    const height = parseInt(element.style.height);
    
    // Update in backend
    console.log(`Updating element ${elementId} size to ${width}x${height}`);
}

function updateElementName(elementId, name) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) {
        element.textContent = name;
        element.innerHTML += '<div class="resize-handle se"></div>';
    }
}

function updateElementPositionX(elementId, value) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) element.style.left = value + 'px';
}

function updateElementPositionY(elementId, value) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) element.style.top = value + 'px';
}

function updateElementWidth(elementId, value) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) element.style.width = value + 'px';
}

function updateElementHeight(elementId, value) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) element.style.height = value + 'px';
}

function updateElementColor(elementId, color) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) element.style.color = color;
}

function updateElementFontSize(elementId, size) {
    const element = document.querySelector(`[data-element-id="${elementId}"]`);
    if (element) element.style.fontSize = size;
}

function deleteBlock(blockId) {
    if (confirm('هل أنت متأكد من حذف هذا الجزء؟')) {
        const block = document.querySelector(`[data-block-id="${blockId}"]`);
        if (block) block.remove();
        
        // Hide elements panel
        document.getElementById('elementControls').style.display = 'none';
        document.getElementById('propertiesPanel').style.display = 'none';
    }
}

function deleteElement(elementId) {
    if (confirm('هل أنت متأكد من حذف هذا العنصر؟')) {
        const element = document.querySelector(`[data-element-id="${elementId}"]`);
        if (element) element.remove();
        
        // Refresh elements list
        if (selectedBlock) {
            showElementsForBlock(selectedBlock);
        }
    }
}

function saveTemplate() {
    // Collect all blocks and elements data
    const templateData = {
        blocks: []
    };

    document.querySelectorAll('.template-block').forEach(block => {
        const blockData = {
            id: block.dataset.blockId,
            name: block.querySelector('.template-block-header').textContent,
            position_x: parseInt(block.style.left) || 0,
            position_y: parseInt(block.style.top) || 0,
            width: parseInt(block.style.width) || 0,
            height: parseInt(block.style.height) || 0,
            elements: []
        };

        block.querySelectorAll('.template-element').forEach(element => {
            blockData.elements.push({
                id: element.dataset.elementId,
                type: element.dataset.type || 'text',
                name: element.textContent.trim(),
                content: element.dataset.content || '',
                position_x: parseInt(element.style.left) || 0,
                position_y: parseInt(element.style.top) || 0,
                width: parseInt(element.style.width) || 0,
                height: parseInt(element.style.height) || 0,
                properties: {} // future: parse inline styles
            });
        });

        templateData.blocks.push(blockData);
    });

    // Send to backend
    (async () => {
        try {
            const url = '{{ route("report-templates.save-customize", $template) }}';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(templateData)
            });

            const json = await resp.json();

            if (resp.ok && json.success) {
                alert(json.message || 'تم حفظ التغييرات بنجاح');
                // reload to reflect saved layout (IDs/DB state)
                location.reload();
            } else {
                console.error('Save failed', json);
                alert('فشل حفظ التغييرات: ' + (json.message || resp.statusText));
            }
        } catch (err) {
            console.error(err);
            alert('حدث خطأ أثناء الحفظ: ' + err.message);
        }
    })();
}

function resetTemplate() {
    if (confirm('هل أنت متأكد من إعادة تعيين جميع التغييرات؟')) {
        location.reload();
    }
}
</script>
@endsection
