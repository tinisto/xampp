<?php
// Working pages guide
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working Pages Guide - 11-классники</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #28a745;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-top: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .working {
            background: #d4edda;
            color: #155724;
        }
        .broken {
            background: #f8d7da;
            color: #721c24;
        }
        .partial {
            background: #fff3cd;
            color: #856404;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        a {
            color: #28a745;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .note {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .issue {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Working Pages Guide</h1>
        
        <div class="note">
            <strong>Note:</strong> This guide shows which pages are working and provides alternatives for broken pages.
        </div>
        
        <h2>User Authentication <span class="status partial">Partial</span></h2>
        <table>
            <tr>
                <th>Page</th>
                <th>Status</th>
                <th>Issue</th>
                <th>Alternative</th>
            </tr>
            <tr>
                <td><a href="/login">/login</a></td>
                <td><span class="status broken">Broken</span></td>
                <td>Security error (CSRF issue)</td>
                <td><a href="/login-simple.php">Simple Login</a></td>
            </tr>
            <tr>
                <td><a href="/registration">/registration</a></td>
                <td><span class="status broken">Broken</span></td>
                <td>Only shows submit button</td>
                <td><a href="/registration-test.php">Working Registration</a> ✅</td>
            </tr>
        </table>
        
        <h2>Educational Institutions <span class="status partial">Partial</span></h2>
        <table>
            <tr>
                <th>Page</th>
                <th>Status</th>
                <th>Issue</th>
                <th>Alternative</th>
            </tr>
            <tr>
                <td><a href="/vpo-all-regions">/vpo-all-regions</a></td>
                <td><span class="status broken">Broken</span></td>
                <td>Error loading data</td>
                <td><a href="/vpo-test-standalone.php">Standalone VPO</a> ✅</td>
            </tr>
            <tr>
                <td><a href="/spo-all-regions">/spo-all-regions</a></td>
                <td><span class="status broken">Broken</span></td>
                <td>Unknown column 'id' error</td>
                <td><a href="/spo-test-direct.php">Direct SPO Test</a></td>
            </tr>
        </table>
        
        <h2>Content Pages <span class="status partial">Partial</span></h2>
        <table>
            <tr>
                <th>Page</th>
                <th>Status</th>
                <th>Issue</th>
                <th>Solution</th>
            </tr>
            <tr>
                <td><a href="/posts">/posts</a></td>
                <td><span class="status working">Working</span></td>
                <td>-</td>
                <td>Redirects to homepage ✅</td>
            </tr>
            <tr>
                <td><a href="/search">/search</a></td>
                <td><span class="status working">Working</span></td>
                <td>-</td>
                <td>Fixed redirect loop ✅</td>
            </tr>
            <tr>
                <td><a href="/write">/write</a></td>
                <td><span class="status broken">Broken</span></td>
                <td>Internal Server Error</td>
                <td>Needs investigation</td>
            </tr>
        </table>
        
        <h2>News System <span class="status partial">Partial</span></h2>
        <table>
            <tr>
                <th>Page</th>
                <th>Status</th>
                <th>Issue</th>
                <th>Solution</th>
            </tr>
            <tr>
                <td><a href="/news/novosti-obrazovaniya">/news/novosti-obrazovaniya</a></td>
                <td><span class="status broken">Broken</span></td>
                <td>Category mismatch (numeric vs text)</td>
                <td>Categories are 1,2,3,4 not text</td>
            </tr>
            <tr>
                <td>News Approval</td>
                <td><span class="status working">Working</span></td>
                <td>-</td>
                <td><a href="/approve_news_correct.php">Approval Tool</a> ✅</td>
            </tr>
        </table>
        
        <div class="issue">
            <h3>Main Issues Found:</h3>
            <ol>
                <li><strong>Template System:</strong> Many pages have nested HTML causing rendering issues</li>
                <li><strong>Database Column Names:</strong> Some queries use wrong column names</li>
                <li><strong>News Categories:</strong> System expects text categories but DB has numeric (1,2,3,4)</li>
                <li><strong>CSRF Protection:</strong> Causing security errors on forms</li>
                <li><strong>Header/Footer Includes:</strong> Breaking database connections on some pages</li>
            </ol>
        </div>
        
        <h2>Admin Tools <span class="status working">Working</span></h2>
        <ul>
            <li><a href="/site_review.php">Site Review</a> - Check site status</li>
            <li><a href="/approve_news_correct.php">News Approval</a> - Manage news (495 approved)</li>
            <li><a href="/check_news_categories.php">News Categories Check</a> - Debug news system</li>
            <li><a href="/check_regions_columns.php">Regions Check</a> - Debug regions table</li>
        </ul>
        
        <h2>Database Status</h2>
        <ul>
            <li>✅ Using correct database: <strong>11klassniki_claude</strong></li>
            <li>✅ Universities migrated: <strong>2,520</strong></li>
            <li>✅ Colleges migrated: <strong>3,363</strong></li>
            <li>✅ News approved: <strong>495</strong></li>
        </ul>
        
        <div class="note">
            <strong>Recommendation:</strong> Use the working alternative pages listed above. The main issues are with the template system creating nested HTML structures and incorrect database queries.
        </div>
        
        <p style="text-align: center; margin-top: 30px;">
            <a href="/">← Back to Homepage</a>
        </p>
    </div>
</body>
</html>