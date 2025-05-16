<?php
// Simple direct test without routing
$profileId = 2;
$editUrl = "/profiles/{$profileId}/edit";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Profile Edit</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Testing Profile Edit</h1>
    <p>Profile ID: <?php echo $profileId; ?></p>
    <p>Edit URL: <?php echo $editUrl; ?></p>
    
    <button id="test-edit">Test Edit Permission</button>
    
    <div id="response"></div>
    
    <script>
    $('#test-edit').click(function() {
        $.ajax({
            url: '<?php echo $editUrl; ?>',
            method: 'GET',
            success: function(response) {
                $('#response').html('<div style="color: green;">Success! You have permission to edit.</div>');
            },
            error: function(xhr) {
                $('#response').html('<div style="color: red;">Error: ' + xhr.status + ' - ' + xhr.statusText + '</div>');
                if (xhr.status === 403) {
                    $('#response').append('<div>403 Forbidden - No permission to edit this profile.</div>');
                    console.log('Response:', xhr.responseText);
                }
            }
        });
    });
    </script>
</body>
</html>