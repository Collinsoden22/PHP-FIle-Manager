<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once('includes/config.php');
include_once('includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';

    // Ensure the uploads directory exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['file'];

    // Validate file size (e.g., 5MB limit)
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxFileSize) {
        echo 'File size exceeds the limit.';
        exit;
    }

    // Validate MIME types (e.g., allow jpg, png, pdf)
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file['type'], $allowedMimeTypes)) {
        $msg = 'Invalid file type. Allowed types: jpg, png, pdf.';
        header("Location: index.php?msg=$msg");
        exit;
    }

    // Get the uploader's username
    $uploaderUsername = $_SESSION['username'];

    // Generate a unique filename
    $timestamp = date('Ymd_His');
    $uuid = uniqid();
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = "{$timestamp}_{$uuid}.{$extension}";

    $targetPath = $uploadDir . $uploaderUsername . '/' . $file['type'] . '/';

    // Ensure the user-specific directory exists
    if (!file_exists($targetPath)) {
        mkdir($targetPath, 0777, true);
    }

    $targetPath .= $newFilename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        saveFileInfoToDatabase($file['name'], $newFilename, $file['size'], $file['type'],  $uploaderUsername);

        // Redirect back to the main page
        $msg = 'Your file has been uploaded successfully.';
        header("Location: index.php?msg=$msg");
        exit;
    } else {
        // Redirect back to the main page
        $msg = 'Your file could not be uploaded.';
        header("Location: index.php?msg=$msg");
    }
} else {
    // Redirect back to the main page
    $msg = 'Error uploading file, please try again.';
    header("Location: index.php?msg=$msg");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload File</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Upload File</h1>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="mb-4">
            <input type="file" name="file" accept=".jpg, .png, .pdf" class="hidden" id="file-upload">
            <label for="file-upload" class="cursor-pointer bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                Choose File
            </label>
            <span id="file-name" class="ml-3"></span>
            <input type="submit" value="Upload" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
        </form>
        <a href="index.php" class="text-blue-500 hover:underline">Back to Home</a>
    </div>

    <script>
        // Display the selected file name in the form
        const fileUpload = document.getElementById('file-upload');
        const fileNameDisplay = document.getElementById('file-name');

        fileUpload.addEventListener('change', () => {
            if (fileUpload.files.length > 0) {
                fileNameDisplay.textContent = fileUpload.files[0].name;
            } else {
                fileNameDisplay.textContent = '';
            }
        });
    </script>
</body>
</html>
