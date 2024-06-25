<?php
$file = 'youtube_urls.txt';
if (file_exists($file)) {
    $urls = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    echo implode("\n", $urls);
} else {
    header('HTTP/1.1 404 Not Found');
    echo 'File not found.';
}
?>
