<!DOCTYPE html>
<html>
<head>
    <title>Debug Duplicate Logo</title>
    <style>
        .debug-container {
            margin: 20px;
            padding: 20px;
            border: 2px solid red;
            background: #f0f0f0;
        }
        .debug-info {
            font-family: monospace;
            background: white;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="debug-container">
        <h1>Duplicate Logo Debug</h1>
        
        <div class="debug-info">
            <h3>Checking for duplicate logos...</h3>
            <?php
            // Include the template to see what's rendered
            ob_start();
            $_SERVER['REQUEST_URI'] = '/debug-duplicate-logo.php';
            $pageTitle = 'Debug';
            $greyContent1 = '<p>Test content 1</p>';
            $greyContent2 = '<p>Test content 2</p>';
            $greyContent3 = '<p>Test content 3</p>';
            $greyContent4 = '<p>Test content 4</p>';
            $greyContent5 = '<p>Test content 5</p>';
            $greyContent6 = '<p>Test content 6</p>';
            $blueContent = '<p>Test blue content</p>';
            
            // Capture what the template outputs
            include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
            $output = ob_get_clean();
            
            // Count occurrences of site logos
            $logoCount = substr_count($output, '11-классники');
            $siteIconCount = substr_count($output, 'site-icon');
            $renderSiteIconCount = substr_count($output, 'renderSiteIcon');
            
            echo "<p>Total occurrences of '11-классники': $logoCount</p>";
            echo "<p>Total occurrences of 'site-icon': $siteIconCount</p>";
            echo "<p>Total occurrences of 'renderSiteIcon': $renderSiteIconCount</p>";
            
            // Look for positioned elements
            preg_match_all('/position:\s*(fixed|absolute)[^}]*11-классники/i', $output, $positionedLogos);
            echo "<p>Positioned logos found: " . count($positionedLogos[0]) . "</p>";
            
            // Look for multiple body tags or weird structure
            $bodyCount = substr_count($output, '<body');
            echo "<p>Body tags found: $bodyCount</p>";
            
            // Check for JavaScript that might create elements
            preg_match_all('/createElement.*11-классники/i', $output, $jsCreatedLogos);
            echo "<p>JS created logos: " . count($jsCreatedLogos[0]) . "</p>";
            ?>
        </div>
        
        <div class="debug-info">
            <h3>JavaScript Debug</h3>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Count elements with site logo text
                    const logoElements = document.querySelectorAll('*');
                    let count = 0;
                    let positions = [];
                    
                    logoElements.forEach(el => {
                        if (el.textContent === '11-классники' && el.children.length === 0) {
                            count++;
                            const rect = el.getBoundingClientRect();
                            const computed = window.getComputedStyle(el);
                            const parent = el.parentElement;
                            
                            positions.push({
                                text: el.textContent,
                                tag: el.tagName,
                                class: el.className,
                                id: el.id,
                                parent: parent ? parent.tagName + '.' + parent.className : 'none',
                                position: computed.position,
                                top: rect.top,
                                left: rect.left,
                                zIndex: computed.zIndex
                            });
                        }
                    });
                    
                    console.log('Logo elements found:', count);
                    console.log('Positions:', positions);
                    
                    // Display in page
                    const debugDiv = document.createElement('div');
                    debugDiv.innerHTML = '<h4>JavaScript Analysis:</h4>' + 
                        '<p>Logo elements found: ' + count + '</p>' +
                        '<pre>' + JSON.stringify(positions, null, 2) + '</pre>';
                    document.querySelector('.debug-container').appendChild(debugDiv);
                });
            </script>
        </div>
    </div>
</body>
</html>