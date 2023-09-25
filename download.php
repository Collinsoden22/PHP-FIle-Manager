<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once('includes/config.php');
include_once('includes/functions.php');

if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];

    $uploaderUsername = $_SESSION['username'];
    $fileInfo = getFileInfo($filename, $uploaderUsername);

    if ($fileInfo) {
        $filePath = 'uploads/' . $uploaderUsername . '/' . $fileInfo['file_type'] . '/' . $filename;

        // Output headers to trigger file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileInfo['original_name'] . '"');
        header('Content-Length: ' . filesize($filePath));

        // Read and output the file contents
        readfile($filePath);
        exit;
    }
}

header('Location: index.php');
exit;
?>
