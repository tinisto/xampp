<?php
/**
 * Rich Text Editor Component for Comments
 * Provides formatting options for comment input
 */

function renderRichTextEditor($config = []) {
    $defaultConfig = [
        'id' => 'rich-text-editor',
        'placeholder' => 'Напишите ваш комментарий...',
        'maxLength' => 2000,
        'allowedFormats' => ['bold', 'italic', 'underline', 'link', 'list', 'quote'],
        'showCounter' => true
    ];
    
    $config = array_merge($defaultConfig, $config);
    ?>
    
    <div class="rich-text-editor-container" id="<?= htmlspecialchars($config['id']) ?>-container">
        <div class="rte-toolbar">
            <?php if (in_array('bold', $config['allowedFormats'])): ?>
            <button type="button" class="rte-btn" data-action="bold" title="Жирный (Ctrl+B)">
                <i class="fas fa-bold"></i>
            </button>
            <?php endif; ?>
            
            <?php if (in_array('italic', $config['allowedFormats'])): ?>
            <button type="button" class="rte-btn" data-action="italic" title="Курсив (Ctrl+I)">
                <i class="fas fa-italic"></i>
            </button>
            <?php endif; ?>
            
            <?php if (in_array('underline', $config['allowedFormats'])): ?>
            <button type="button" class="rte-btn" data-action="underline" title="Подчеркнутый (Ctrl+U)">
                <i class="fas fa-underline"></i>
            </button>
            <?php endif; ?>
            
            <div class="rte-separator"></div>
            
            <?php if (in_array('link', $config['allowedFormats'])): ?>
            <button type="button" class="rte-btn" data-action="link" title="Вставить ссылку">
                <i class="fas fa-link"></i>
            </button>
            <?php endif; ?>
            
            <?php if (in_array('list', $config['allowedFormats'])): ?>
            <button type="button" class="rte-btn" data-action="bullet-list" title="Маркированный список">
                <i class="fas fa-list-ul"></i>
            </button>
            <button type="button" class="rte-btn" data-action="ordered-list" title="Нумерованный список">
                <i class="fas fa-list-ol"></i>
            </button>
            <?php endif; ?>
            
            <?php if (in_array('quote', $config['allowedFormats'])): ?>
            <button type="button" class="rte-btn" data-action="quote" title="Цитата">
                <i class="fas fa-quote-right"></i>
            </button>
            <?php endif; ?>
            
            <?php if (in_array('image', $config['allowedFormats'])): ?>
            <button type="button" class="rte-btn" data-action="image" title="Вставить изображение">
                <i class="fas fa-image"></i>
            </button>
            <?php endif; ?>
            
            <div class="rte-separator"></div>
            
            <button type="button" class="rte-btn" data-action="preview" title="Предпросмотр">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        
        <div class="rte-content">
            <div class="rte-editor" 
                 contenteditable="true" 
                 id="<?= htmlspecialchars($config['id']) ?>"
                 data-placeholder="<?= htmlspecialchars($config['placeholder']) ?>"
                 data-max-length="<?= $config['maxLength'] ?>"></div>
            <textarea class="rte-hidden-input" name="comment" style="display: none;"></textarea>
        </div>
        
        <?php if ($config['showCounter']): ?>
        <div class="rte-footer">
            <span class="rte-counter">
                <span class="rte-current">0</span> / <span class="rte-max"><?= $config['maxLength'] ?></span>
            </span>
        </div>
        <?php endif; ?>
        
        <!-- Link Dialog -->
        <div class="rte-dialog" id="link-dialog" style="display: none;">
            <div class="rte-dialog-content">
                <h4>Вставить ссылку</h4>
                <input type="url" class="rte-dialog-input" id="link-url" placeholder="https://example.com">
                <input type="text" class="rte-dialog-input" id="link-text" placeholder="Текст ссылки">
                <div class="rte-dialog-actions">
                    <button type="button" class="rte-dialog-btn rte-dialog-cancel">Отмена</button>
                    <button type="button" class="rte-dialog-btn rte-dialog-confirm">Вставить</button>
                </div>
            </div>
        </div>
        
        <!-- Image Upload Dialog -->
        <div class="rte-dialog" id="image-dialog" style="display: none;">
            <div class="rte-dialog-content" style="max-width: 500px;">
                <h4>Вставить изображение</h4>
                
                <div class="rte-upload-area" id="upload-area">
                    <input type="file" id="image-file" accept="image/*" style="display: none;">
                    <div class="rte-upload-dropzone" id="dropzone">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--primary-color); margin-bottom: 10px;"></i>
                        <p>Перетащите изображение сюда или <a href="#" onclick="document.getElementById('image-file').click(); return false;">выберите файл</a></p>
                        <small>Максимальный размер: 5MB. Форматы: JPEG, PNG, GIF, WebP</small>
                    </div>
                    <div class="rte-upload-progress" id="upload-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <small>Загрузка...</small>
                    </div>
                    <div class="rte-upload-preview" id="upload-preview" style="display: none;">
                        <img src="" alt="Preview" style="max-width: 100%; max-height: 200px;">
                        <input type="text" class="rte-dialog-input" id="image-alt" placeholder="Описание изображения (alt текст)" style="margin-top: 10px;">
                    </div>
                </div>
                
                <div class="rte-dialog-actions">
                    <button type="button" class="rte-dialog-btn rte-dialog-cancel">Отмена</button>
                    <button type="button" class="rte-dialog-btn rte-dialog-confirm" disabled>Вставить</button>
                </div>
            </div>
        </div>
        
        <!-- Preview Modal -->
        <div class="rte-preview" id="preview-modal" style="display: none;">
            <div class="rte-preview-content">
                <div class="rte-preview-header">
                    <h4>Предпросмотр</h4>
                    <button type="button" class="rte-preview-close">&times;</button>
                </div>
                <div class="rte-preview-body"></div>
            </div>
        </div>
    </div>
    
    <style>
    .rich-text-editor-container {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--surface);
        overflow: hidden;
    }
    
    .rte-toolbar {
        display: flex;
        align-items: center;
        padding: 8px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-light);
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .rte-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    
    .rte-btn:hover {
        background: var(--surface);
        color: var(--text-primary);
    }
    
    .rte-btn.active {
        background: var(--primary-color);
        color: white;
    }
    
    .rte-separator {
        width: 1px;
        height: 24px;
        background: var(--border-color);
        margin: 0 4px;
    }
    
    .rte-content {
        position: relative;
    }
    
    .rte-editor {
        min-height: 120px;
        max-height: 400px;
        overflow-y: auto;
        padding: 12px;
        outline: none;
        line-height: 1.6;
        color: var(--text-primary);
    }
    
    .rte-editor:empty:before {
        content: attr(data-placeholder);
        color: var(--text-secondary);
        pointer-events: none;
        position: absolute;
    }
    
    .rte-editor:focus {
        outline: none;
    }
    
    /* Formatting styles */
    .rte-editor b, .rte-editor strong {
        font-weight: 700;
    }
    
    .rte-editor i, .rte-editor em {
        font-style: italic;
    }
    
    .rte-editor u {
        text-decoration: underline;
    }
    
    .rte-editor a {
        color: var(--primary-color);
        text-decoration: underline;
    }
    
    .rte-editor ul, .rte-editor ol {
        margin: 10px 0;
        padding-left: 30px;
    }
    
    .rte-editor blockquote {
        border-left: 4px solid var(--border-color);
        margin: 10px 0;
        padding: 10px 20px;
        background: var(--bg-light);
        color: var(--text-secondary);
    }
    
    .rte-footer {
        padding: 8px 12px;
        border-top: 1px solid var(--border-color);
        background: var(--bg-light);
        display: flex;
        justify-content: flex-end;
    }
    
    .rte-counter {
        font-size: 13px;
        color: var(--text-secondary);
    }
    
    .rte-counter.warning {
        color: #ff9800;
    }
    
    .rte-counter.error {
        color: #f44336;
    }
    
    /* Dialog styles */
    .rte-dialog {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .rte-dialog-content {
        background: var(--surface);
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    
    .rte-dialog-content h4 {
        margin: 0 0 15px;
        color: var(--text-primary);
    }
    
    .rte-dialog-input {
        width: 100%;
        padding: 8px 12px;
        margin-bottom: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: var(--bg-light);
        color: var(--text-primary);
        font-size: 14px;
    }
    
    .rte-dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 15px;
    }
    
    .rte-dialog-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .rte-dialog-cancel {
        background: transparent;
        color: var(--text-secondary);
    }
    
    .rte-dialog-confirm {
        background: var(--primary-color);
        color: white;
    }
    
    /* Preview styles */
    .rte-preview {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .rte-preview-content {
        background: var(--surface);
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .rte-preview-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .rte-preview-header h4 {
        margin: 0;
        color: var(--text-primary);
    }
    
    .rte-preview-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-secondary);
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }
    
    .rte-preview-close:hover {
        background: var(--bg-light);
    }
    
    .rte-preview-body {
        padding: 20px;
        overflow-y: auto;
        line-height: 1.6;
        color: var(--text-primary);
    }
    
    /* Image upload styles */
    .rte-upload-dropzone {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        background: var(--bg-light);
        transition: all 0.3s;
    }
    
    .rte-upload-dropzone.dragover {
        border-color: var(--primary-color);
        background: rgba(0, 123, 255, 0.05);
    }
    
    .rte-upload-progress {
        padding: 20px;
        text-align: center;
    }
    
    .progress-bar {
        height: 4px;
        background: var(--bg-light);
        border-radius: 2px;
        overflow: hidden;
        margin: 10px 0;
    }
    
    .progress-fill {
        height: 100%;
        background: var(--primary-color);
        transition: width 0.3s;
    }
    
    .rte-upload-preview {
        text-align: center;
        padding: 20px;
    }
    
    .rte-upload-preview img {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Image display in editor */
    .rte-editor img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 10px 0;
        cursor: pointer;
    }
    
    .rte-editor img.selected {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }
    
    @media (max-width: 768px) {
        .rte-toolbar {
            padding: 6px;
        }
        
        .rte-btn {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }
        
        .rte-editor {
            min-height: 100px;
            padding: 10px;
        }
        
        .rte-upload-dropzone {
            padding: 30px 15px;
        }
    }
    </style>
    
    <script>
    (function() {
        const container = document.getElementById('<?= $config['id'] ?>-container');
        const editor = container.querySelector('.rte-editor');
        const hiddenInput = container.querySelector('.rte-hidden-input');
        const toolbar = container.querySelector('.rte-toolbar');
        const counter = container.querySelector('.rte-counter');
        const maxLength = <?= $config['maxLength'] ?>;
        
        // Initialize
        editor.focus();
        updateCounter();
        
        // Toolbar actions
        toolbar.addEventListener('click', function(e) {
            const btn = e.target.closest('.rte-btn');
            if (!btn) return;
            
            e.preventDefault();
            const action = btn.dataset.action;
            
            switch (action) {
                case 'bold':
                    document.execCommand('bold', false, null);
                    break;
                case 'italic':
                    document.execCommand('italic', false, null);
                    break;
                case 'underline':
                    document.execCommand('underline', false, null);
                    break;
                case 'bullet-list':
                    document.execCommand('insertUnorderedList', false, null);
                    break;
                case 'ordered-list':
                    document.execCommand('insertOrderedList', false, null);
                    break;
                case 'quote':
                    formatQuote();
                    break;
                case 'link':
                    showLinkDialog();
                    break;
                case 'image':
                    showImageDialog();
                    break;
                case 'preview':
                    showPreview();
                    break;
            }
            
            updateToolbarState();
            editor.focus();
        });
        
        // Update toolbar state
        function updateToolbarState() {
            toolbar.querySelectorAll('.rte-btn').forEach(btn => {
                const action = btn.dataset.action;
                let active = false;
                
                switch (action) {
                    case 'bold':
                        active = document.queryCommandState('bold');
                        break;
                    case 'italic':
                        active = document.queryCommandState('italic');
                        break;
                    case 'underline':
                        active = document.queryCommandState('underline');
                        break;
                }
                
                btn.classList.toggle('active', active);
            });
        }
        
        // Format quote
        function formatQuote() {
            const selection = window.getSelection();
            const text = selection.toString();
            
            if (text) {
                document.execCommand('formatBlock', false, 'blockquote');
            }
        }
        
        // Show link dialog
        function showLinkDialog() {
            const dialog = document.getElementById('link-dialog');
            const urlInput = dialog.querySelector('#link-url');
            const textInput = dialog.querySelector('#link-text');
            
            const selection = window.getSelection();
            const selectedText = selection.toString();
            
            textInput.value = selectedText;
            urlInput.value = '';
            
            dialog.style.display = 'flex';
            urlInput.focus();
            
            const confirm = dialog.querySelector('.rte-dialog-confirm');
            const cancel = dialog.querySelector('.rte-dialog-cancel');
            
            confirm.onclick = function() {
                const url = urlInput.value.trim();
                const text = textInput.value.trim() || url;
                
                if (url) {
                    if (selectedText) {
                        document.execCommand('createLink', false, url);
                    } else {
                        document.execCommand('insertHTML', false, `<a href="${url}">${text}</a>`);
                    }
                }
                
                dialog.style.display = 'none';
                editor.focus();
            };
            
            cancel.onclick = function() {
                dialog.style.display = 'none';
                editor.focus();
            };
        }
        
        // Show image dialog
        function showImageDialog() {
            const dialog = document.getElementById('image-dialog');
            const fileInput = dialog.querySelector('#image-file');
            const dropzone = dialog.querySelector('#dropzone');
            const uploadProgress = dialog.querySelector('#upload-progress');
            const uploadPreview = dialog.querySelector('#upload-preview');
            const previewImg = uploadPreview.querySelector('img');
            const altInput = dialog.querySelector('#image-alt');
            const confirmBtn = dialog.querySelector('.rte-dialog-confirm');
            const cancelBtn = dialog.querySelector('.rte-dialog-cancel');
            
            let uploadedUrl = null;
            
            // Reset dialog
            fileInput.value = '';
            altInput.value = '';
            uploadedUrl = null;
            dropzone.style.display = 'block';
            uploadProgress.style.display = 'none';
            uploadPreview.style.display = 'none';
            confirmBtn.disabled = true;
            
            dialog.style.display = 'flex';
            
            // File input change
            fileInput.onchange = function() {
                if (this.files && this.files[0]) {
                    uploadImage(this.files[0]);
                }
            };
            
            // Drag and drop
            dropzone.ondragover = function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            };
            
            dropzone.ondragleave = function() {
                this.classList.remove('dragover');
            };
            
            dropzone.ondrop = function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    uploadImage(e.dataTransfer.files[0]);
                }
            };
            
            // Upload image
            function uploadImage(file) {
                if (!file.type.startsWith('image/')) {
                    alert('Пожалуйста, выберите изображение');
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('Размер файла не должен превышать 5MB');
                    return;
                }
                
                const formData = new FormData();
                formData.append('image', file);
                
                dropzone.style.display = 'none';
                uploadProgress.style.display = 'block';
                
                const progressBar = uploadProgress.querySelector('.progress-fill');
                progressBar.style.width = '0%';
                
                fetch('/api/comments/upload-image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadedUrl = data.url;
                        previewImg.src = data.url;
                        uploadProgress.style.display = 'none';
                        uploadPreview.style.display = 'block';
                        confirmBtn.disabled = false;
                    } else {
                        throw new Error(data.error || 'Upload failed');
                    }
                })
                .catch(error => {
                    alert('Ошибка загрузки: ' + error.message);
                    dropzone.style.display = 'block';
                    uploadProgress.style.display = 'none';
                });
                
                // Simulate progress (since we can't track real upload progress with fetch)
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 30;
                    if (progress > 90) {
                        clearInterval(interval);
                        progress = 90;
                    }
                    progressBar.style.width = progress + '%';
                }, 200);
            }
            
            // Confirm button
            confirmBtn.onclick = function() {
                if (uploadedUrl) {
                    const alt = altInput.value || 'Изображение';
                    document.execCommand('insertHTML', false, `<img src="${uploadedUrl}" alt="${escapeHtml(alt)}" />`);
                    dialog.style.display = 'none';
                    editor.focus();
                }
            };
            
            // Cancel button
            cancelBtn.onclick = function() {
                dialog.style.display = 'none';
                editor.focus();
            };
        }
        
        // Show preview
        function showPreview() {
            const modal = document.getElementById('preview-modal');
            const body = modal.querySelector('.rte-preview-body');
            
            body.innerHTML = editor.innerHTML || '<p style="color: var(--text-secondary);">Нет содержимого для предпросмотра</p>';
            modal.style.display = 'flex';
            
            modal.querySelector('.rte-preview-close').onclick = function() {
                modal.style.display = 'none';
                editor.focus();
            };
        }
        
        // Update counter
        function updateCounter() {
            if (!counter) return;
            
            const text = editor.innerText || '';
            const length = text.length;
            const current = counter.querySelector('.rte-current');
            
            current.textContent = length;
            
            counter.classList.remove('warning', 'error');
            if (length > maxLength * 0.8) {
                counter.classList.add('warning');
            }
            if (length > maxLength) {
                counter.classList.add('error');
            }
        }
        
        // Convert HTML to clean text for submission
        function getCleanHTML() {
            let html = editor.innerHTML;
            
            // Remove any script tags or dangerous content
            html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
            html = html.replace(/on\w+\s*=\s*["'][^"']*["']/gi, '');
            
            // Convert to allowed tags only
            const allowedTags = ['b', 'i', 'u', 'a', 'ul', 'ol', 'li', 'blockquote', 'br', 'p'];
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Clean up the HTML
            function cleanNode(node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    return node.textContent;
                }
                
                if (node.nodeType === Node.ELEMENT_NODE) {
                    const tagName = node.tagName.toLowerCase();
                    
                    if (!allowedTags.includes(tagName)) {
                        // Replace with its content
                        return Array.from(node.childNodes).map(cleanNode).join('');
                    }
                    
                    // Clean attributes
                    if (tagName === 'a') {
                        const href = node.getAttribute('href');
                        if (href && href.startsWith('http')) {
                            return `<a href="${href}" target="_blank" rel="noopener">${Array.from(node.childNodes).map(cleanNode).join('')}</a>`;
                        }
                        return Array.from(node.childNodes).map(cleanNode).join('');
                    }
                    
                    return `<${tagName}>${Array.from(node.childNodes).map(cleanNode).join('')}</${tagName}>`;
                }
                
                return '';
            }
            
            return Array.from(tempDiv.childNodes).map(cleanNode).join('');
        }
        
        // Update hidden input on change
        editor.addEventListener('input', function() {
            updateCounter();
            updateToolbarState();
            hiddenInput.value = getCleanHTML();
        });
        
        editor.addEventListener('paste', function(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
        });
        
        // Keyboard shortcuts
        editor.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'b':
                        e.preventDefault();
                        document.execCommand('bold', false, null);
                        break;
                    case 'i':
                        e.preventDefault();
                        document.execCommand('italic', false, null);
                        break;
                    case 'u':
                        e.preventDefault();
                        document.execCommand('underline', false, null);
                        break;
                }
                updateToolbarState();
            }
        });
        
        // Update toolbar on selection change
        document.addEventListener('selectionchange', function() {
            if (editor.contains(window.getSelection().anchorNode)) {
                updateToolbarState();
            }
        });
        
        // Expose API
        window.RichTextEditor = window.RichTextEditor || {};
        window.RichTextEditor['<?= $config['id'] ?>'] = {
            getValue: function() {
                return getCleanHTML();
            },
            setValue: function(html) {
                editor.innerHTML = html;
                updateCounter();
            },
            clear: function() {
                editor.innerHTML = '';
                updateCounter();
            },
            focus: function() {
                editor.focus();
            }
        };
    })();
    </script>
    
    <?php
}
?>