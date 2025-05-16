<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Watchlist Test</title>
</head>
<body>
    <h1>Simple Watchlist Test</h1>
    
    <form method="POST" action="/watchlist/toggle">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?? ''; ?>">
        <input type="hidden" name="title_id" value="1">
        <button type="submit">Toggle Watchlist</button>
    </form>
    
    <hr>
    
    <h2>AJAX Test</h2>
    <button onclick="testAjax()">Test AJAX Toggle</button>
    <div id="result"></div>
    
    <script>
    function testAjax() {
        fetch('/sanctum/csrf-cookie', {
            credentials: 'same-origin'
        })
        .then(() => {
            return fetch('/watchlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    title_id: 1
                })
            });
        })
        .then(response => {
            document.getElementById('result').innerHTML = 'Status: ' + response.status;
            return response.text();
        })
        .then(text => {
            document.getElementById('result').innerHTML += '<br>' + text;
        })
        .catch(error => {
            document.getElementById('result').innerHTML = 'Error: ' + error.message;
        });
    }
    </script>
</body>
</html>