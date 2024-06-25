<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = $_POST['url'];
    $file = 'youtube_urls.txt';
    file_put_contents($file, $url . PHP_EOL, FILE_APPEND);
    echo 'URL saved successfully.';
} else {
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid request.';
}
?>
