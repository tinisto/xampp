<?php
/**
 * Modern Reusable Modal Component
 * 
 * Usage:
 * include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/modal-modern.php';
 * renderModalModern(); // Include this once per page
 * 
 * JavaScript Usage:
 * ModalManager.confirm('Title', 'Message', () => { /* callback */ });
 * ModalManager.alert('Title', 'Message');
 * ModalManager.prompt('Title', 'Message', (value) => { /* callback */ });
 */

function renderModalModern() {
?>
<style>
:root {
    --modal-overlay: rgba(0, 0, 0, 0.5);
    --modal-background: #ffffff;
    --modal-border: #e9ecef;
    --modal-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    --modal-radius: 12px;
    --modal-text: #333;
    --modal-text-secondary: #666;
}

[data-theme="dark"] {
    --modal-background: #2d3748;
    --modal-border: #4a5568;
    --modal-text: #e4e6eb;
    --modal-text-secondary: #a0aec0;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--modal-overlay);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    backdrop-filter: blur(2px);
}

.modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.modal-container {
    background: var(--modal-background);
    border: 1px solid var(--modal-border);
    border-radius: var(--modal-radius);
    box-shadow: var(--modal-shadow);
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow: hidden;
    transform: translateY(-20px) scale(0.95);
    transition: all 0.3s ease;
}

.modal-overlay.show .modal-container {
    transform: translateY(0) scale(1);
}

.modal-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--modal-border);
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.modal-icon.info {
    background: #e3f2fd;
    color: #1976d2;
}

.modal-icon.warning {
    background: #fff3e0;
    color: #f57c00;
}

.modal-icon.danger {
    background: #ffebee;
    color: #d32f2f;
}

.modal-icon.success {
    background: #e8f5e8;
    color: #2e7d32;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--modal-text);
    margin: 0;
    flex: 1;
}

.modal-close {
    background: none;
    border: none;
    font-size: 20px;
    color: var(--modal-text-secondary);
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: var(--modal-border);
    color: var(--modal-text);
}

.modal-body {
    padding: 16px 24px 20px;
    color: var(--modal-text);
    line-height: 1.5;
}

.modal-input {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--modal-border);
    border-radius: 8px;
    font-size: 14px;
    background: var(--modal-background);
    color: var(--modal-text);
    margin-top: 12px;
}

.modal-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.modal-footer {
    padding: 16px 24px 20px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    border-top: 1px solid var(--modal-border);
}

.modal-btn {
    padding: 10px 20px;
    border: 1px solid var(--modal-border);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 80px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.modal-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.modal-btn.primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.modal-btn.primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.modal-btn.danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.modal-btn.danger:hover {
    background: #c82333;
    border-color: #c82333;
}

.modal-btn.secondary {
    background: var(--modal-background);
    color: var(--modal-text);
}

.modal-btn.secondary:hover {
    background: var(--modal-border);
}

/* Mobile responsive */
@media (max-width: 480px) {
    .modal-container {
        width: 95%;
        margin: 20px;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding-left: 16px;
        padding-right: 16px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .modal-btn {
        width: 100%;
    }
}

/* Animation for backdrop */
@keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes modalSlideIn {
    from { 
        transform: translateY(-30px) scale(0.9);
        opacity: 0;
    }
    to { 
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}
</style>

<!-- Modal HTML Structure -->
<div id="modernModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <div id="modalIcon" class="modal-icon info">
                <i class="fas fa-info"></i>
            </div>
            <h3 id="modalTitle" class="modal-title">Modal Title</h3>
            <button id="modalClose" class="modal-close" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="modalMessage">Modal message content</div>
            <input type="text" id="modalInput" class="modal-input" style="display: none;" placeholder="Enter value...">
        </div>
        <div class="modal-footer" id="modalFooter">
            <button id="modalCancel" class="modal-btn secondary">Отмена</button>
            <button id="modalConfirm" class="modal-btn primary">OK</button>
        </div>
    </div>
</div>

<script>
class ModalManager {
    static currentCallback = null;
    static currentInput = null;
    
    static init() {
        const modal = document.getElementById('modernModal');
        const closeBtn = document.getElementById('modalClose');
        const cancelBtn = document.getElementById('modalCancel');
        const confirmBtn = document.getElementById('modalConfirm');
        const input = document.getElementById('modalInput');
        
        // Close handlers
        closeBtn?.addEventListener('click', () => this.hide());
        cancelBtn?.addEventListener('click', () => this.hide());
        
        // Confirm handler
        confirmBtn?.addEventListener('click', () => {
            if (this.currentCallback) {
                if (this.currentInput) {
                    // Prompt mode
                    const value = input.value.trim();
                    if (value) {
                        this.currentCallback(value);
                        this.hide();
                    } else {
                        input.focus();
                        input.style.borderColor = '#dc3545';
                        setTimeout(() => {
                            input.style.borderColor = '';
                        }, 2000);
                    }
                } else {
                    // Confirm mode
                    this.currentCallback();
                    this.hide();
                }
            } else {
                // Alert mode
                this.hide();
            }
        });
        
        // Backdrop click
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.hide();
            }
        });
        
        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal?.classList.contains('show')) {
                this.hide();
            }
        });
        
        // Enter key in input
        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                confirmBtn?.click();
            }
        });
    }
    
    static show(title, message, type = 'info', buttons = null) {
        const modal = document.getElementById('modernModal');
        const titleEl = document.getElementById('modalTitle');
        const messageEl = document.getElementById('modalMessage');
        const iconEl = document.getElementById('modalIcon');
        const footerEl = document.getElementById('modalFooter');
        const inputEl = document.getElementById('modalInput');
        
        if (!modal) {
            console.error('Modal component not found. Make sure to include renderModalModern()');
            return;
        }
        
        // Set content
        titleEl.textContent = title;
        messageEl.innerHTML = message;
        
        // Set icon
        iconEl.className = `modal-icon ${type}`;
        const iconMap = {
            'info': 'fas fa-info',
            'warning': 'fas fa-exclamation-triangle',
            'danger': 'fas fa-exclamation-circle',
            'success': 'fas fa-check-circle'
        };
        iconEl.innerHTML = `<i class="${iconMap[type] || iconMap.info}"></i>`;
        
        // Set buttons
        if (buttons) {
            footerEl.innerHTML = buttons;
        }
        
        // Hide input by default
        inputEl.style.display = 'none';
        inputEl.value = '';
        
        // Show modal
        modal.classList.add('show');
        
        // Focus first button or input
        setTimeout(() => {
            const firstBtn = footerEl.querySelector('button');
            if (inputEl.style.display !== 'none') {
                inputEl.focus();
            } else if (firstBtn) {
                firstBtn.focus();
            }
        }, 100);
    }
    
    static hide() {
        const modal = document.getElementById('modernModal');
        if (modal) {
            modal.classList.remove('show');
        }
        this.currentCallback = null;
        this.currentInput = null;
    }
    
    static alert(title, message, type = 'info') {
        const buttons = `<button id="modalConfirm" class="modal-btn primary">OK</button>`;
        this.show(title, message, type, buttons);
        this.currentCallback = null;
    }
    
    static confirm(title, message, callback, type = 'warning') {
        const buttons = `
            <button id="modalCancel" class="modal-btn secondary">Отмена</button>
            <button id="modalConfirm" class="modal-btn ${type === 'danger' ? 'danger' : 'primary'}">Подтвердить</button>
        `;
        this.show(title, message, type, buttons);
        this.currentCallback = callback;
    }
    
    static prompt(title, message, callback, placeholder = '', type = 'info') {
        const buttons = `
            <button id="modalCancel" class="modal-btn secondary">Отмена</button>
            <button id="modalConfirm" class="modal-btn primary">OK</button>
        `;
        this.show(title, message, type, buttons);
        
        const inputEl = document.getElementById('modalInput');
        inputEl.style.display = 'block';
        inputEl.placeholder = placeholder;
        
        this.currentCallback = callback;
        this.currentInput = true;
    }
    
    static success(title, message) {
        this.alert(title, message, 'success');
    }
    
    static error(title, message) {
        this.alert(title, message, 'danger');
    }
    
    static warning(title, message) {
        this.alert(title, message, 'warning');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    ModalManager.init();
});

// Global convenience functions
window.modalAlert = (title, message, type) => ModalManager.alert(title, message, type);
window.modalConfirm = (title, message, callback, type) => ModalManager.confirm(title, message, callback, type);
window.modalPrompt = (title, message, callback, placeholder) => ModalManager.prompt(title, message, callback, placeholder);
</script>

<?php
}
?>