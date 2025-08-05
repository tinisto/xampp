<?php
/**
 * Web-based Cleanup Review Interface
 * Provides a visual interface to review and manage cleanup operations
 */

session_start();

// Define file categories
$categories = [
    'under_construction' => [
        'title' => 'Under Construction Files',
        'files' => [
            'pages/about_us_under_construction.php',
            'pages/privacy_policy_under_construction.php',
            'pages/search_under_construction.php',
            'pages/stats_under_construction.php'
        ]
    ],
    'python_scripts' => [
        'title' => 'Python Upload/Deploy Scripts',
        'files' => []
    ],
    'test_files' => [
        'title' => 'Test Files',
        'files' => []
    ],
    'migrations' => [
        'title' => 'Migration Files',
        'files' => []
    ],
    'cleanup_directory' => [
        'title' => '_cleanup Directory',
        'files' => []
    ]
];

// Function to scan for files
function scanForFiles($pattern, $baseDir = '.') {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && fnmatch($pattern, $file->getFilename())) {
            $files[] = str_replace('./', '', $file->getPathname());
        }
    }
    
    return $files;
}

// Populate dynamic categories
$categories['python_scripts']['files'] = array_merge(
    scanForFiles('upload-*.py'),
    scanForFiles('fix-*.py'),
    scanForFiles('verify-*.py'),
    scanForFiles('move-*.py'),
    scanForFiles('find-*.py')
);

$categories['test_files']['files'] = array_merge(
    scanForFiles('test-*.php'),
    scanForFiles('test_*.php'),
    scanForFiles('check-*.php'),
    scanForFiles('debug-*.php')
);

$categories['migrations']['files'] = scanForFiles('*migration*.php');

// Get all files in _cleanup directory
if (is_dir('_cleanup')) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('_cleanup', RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $categories['cleanup_directory']['files'][] = str_replace('./', '', $file->getPathname());
        }
    }
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'preview':
            $file = $_POST['file'];
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $lines = substr_count($content, "\n") + 1;
                $size = filesize($file);
                $modified = date('Y-m-d H:i:s', filemtime($file));
                
                // Get first 50 lines for preview
                $preview = implode("\n", array_slice(explode("\n", $content), 0, 50));
                if ($lines > 50) {
                    $preview .= "\n\n... (" . ($lines - 50) . " more lines)";
                }
                
                echo json_encode([
                    'success' => true,
                    'preview' => htmlspecialchars($preview),
                    'lines' => $lines,
                    'size' => number_format($size) . ' bytes',
                    'modified' => $modified
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'File not found']);
            }
            exit;
            
        case 'delete':
            $file = $_POST['file'];
            if (file_exists($file)) {
                if (unlink($file)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to delete file']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'File not found']);
            }
            exit;
            
        case 'archive':
            $file = $_POST['file'];
            if (file_exists($file)) {
                $archiveDir = '_archive/' . date('Y-m-d');
                if (!is_dir($archiveDir)) {
                    mkdir($archiveDir, 0755, true);
                }
                
                $destination = $archiveDir . '/' . basename($file);
                if (rename($file, $destination)) {
                    echo json_encode(['success' => true, 'archived_to' => $destination]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to archive file']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'File not found']);
            }
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleanup Review Interface</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        
        .category {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .category h2 {
            margin: 0 0 15px 0;
            color: #444;
            font-size: 20px;
        }
        
        .file-list {
            display: grid;
            gap: 10px;
        }
        
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        
        .file-name {
            flex: 1;
            font-family: monospace;
            font-size: 14px;
            color: #495057;
        }
        
        .file-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 5px 15px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .btn-preview {
            background: #007bff;
            color: white;
        }
        
        .btn-preview:hover {
            background: #0056b3;
        }
        
        .btn-archive {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-archive:hover {
            background: #e0a800;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            position: relative;
            background: white;
            margin: 50px auto;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            border-radius: 8px;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-title {
            margin: 0;
            font-size: 18px;
        }
        
        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #999;
        }
        
        .close:hover {
            color: #333;
        }
        
        .file-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .file-info label {
            font-weight: bold;
            color: #666;
        }
        
        .file-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
            overflow-x: auto;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .stats {
            background: #e9ecef;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 30px;
            display: flex;
            gap: 30px;
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .empty-category {
            color: #999;
            font-style: italic;
        }
        
        .bulk-actions {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .btn-bulk {
            padding: 10px 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Website Cleanup Review Interface</h1>
        
        <div class="stats">
            <div class="stat-item">
                <span class="stat-label">Total Files Found</span>
                <span class="stat-value" id="total-files">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Files Reviewed</span>
                <span class="stat-value" id="files-reviewed">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Files Deleted</span>
                <span class="stat-value" id="files-deleted">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Files Archived</span>
                <span class="stat-value" id="files-archived">0</span>
            </div>
        </div>
        
        <div class="bulk-actions">
            <button class="btn btn-bulk btn-archive" onclick="archiveAll('python_scripts')">
                Archive All Python Scripts
            </button>
            <button class="btn btn-bulk btn-delete" onclick="deleteAll('test_files')">
                Delete All Test Files
            </button>
        </div>
        
        <?php
        $totalFiles = 0;
        foreach ($categories as $categoryId => $category):
            $fileCount = count($category['files']);
            $totalFiles += $fileCount;
        ?>
        <div class="category" id="category-<?php echo $categoryId; ?>">
            <h2><?php echo $category['title']; ?> (<?php echo $fileCount; ?>)</h2>
            
            <?php if (empty($category['files'])): ?>
                <p class="empty-category">No files found in this category</p>
            <?php else: ?>
                <div class="file-list">
                    <?php foreach ($category['files'] as $file): ?>
                    <div class="file-item" data-file="<?php echo htmlspecialchars($file); ?>">
                        <span class="file-name"><?php echo htmlspecialchars($file); ?></span>
                        <div class="file-actions">
                            <button class="btn btn-preview" onclick="previewFile('<?php echo htmlspecialchars($file); ?>')">
                                Preview
                            </button>
                            <button class="btn btn-archive" onclick="archiveFile('<?php echo htmlspecialchars($file); ?>')">
                                Archive
                            </button>
                            <button class="btn btn-delete" onclick="deleteFile('<?php echo htmlspecialchars($file); ?>')">
                                Delete
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div id="preview-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="preview-title">File Preview</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="file-info">
                <label>File:</label><span id="preview-filename"></span>
                <label>Size:</label><span id="preview-size"></span>
                <label>Lines:</label><span id="preview-lines"></span>
                <label>Modified:</label><span id="preview-modified"></span>
            </div>
            <div class="file-preview" id="preview-content"></div>
        </div>
    </div>
    
    <script>
        // Update stats
        document.getElementById('total-files').textContent = <?php echo $totalFiles; ?>;
        
        let filesDeleted = 0;
        let filesArchived = 0;
        let filesReviewed = new Set();
        
        function previewFile(file) {
            filesReviewed.add(file);
            document.getElementById('files-reviewed').textContent = filesReviewed.size;
            
            fetch('cleanup-review-interface.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=preview&file=' + encodeURIComponent(file)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('preview-filename').textContent = file;
                    document.getElementById('preview-size').textContent = data.size;
                    document.getElementById('preview-lines').textContent = data.lines;
                    document.getElementById('preview-modified').textContent = data.modified;
                    document.getElementById('preview-content').textContent = data.preview;
                    document.getElementById('preview-modal').style.display = 'block';
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
        
        function deleteFile(file) {
            if (!confirm('Are you sure you want to delete ' + file + '?')) {
                return;
            }
            
            fetch('cleanup-review-interface.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=delete&file=' + encodeURIComponent(file)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('[data-file="' + file + '"]').remove();
                    filesDeleted++;
                    document.getElementById('files-deleted').textContent = filesDeleted;
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
        
        function archiveFile(file) {
            fetch('cleanup-review-interface.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=archive&file=' + encodeURIComponent(file)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const fileItem = document.querySelector('[data-file="' + file + '"]');
                    fileItem.style.opacity = '0.5';
                    fileItem.querySelector('.file-name').textContent += ' → ' + data.archived_to;
                    fileItem.querySelectorAll('button').forEach(btn => btn.disabled = true);
                    filesArchived++;
                    document.getElementById('files-archived').textContent = filesArchived;
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
        
        function closeModal() {
            document.getElementById('preview-modal').style.display = 'none';
        }
        
        function archiveAll(categoryId) {
            if (!confirm('Archive all files in this category?')) {
                return;
            }
            
            const category = document.getElementById('category-' + categoryId);
            const files = category.querySelectorAll('.file-item');
            
            files.forEach(fileItem => {
                const file = fileItem.getAttribute('data-file');
                archiveFile(file);
            });
        }
        
        function deleteAll(categoryId) {
            if (!confirm('Delete all files in this category? This cannot be undone!')) {
                return;
            }
            
            const category = document.getElementById('category-' + categoryId);
            const files = category.querySelectorAll('.file-item');
            
            files.forEach(fileItem => {
                const file = fileItem.getAttribute('data-file');
                deleteFile(file);
            });
        }
        
        // Click outside modal to close
        window.onclick = function(event) {
            if (event.target == document.getElementById('preview-modal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>