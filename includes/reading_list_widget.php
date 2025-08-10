<?php
// Reading list widget for adding items to lists

function include_reading_list_widget($itemType, $itemId) {
    if (!isset($_SESSION['user_id'])) {
        return; // Only show for logged-in users
    }
    
    ?>
    <div class="reading-list-widget" style="background: var(--bg-secondary); border-radius: 12px; padding: 20px; margin: 20px 0;">
        <h3 style="margin: 0 0 15px 0; font-size: 18px;">
            <i class="fas fa-bookmark"></i> Добавить в список для чтения
        </h3>
        
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- Quick add to "Read Later" -->
            <button onclick="quickAddToReadLater('<?= $itemType ?>', <?= $itemId ?>)" 
                    id="quickAddBtn"
                    style="background: #28a745; color: white; border: none; padding: 8px 16px; 
                           border-radius: 6px; font-size: 14px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-plus"></i> Читать позже
            </button>
            
            <!-- Add to specific list dropdown -->
            <div style="position: relative; display: inline-block;">
                <button onclick="toggleListDropdown()" 
                        id="listDropdownBtn"
                        style="background: #007bff; color: white; border: none; padding: 8px 16px; 
                               border-radius: 6px; font-size: 14px; cursor: pointer; white-space: nowrap;">
                    <i class="fas fa-list"></i> Выбрать список
                    <i class="fas fa-chevron-down" style="margin-left: 5px; font-size: 12px;"></i>
                </button>
                
                <div id="listDropdown" 
                     style="display: none; position: absolute; top: 100%; left: 0; margin-top: 5px; 
                            background: var(--bg-primary); border: 1px solid var(--border-color); 
                            border-radius: 8px; box-shadow: 0 4px 12px var(--shadow); 
                            min-width: 250px; z-index: 1000; max-height: 300px; overflow-y: auto;">
                    <div id="listDropdownContent">
                        <div style="padding: 15px; text-align: center; color: var(--text-secondary);">
                            <i class="fas fa-spinner fa-spin"></i> Загрузка списков...
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Create new list button -->
            <button onclick="showCreateListForm()" 
                    style="background: transparent; color: var(--text-primary); 
                           border: 1px solid var(--border-color); padding: 8px 16px; 
                           border-radius: 6px; font-size: 14px; cursor: pointer; white-space: nowrap;">
                <i class="fas fa-plus-circle"></i> Новый список
            </button>
        </div>
        
        <!-- Create new list form (hidden) -->
        <div id="createListForm" style="display: none; margin-top: 20px; padding: 20px; 
                                        background: var(--bg-primary); border-radius: 8px; 
                                        border: 1px solid var(--border-color);">
            <h4 style="margin: 0 0 15px 0; font-size: 16px;">Создать новый список</h4>
            <div style="display: grid; gap: 15px;">
                <div>
                    <input type="text" id="newListName" placeholder="Название списка" 
                           style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); 
                                  border-radius: 4px; font-size: 14px;">
                </div>
                <div>
                    <textarea id="newListDesc" placeholder="Описание (необязательно)" rows="2"
                              style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); 
                                     border-radius: 4px; font-size: 14px; resize: vertical;"></textarea>
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" id="newListPublic">
                        <span style="font-size: 14px;">Публичный список</span>
                    </label>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button onclick="createListAndAdd('<?= $itemType ?>', <?= $itemId ?>)" 
                            style="background: #28a745; color: white; border: none; padding: 8px 16px; 
                                   border-radius: 4px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-save"></i> Создать и добавить
                    </button>
                    <button onclick="hideCreateListForm()" 
                            style="background: var(--bg-secondary); color: var(--text-primary); 
                                   border: 1px solid var(--border-color); padding: 8px 16px; 
                                   border-radius: 4px; font-size: 14px; cursor: pointer;">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Status messages -->
        <div id="readingListMessage" style="display: none; margin-top: 15px; padding: 10px; 
                                           border-radius: 4px; font-size: 14px;"></div>
    </div>
    
    <script>
    let userLists = [];
    let isDropdownLoaded = false;
    
    async function quickAddToReadLater(itemType, itemId) {
        const btn = document.getElementById('quickAddBtn');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Добавление...';
        btn.disabled = true;
        
        try {
            const response = await fetch('/api/reading-lists/quick-add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_type: itemType,
                    item_id: itemId
                })
            });
            
            const data = await response.json();
            showMessage(data.success ? 'success' : 'error', data.message || data.error);
            
            if (data.success) {
                btn.innerHTML = '<i class="fas fa-check"></i> Добавлено';
                btn.style.background = '#6c757d';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '#28a745';
                    btn.disabled = false;
                }, 2000);
            } else {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'Произошла ошибка');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
    
    async function toggleListDropdown() {
        const dropdown = document.getElementById('listDropdown');
        
        if (dropdown.style.display === 'none') {
            if (!isDropdownLoaded) {
                await loadUserLists();
            }
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }
    
    async function loadUserLists() {
        const content = document.getElementById('listDropdownContent');
        
        try {
            const response = await fetch('/api/reading-lists/get-user-lists');
            const data = await response.json();
            
            if (data.success) {
                userLists = data.lists;
                
                if (userLists.length === 0) {
                    content.innerHTML = `
                        <div style="padding: 15px; text-align: center; color: var(--text-secondary);">
                            <p>У вас пока нет списков</p>
                            <button onclick="showCreateListForm(); toggleListDropdown();" 
                                    style="background: #28a745; color: white; border: none; 
                                           padding: 6px 12px; border-radius: 4px; font-size: 14px; cursor: pointer;">
                                Создать первый список
                            </button>
                        </div>
                    `;
                } else {
                    content.innerHTML = userLists.map(list => `
                        <div onclick="addToList(${list.id}, '<?= $itemType ?>', <?= $itemId ?>)" 
                             style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid var(--border-color);" 
                             onmouseover="this.style.background='var(--bg-secondary)'" 
                             onmouseout="this.style.background='transparent'">
                            <div style="font-weight: 500;">${escapeHtml(list.name)}</div>
                            <div style="font-size: 12px; color: var(--text-secondary);">
                                ${list.item_count} материалов
                            </div>
                        </div>
                    `).join('');
                }
                
                isDropdownLoaded = true;
            } else {
                content.innerHTML = `
                    <div style="padding: 15px; text-align: center; color: #dc3545;">
                        Ошибка загрузки списков
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading lists:', error);
            content.innerHTML = `
                <div style="padding: 15px; text-align: center; color: #dc3545;">
                    Ошибка загрузки списков
                </div>
            `;
        }
    }
    
    async function addToList(listId, itemType, itemId) {
        toggleListDropdown(); // Close dropdown
        
        try {
            const response = await fetch('/api/reading-lists/add-to-list', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    list_id: listId,
                    item_type: itemType,
                    item_id: itemId
                })
            });
            
            const data = await response.json();
            showMessage(data.success ? 'success' : 'error', data.message || data.error);
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'Произошла ошибка');
        }
    }
    
    function showCreateListForm() {
        document.getElementById('createListForm').style.display = 'block';
        document.getElementById('newListName').focus();
    }
    
    function hideCreateListForm() {
        document.getElementById('createListForm').style.display = 'none';
        document.getElementById('newListName').value = '';
        document.getElementById('newListDesc').value = '';
        document.getElementById('newListPublic').checked = false;
    }
    
    async function createListAndAdd(itemType, itemId) {
        const name = document.getElementById('newListName').value.trim();
        const description = document.getElementById('newListDesc').value.trim();
        const isPublic = document.getElementById('newListPublic').checked;
        
        if (!name) {
            showMessage('error', 'Введите название списка');
            return;
        }
        
        try {
            // Create list first
            const createResponse = await fetch('/api/reading-lists/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: name,
                    description: description,
                    is_public: isPublic
                })
            });
            
            const createData = await createResponse.json();
            
            if (createData.success) {
                // Add item to the new list
                const addResponse = await fetch('/api/reading-lists/add-to-list', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        list_id: createData.list_id,
                        item_type: itemType,
                        item_id: itemId
                    })
                });
                
                const addData = await addResponse.json();
                showMessage(addData.success ? 'success' : 'error', 
                           addData.success ? `Создан список "${name}" и материал добавлен` : addData.error);
                
                if (addData.success) {
                    hideCreateListForm();
                    isDropdownLoaded = false; // Reload lists
                }
            } else {
                showMessage('error', createData.error);
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'Произошла ошибка');
        }
    }
    
    function showMessage(type, message) {
        const msgDiv = document.getElementById('readingListMessage');
        msgDiv.style.display = 'block';
        msgDiv.textContent = message;
        
        if (type === 'success') {
            msgDiv.style.background = '#d4edda';
            msgDiv.style.border = '1px solid #c3e6cb';
            msgDiv.style.color = '#155724';
        } else {
            msgDiv.style.background = '#f8d7da';
            msgDiv.style.border = '1px solid #f5c6cb';
            msgDiv.style.color = '#721c24';
        }
        
        setTimeout(() => {
            msgDiv.style.display = 'none';
        }, 5000);
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.reading-list-widget')) {
            document.getElementById('listDropdown').style.display = 'none';
        }
    });
    </script>
    <?php
}
?>