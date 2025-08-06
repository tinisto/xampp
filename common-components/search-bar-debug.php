<?php
/**
 * DEBUG Search Bar Component
 * Simple, aggressive version for troubleshooting
 */

function renderDebugSearchBar($config = []) {
    $defaults = [
        'placeholder' => 'Поиск...',
        'action' => '/search',
        'name' => 'query',
        'value' => $_GET[$config['name'] ?? 'query'] ?? '',
    ];
    
    $config = array_merge($defaults, $config);
    $instanceId = 'search_debug_' . time();
    ?>
    
    <div class="search-debug-container">
        <form action="<?php echo htmlspecialchars($config['action']); ?>" method="get">
            <div class="search-debug-wrapper">
                <input 
                    type="text" 
                    name="<?php echo htmlspecialchars($config['name']); ?>" 
                    placeholder="<?php echo htmlspecialchars($config['placeholder']); ?>"
                    value="<?php echo htmlspecialchars($config['value']); ?>"
                    class="search-debug-input"
                    id="search-debug-input-<?php echo $instanceId; ?>"
                >
                <button type="button" class="search-debug-clear" id="clear-debug-btn-<?php echo $instanceId; ?>">
                    ✕
                </button>
            </div>
        </form>
    </div>
    
    <style>
        .search-debug-container {
            padding: 20px 0;
        }
        
        .search-debug-wrapper {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-debug-input {
            width: 100%;
            padding: 16px 60px 16px 24px;
            border: 2px solid #ddd;
            border-radius: 50px;
            font-size: 16px;
            outline: none;
        }
        
        .search-debug-input:focus {
            border-color: #28a745;
        }
        
        .search-debug-clear {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: none;
            z-index: 999999;
        }
        
        .search-debug-clear:hover {
            background: #c82333;
        }
        
        .search-debug-clear.show {
            display: block !important;
        }
    </style>
    
    <script>
        (function() {
            const input = document.getElementById('search-debug-input-<?php echo $instanceId; ?>');
            const clearBtn = document.getElementById('clear-debug-btn-<?php echo $instanceId; ?>');
            
            console.log('DEBUG SEARCH BAR INIT:', {
                input: !!input,
                clearBtn: !!clearBtn,
                inputId: 'search-debug-input-<?php echo $instanceId; ?>',
                clearId: 'clear-debug-btn-<?php echo $instanceId; ?>'
            });
            
            if (input && clearBtn) {
                // Show/hide clear button
                input.addEventListener('input', function() {
                    console.log('Input event, value:', this.value);
                    if (this.value.length > 0) {
                        clearBtn.style.display = 'block';
                        clearBtn.classList.add('show');
                        console.log('Showing clear button');
                    } else {
                        clearBtn.style.display = 'none';
                        clearBtn.classList.remove('show');
                        console.log('Hiding clear button');
                    }
                });
                
                // Clear button click
                clearBtn.addEventListener('click', function() {
                    console.log('Clear button clicked');
                    input.value = '';
                    this.style.display = 'none';
                    this.classList.remove('show');
                    input.focus();
                });
                
                // Initialize
                if (input.value.length > 0) {
                    clearBtn.style.display = 'block';
                    clearBtn.classList.add('show');
                }
            } else {
                console.error('DEBUG SEARCH: Elements not found!');
            }
        })();
    </script>
    <?php
}
?>