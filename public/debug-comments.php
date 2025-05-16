<?php

// Simple test to see what's happening with comments
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Comments</title>
    <meta name="csrf-token" content="test">
    <style>
        body { font-family: Arial; padding: 20px; background: #333; color: white; }
        .section { margin: 20px 0; padding: 20px; background: #444; border-radius: 5px; }
        pre { background: #222; padding: 10px; overflow: auto; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .error { color: #ff6b6b; }
        .success { color: #51cf66; }
    </style>
</head>
<body>
    <h1>Debug Comments System</h1>
    
    <div class="section">
        <h2>1. Test Basic Fetch</h2>
        <button onclick="testBasicFetch()">Test Basic Fetch</button>
        <pre id="basic-result"></pre>
    </div>
    
    <div class="section">
        <h2>2. Test with Headers</h2>
        <button onclick="testWithHeaders()">Test with Headers</button>
        <pre id="headers-result"></pre>
    </div>
    
    <div class="section">
        <h2>3. Test XMLHttpRequest</h2>
        <button onclick="testXHR()">Test XMLHttpRequest</button>
        <pre id="xhr-result"></pre>
    </div>
    
    <div class="section">
        <h2>4. Test jQuery (if available)</h2>
        <button onclick="testJQuery()">Test jQuery</button>
        <pre id="jquery-result"></pre>
    </div>
    
    <div class="section">
        <h2>5. Current Window Location</h2>
        <pre id="location-info"></pre>
    </div>
    
    <div class="section">
        <h2>6. Browser Console Logs</h2>
        <p>Open browser console (F12) to see detailed logs</p>
    </div>
    
    <script>
    // Show current location info
    document.getElementById('location-info').textContent = JSON.stringify({
        protocol: window.location.protocol,
        host: window.location.host,
        hostname: window.location.hostname,
        port: window.location.port,
        pathname: window.location.pathname,
        href: window.location.href
    }, null, 2);
    
    // 1. Basic fetch
    async function testBasicFetch() {
        const result = document.getElementById('basic-result');
        result.textContent = 'Testing...';
        
        try {
            const url = '/api/titles/1/comments';
            console.log('Basic fetch to:', url);
            
            const response = await fetch(url);
            console.log('Response:', response);
            
            const text = await response.text();
            console.log('Response text:', text);
            
            result.textContent = `Status: ${response.status}\nURL: ${response.url}\nText: ${text}`;
            
            if (response.status !== 200) {
                result.classList.add('error');
            } else {
                result.classList.add('success');
            }
        } catch (error) {
            console.error('Basic fetch error:', error);
            result.textContent = `Error: ${error.message}\nStack: ${error.stack}`;
            result.classList.add('error');
        }
    }
    
    // 2. Test with headers
    async function testWithHeaders() {
        const result = document.getElementById('headers-result');
        result.textContent = 'Testing...';
        
        try {
            const url = '/api/titles/1/comments';
            console.log('Headers fetch to:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const text = await response.text();
            
            result.textContent = `Status: ${response.status}\nHeaders: ${JSON.stringify([...response.headers.entries()], null, 2)}\nText: ${text}`;
            
            if (response.status !== 200) {
                result.classList.add('error');
            } else {
                result.classList.add('success');
            }
        } catch (error) {
            console.error('Headers fetch error:', error);
            result.textContent = `Error: ${error.message}`;
            result.classList.add('error');
        }
    }
    
    // 3. Test XMLHttpRequest
    function testXHR() {
        const result = document.getElementById('xhr-result');
        result.textContent = 'Testing...';
        
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/api/titles/1/comments', true);
        xhr.setRequestHeader('Accept', 'application/json');
        
        xhr.onload = function() {
            result.textContent = `Status: ${xhr.status}\nResponse: ${xhr.responseText}`;
            if (xhr.status !== 200) {
                result.classList.add('error');
            } else {
                result.classList.add('success');
            }
        };
        
        xhr.onerror = function() {
            result.textContent = `XHR Error: ${xhr.statusText}`;
            result.classList.add('error');
        };
        
        console.log('XHR sending request...');
        xhr.send();
    }
    
    // 4. Test jQuery (if available)
    function testJQuery() {
        const result = document.getElementById('jquery-result');
        
        if (typeof $ === 'undefined') {
            result.textContent = 'jQuery not loaded';
            result.classList.add('error');
            return;
        }
        
        result.textContent = 'Testing...';
        
        $.ajax({
            url: '/api/titles/1/comments',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                result.textContent = `Success: ${JSON.stringify(data, null, 2)}`;
                result.classList.add('success');
            },
            error: function(xhr, status, error) {
                result.textContent = `Error: ${status} - ${error}\nResponse: ${xhr.responseText}`;
                result.classList.add('error');
            }
        });
    }
    </script>
</body>
</html>