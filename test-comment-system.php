<?php
/**
 * Comment System Test Suite
 * Tests all features of the comment system
 */

session_start();

// Set test mode
define('TEST_MODE', true);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment System Test Suite</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .test-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .test-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .test-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }
        
        .test-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-pending { background: #e3f2fd; color: #1976d2; }
        .status-running { background: #fff3e0; color: #f57c00; }
        .status-success { background: #e8f5e9; color: #388e3c; }
        .status-failed { background: #ffebee; color: #d32f2f; }
        
        .test-steps {
            margin-top: 15px;
        }
        
        .test-step {
            padding: 10px;
            margin: 5px 0;
            border-left: 3px solid #ddd;
            background: #f9f9f9;
        }
        
        .step-success { border-color: #4caf50; background: #f1f8e9; }
        .step-failed { border-color: #f44336; background: #ffebee; }
        .step-running { border-color: #ff9800; background: #fff8e1; }
        
        .test-controls {
            margin-top: 30px;
            text-align: center;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .results {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .result-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .result-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .result-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .result-label {
            color: #666;
            font-size: 14px;
        }
        
        .test-log {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 13px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .log-entry {
            margin: 2px 0;
        }
        
        .log-success { color: #4ec9b0; }
        .log-error { color: #f48771; }
        .log-warning { color: #dcdcaa; }
        .log-info { color: #9cdcfe; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Comment System Test Suite</h1>
        
        <!-- Test Sections -->
        <div id="test-sections">
            <div class="test-section" data-test="basic-comment">
                <div class="test-header">
                    <h3 class="test-title">1. Basic Comment Posting</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="threaded-replies">
                <div class="test-header">
                    <h3 class="test-title">2. Threaded Replies</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="like-dislike">
                <div class="test-header">
                    <h3 class="test-title">3. Like/Dislike System</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="comment-editing">
                <div class="test-header">
                    <h3 class="test-title">4. Comment Editing</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="reporting">
                <div class="test-header">
                    <h3 class="test-title">5. Comment Reporting</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="mentions">
                <div class="test-header">
                    <h3 class="test-title">6. User Mentions</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="rate-limiting">
                <div class="test-header">
                    <h3 class="test-title">7. Rate Limiting</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="analytics-api">
                <div class="test-header">
                    <h3 class="test-title">8. Analytics API</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="performance">
                <div class="test-header">
                    <h3 class="test-title">9. Performance Test</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
            
            <div class="test-section" data-test="security">
                <div class="test-header">
                    <h3 class="test-title">10. Security Tests</h3>
                    <span class="test-status status-pending">Pending</span>
                </div>
                <div class="test-steps"></div>
            </div>
        </div>
        
        <!-- Test Controls -->
        <div class="test-controls">
            <button class="btn btn-primary" onclick="runAllTests()">Run All Tests</button>
            <button class="btn btn-secondary" onclick="runSelectedTests()">Run Selected</button>
            <button class="btn btn-secondary" onclick="clearResults()">Clear Results</button>
        </div>
        
        <!-- Results Section -->
        <div class="results" style="display: none;">
            <h2>Test Results</h2>
            <div class="result-summary">
                <div class="result-card">
                    <div class="result-number" id="total-tests">0</div>
                    <div class="result-label">Total Tests</div>
                </div>
                <div class="result-card">
                    <div class="result-number" id="passed-tests" style="color: #4caf50;">0</div>
                    <div class="result-label">Passed</div>
                </div>
                <div class="result-card">
                    <div class="result-number" id="failed-tests" style="color: #f44336;">0</div>
                    <div class="result-label">Failed</div>
                </div>
                <div class="result-card">
                    <div class="result-number" id="test-duration">0s</div>
                    <div class="result-label">Duration</div>
                </div>
            </div>
            <div class="test-log" id="test-log"></div>
        </div>
    </div>
    
    <script>
        const API_BASE = '/api/comments';
        const TEST_ENTITY_TYPE = 'test';
        const TEST_ENTITY_ID = 99999;
        
        let testResults = {
            total: 0,
            passed: 0,
            failed: 0,
            startTime: null,
            endTime: null
        };
        
        // Test implementations
        const tests = {
            'basic-comment': async function() {
                const steps = [];
                
                // Step 1: Post a comment
                steps.push('Posting test comment...');
                updateTestStep('basic-comment', steps, 'running');
                
                const response = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'Test User',
                        email: 'test@example.com',
                        comment: 'This is a test comment posted at ' + new Date().toISOString()
                    })
                });
                
                const result = await response.json();
                
                if (result.success && result.comment) {
                    steps.push('✓ Comment posted successfully');
                    
                    // Step 2: Verify comment appears
                    steps.push('Verifying comment appears in list...');
                    updateTestStep('basic-comment', steps, 'running');
                    
                    const listResponse = await fetch(`${API_BASE}/threaded.php?entity_type=${TEST_ENTITY_TYPE}&entity_id=${TEST_ENTITY_ID}`);
                    const listResult = await listResponse.json();
                    
                    if (listResult.success && listResult.comments.some(c => c.id === result.comment.id)) {
                        steps.push('✓ Comment found in list');
                        return { success: true, steps };
                    } else {
                        steps.push('✗ Comment not found in list');
                        return { success: false, steps };
                    }
                } else {
                    steps.push('✗ Failed to post comment: ' + (result.error || 'Unknown error'));
                    return { success: false, steps };
                }
            },
            
            'threaded-replies': async function() {
                const steps = [];
                
                // First post a parent comment
                steps.push('Posting parent comment...');
                updateTestStep('threaded-replies', steps, 'running');
                
                const parentResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'Parent User',
                        comment: 'Parent comment for reply test'
                    })
                });
                
                const parentResult = await parentResponse.json();
                
                if (!parentResult.success) {
                    steps.push('✗ Failed to create parent comment');
                    return { success: false, steps };
                }
                
                steps.push('✓ Parent comment created');
                const parentId = parentResult.comment.id;
                
                // Post a reply
                steps.push('Posting reply to parent comment...');
                updateTestStep('threaded-replies', steps, 'running');
                
                const replyResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'Reply User',
                        comment: 'This is a reply to the parent comment',
                        parent_id: parentId
                    })
                });
                
                const replyResult = await replyResponse.json();
                
                if (replyResult.success && replyResult.comment.parent_id === parentId) {
                    steps.push('✓ Reply posted successfully');
                    
                    // Verify threaded structure
                    steps.push('Verifying threaded structure...');
                    const listResponse = await fetch(`${API_BASE}/threaded.php?entity_type=${TEST_ENTITY_TYPE}&entity_id=${TEST_ENTITY_ID}`);
                    const listResult = await listResponse.json();
                    
                    const parent = listResult.comments.find(c => c.id === parentId);
                    if (parent && parent.replies && parent.replies.length > 0) {
                        steps.push('✓ Reply appears in threaded structure');
                        return { success: true, steps };
                    } else {
                        steps.push('✗ Reply not found in threaded structure');
                        return { success: false, steps };
                    }
                } else {
                    steps.push('✗ Failed to post reply');
                    return { success: false, steps };
                }
            },
            
            'like-dislike': async function() {
                const steps = [];
                
                // Create a comment to like
                steps.push('Creating comment for like test...');
                updateTestStep('like-dislike', steps, 'running');
                
                const commentResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'Like Test User',
                        comment: 'Comment for like/dislike test'
                    })
                });
                
                const commentResult = await commentResponse.json();
                if (!commentResult.success) {
                    steps.push('✗ Failed to create test comment');
                    return { success: false, steps };
                }
                
                const commentId = commentResult.comment.id;
                steps.push('✓ Test comment created');
                
                // Test liking
                steps.push('Testing like functionality...');
                updateTestStep('like-dislike', steps, 'running');
                
                const likeResponse = await fetch(`${API_BASE}/like.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        comment_id: commentId,
                        vote_type: 'like'
                    })
                });
                
                const likeResult = await likeResponse.json();
                
                if (likeResult.success && likeResult.likes > 0) {
                    steps.push('✓ Like registered successfully');
                    
                    // Test toggle (unlike)
                    steps.push('Testing like toggle (unlike)...');
                    const unlikeResponse = await fetch(`${API_BASE}/like.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            comment_id: commentId,
                            vote_type: 'like'
                        })
                    });
                    
                    const unlikeResult = await unlikeResponse.json();
                    
                    if (unlikeResult.success && unlikeResult.likes === 0) {
                        steps.push('✓ Like toggle works correctly');
                        return { success: true, steps };
                    } else {
                        steps.push('✗ Like toggle failed');
                        return { success: false, steps };
                    }
                } else {
                    steps.push('✗ Failed to register like');
                    return { success: false, steps };
                }
            },
            
            'comment-editing': async function() {
                const steps = [];
                
                // Create a comment to edit
                steps.push('Creating comment for edit test...');
                updateTestStep('comment-editing', steps, 'running');
                
                const commentResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'Edit Test User',
                        comment: 'Original comment text'
                    })
                });
                
                const commentResult = await commentResponse.json();
                if (!commentResult.success) {
                    steps.push('✗ Failed to create test comment');
                    return { success: false, steps };
                }
                
                const commentId = commentResult.comment.id;
                steps.push('✓ Test comment created');
                
                // Edit the comment
                steps.push('Editing comment...');
                updateTestStep('comment-editing', steps, 'running');
                
                const editResponse = await fetch(`${API_BASE}/edit.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        comment_id: commentId,
                        comment_text: 'Edited comment text - updated at ' + new Date().toLocaleTimeString()
                    })
                });
                
                const editResult = await editResponse.json();
                
                if (editResult.success) {
                    steps.push('✓ Comment edited successfully');
                    steps.push(`✓ Edit count: ${editResult.edit_count}`);
                    return { success: true, steps };
                } else {
                    steps.push('✗ Failed to edit comment: ' + editResult.error);
                    return { success: false, steps };
                }
            },
            
            'reporting': async function() {
                const steps = [];
                
                // Create a comment to report
                steps.push('Creating comment for report test...');
                updateTestStep('reporting', steps, 'running');
                
                const commentResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'Spam User',
                        comment: 'This is a test spam comment'
                    })
                });
                
                const commentResult = await commentResponse.json();
                if (!commentResult.success) {
                    steps.push('✗ Failed to create test comment');
                    return { success: false, steps };
                }
                
                const commentId = commentResult.comment.id;
                steps.push('✓ Test comment created');
                
                // Report the comment
                steps.push('Reporting comment as spam...');
                updateTestStep('reporting', steps, 'running');
                
                const reportResponse = await fetch(`${API_BASE}/report.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        comment_id: commentId,
                        reason: 'spam',
                        details: 'This is a test report'
                    })
                });
                
                const reportResult = await reportResponse.json();
                
                if (reportResult.success) {
                    steps.push('✓ Comment reported successfully');
                    return { success: true, steps };
                } else {
                    steps.push('✗ Failed to report comment: ' + reportResult.error);
                    return { success: false, steps };
                }
            },
            
            'mentions': async function() {
                const steps = [];
                
                // Create a comment with mention
                steps.push('Creating comment with @mention...');
                updateTestStep('mentions', steps, 'running');
                
                const commentResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'Mention Test User',
                        comment: 'Hello @testuser, this is a mention test! Also mentioning @admin here.',
                        email: 'mentioner@example.com'
                    })
                });
                
                const commentResult = await commentResponse.json();
                
                if (commentResult.success) {
                    steps.push('✓ Comment with mentions posted successfully');
                    
                    // Verify mention formatting
                    const listResponse = await fetch(`${API_BASE}/threaded.php?entity_type=${TEST_ENTITY_TYPE}&entity_id=${TEST_ENTITY_ID}`);
                    const listResult = await listResponse.json();
                    
                    const comment = listResult.comments.find(c => c.id === commentResult.comment.id);
                    if (comment && comment.comment_text.includes('@testuser')) {
                        steps.push('✓ Mentions preserved in comment text');
                        return { success: true, steps };
                    } else {
                        steps.push('✗ Mentions not found in retrieved comment');
                        return { success: false, steps };
                    }
                } else {
                    steps.push('✗ Failed to post comment with mentions');
                    return { success: false, steps };
                }
            },
            
            'rate-limiting': async function() {
                const steps = [];
                
                steps.push('Testing rate limiting (3 comments/minute)...');
                updateTestStep('rate-limiting', steps, 'running');
                
                let successCount = 0;
                let blocked = false;
                
                // Try to post 4 comments quickly
                for (let i = 1; i <= 4; i++) {
                    steps.push(`Posting comment ${i}...`);
                    updateTestStep('rate-limiting', steps, 'running');
                    
                    const response = await fetch(`${API_BASE}/add.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            entity_type: TEST_ENTITY_TYPE,
                            entity_id: TEST_ENTITY_ID,
                            author: 'Rate Limit Test',
                            comment: `Rate limit test comment ${i}`
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        successCount++;
                        steps.push(`✓ Comment ${i} posted`);
                    } else if (response.status === 429 || result.error.includes('Слишком много')) {
                        blocked = true;
                        steps.push(`✓ Comment ${i} blocked by rate limiter`);
                        break;
                    } else {
                        steps.push(`✗ Comment ${i} failed: ${result.error}`);
                    }
                }
                
                if (blocked && successCount === 3) {
                    steps.push('✓ Rate limiting working correctly (3 allowed, 4th blocked)');
                    return { success: true, steps };
                } else {
                    steps.push(`✗ Rate limiting not working properly (${successCount} posted, blocked: ${blocked})`);
                    return { success: false, steps };
                }
            },
            
            'analytics-api': async function() {
                const steps = [];
                
                // Test various analytics endpoints
                const endpoints = [
                    { type: 'summary', name: 'Summary Statistics' },
                    { type: 'timeline', name: 'Timeline Data' },
                    { type: 'sentiment', name: 'Sentiment Analysis' },
                    { type: 'top_threads', name: 'Top Threads' },
                    { type: 'user_activity', name: 'User Activity' }
                ];
                
                for (const endpoint of endpoints) {
                    steps.push(`Testing ${endpoint.name} endpoint...`);
                    updateTestStep('analytics-api', steps, 'running');
                    
                    try {
                        const response = await fetch(`${API_BASE}/analytics.php?type=${endpoint.type}&period=7d`);
                        
                        if (response.status === 403) {
                            steps.push(`⚠️ ${endpoint.name}: Access denied (admin only)`);
                            continue;
                        }
                        
                        const result = await response.json();
                        
                        if (result.success && result.data) {
                            steps.push(`✓ ${endpoint.name}: Data retrieved`);
                        } else {
                            steps.push(`✗ ${endpoint.name}: Failed`);
                            return { success: false, steps };
                        }
                    } catch (error) {
                        steps.push(`✗ ${endpoint.name}: Error - ${error.message}`);
                        return { success: false, steps };
                    }
                }
                
                return { success: true, steps };
            },
            
            'performance': async function() {
                const steps = [];
                
                // Test loading performance
                steps.push('Testing comment loading performance...');
                updateTestStep('performance', steps, 'running');
                
                const startTime = performance.now();
                
                const response = await fetch(`${API_BASE}/threaded.php?entity_type=${TEST_ENTITY_TYPE}&entity_id=${TEST_ENTITY_ID}&limit=50`);
                const result = await response.json();
                
                const loadTime = performance.now() - startTime;
                
                steps.push(`✓ Comments loaded in ${loadTime.toFixed(2)}ms`);
                
                if (loadTime < 1000) {
                    steps.push('✓ Performance is good (< 1 second)');
                } else if (loadTime < 3000) {
                    steps.push('⚠️ Performance is acceptable (< 3 seconds)');
                } else {
                    steps.push('✗ Performance needs improvement (> 3 seconds)');
                    return { success: false, steps };
                }
                
                // Test concurrent requests
                steps.push('Testing concurrent request handling...');
                updateTestStep('performance', steps, 'running');
                
                const concurrentStart = performance.now();
                const promises = [];
                
                for (let i = 0; i < 5; i++) {
                    promises.push(fetch(`${API_BASE}/threaded.php?entity_type=${TEST_ENTITY_TYPE}&entity_id=${TEST_ENTITY_ID}`));
                }
                
                await Promise.all(promises);
                const concurrentTime = performance.now() - concurrentStart;
                
                steps.push(`✓ 5 concurrent requests completed in ${concurrentTime.toFixed(2)}ms`);
                
                return { success: true, steps };
            },
            
            'security': async function() {
                const steps = [];
                
                // Test XSS prevention
                steps.push('Testing XSS prevention...');
                updateTestStep('security', steps, 'running');
                
                const xssPayload = '<script>alert("XSS")</script>';
                const xssResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: 'XSS Tester',
                        comment: xssPayload
                    })
                });
                
                const xssResult = await xssResponse.json();
                
                if (xssResult.success) {
                    // Check if script tags are escaped
                    const listResponse = await fetch(`${API_BASE}/threaded.php?entity_type=${TEST_ENTITY_TYPE}&entity_id=${TEST_ENTITY_ID}`);
                    const listResult = await listResponse.json();
                    
                    const comment = listResult.comments.find(c => c.author_of_comment === 'XSS Tester');
                    if (comment && !comment.comment_text.includes('<script>')) {
                        steps.push('✓ XSS payload properly escaped');
                    } else {
                        steps.push('✗ XSS payload not escaped properly');
                        return { success: false, steps };
                    }
                }
                
                // Test SQL injection prevention
                steps.push('Testing SQL injection prevention...');
                updateTestStep('security', steps, 'running');
                
                const sqlPayload = "'; DROP TABLE comments; --";
                const sqlResponse = await fetch(`${API_BASE}/add.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        entity_type: TEST_ENTITY_TYPE,
                        entity_id: TEST_ENTITY_ID,
                        author: sqlPayload,
                        comment: 'SQL injection test'
                    })
                });
                
                const sqlResult = await sqlResponse.json();
                
                // If we can still load comments, the table wasn't dropped
                const checkResponse = await fetch(`${API_BASE}/threaded.php?entity_type=${TEST_ENTITY_TYPE}&entity_id=${TEST_ENTITY_ID}`);
                if (checkResponse.ok) {
                    steps.push('✓ SQL injection prevented');
                } else {
                    steps.push('✗ SQL injection protection failed');
                    return { success: false, steps };
                }
                
                // Test authentication on protected endpoints
                steps.push('Testing authentication on admin endpoints...');
                updateTestStep('security', steps, 'running');
                
                const adminResponse = await fetch(`${API_BASE}/analytics.php?type=summary`);
                if (adminResponse.status === 403) {
                    steps.push('✓ Admin endpoints properly protected');
                } else {
                    steps.push('⚠️ Admin endpoint accessible without authentication');
                }
                
                return { success: true, steps };
            }
        };
        
        function updateTestStep(testName, steps, status) {
            const section = document.querySelector(`[data-test="${testName}"]`);
            const statusEl = section.querySelector('.test-status');
            const stepsEl = section.querySelector('.test-steps');
            
            statusEl.className = `test-status status-${status}`;
            statusEl.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            
            stepsEl.innerHTML = steps.map((step, index) => {
                let stepClass = '';
                if (step.includes('✓')) stepClass = 'step-success';
                else if (step.includes('✗')) stepClass = 'step-failed';
                else if (index === steps.length - 1 && status === 'running') stepClass = 'step-running';
                
                return `<div class="test-step ${stepClass}">${step}</div>`;
            }).join('');
        }
        
        function log(message, type = 'info') {
            const logEl = document.getElementById('test-log');
            const timestamp = new Date().toLocaleTimeString();
            logEl.innerHTML += `<div class="log-entry log-${type}">[${timestamp}] ${message}</div>`;
            logEl.scrollTop = logEl.scrollHeight;
        }
        
        async function runTest(testName) {
            log(`Starting test: ${testName}`, 'info');
            updateTestStep(testName, [], 'running');
            
            try {
                const result = await tests[testName]();
                
                if (result.success) {
                    updateTestStep(testName, result.steps, 'success');
                    log(`✓ Test passed: ${testName}`, 'success');
                    testResults.passed++;
                } else {
                    updateTestStep(testName, result.steps, 'failed');
                    log(`✗ Test failed: ${testName}`, 'error');
                    testResults.failed++;
                }
            } catch (error) {
                updateTestStep(testName, [`✗ Test error: ${error.message}`], 'failed');
                log(`✗ Test error in ${testName}: ${error.message}`, 'error');
                testResults.failed++;
            }
            
            testResults.total++;
            updateResults();
        }
        
        async function runAllTests() {
            clearResults();
            document.querySelector('.results').style.display = 'block';
            testResults.startTime = Date.now();
            
            log('Starting all tests...', 'info');
            
            for (const testName of Object.keys(tests)) {
                await runTest(testName);
                // Small delay between tests
                await new Promise(resolve => setTimeout(resolve, 500));
            }
            
            testResults.endTime = Date.now();
            log('All tests completed!', 'info');
            updateResults();
        }
        
        async function runSelectedTests() {
            // This could be extended to allow selecting specific tests
            alert('Feature not implemented yet. Use "Run All Tests" for now.');
        }
        
        function clearResults() {
            testResults = {
                total: 0,
                passed: 0,
                failed: 0,
                startTime: null,
                endTime: null
            };
            
            document.getElementById('test-log').innerHTML = '';
            document.querySelectorAll('.test-section').forEach(section => {
                section.querySelector('.test-status').className = 'test-status status-pending';
                section.querySelector('.test-status').textContent = 'Pending';
                section.querySelector('.test-steps').innerHTML = '';
            });
            
            updateResults();
        }
        
        function updateResults() {
            document.getElementById('total-tests').textContent = testResults.total;
            document.getElementById('passed-tests').textContent = testResults.passed;
            document.getElementById('failed-tests').textContent = testResults.failed;
            
            if (testResults.startTime && testResults.endTime) {
                const duration = (testResults.endTime - testResults.startTime) / 1000;
                document.getElementById('test-duration').textContent = duration.toFixed(1) + 's';
            }
        }
        
        // Initialize
        log('Test suite loaded and ready', 'info');
    </script>
</body>
</html>