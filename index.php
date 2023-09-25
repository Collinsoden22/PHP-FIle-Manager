<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include_once('includes/functions.php');
// Fetch uploaded files
$uploadedFiles = listUploadedFiles($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">File Manager</h1>
        <p class="mb-4">Welcome, <?php echo $_SESSION['username']; ?>!</p>

        <!-- File Upload Form -->
        <form action="upload.php" method="post" enctype="multipart/form-data"
        class="mb-4">
        <?php
         if($_GET['msg']) {
            $msg = htmlentities($_GET['msg']);
            echo "<p class='text-green-900 text-center'>$msg <p>";
        }
        ?>
            <input type="file" name="file" accept=".jpg, .png, .pdf" class="py-2 px-3 border rounded">
            <input type="submit" value="Upload" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
        </form>

        <!-- List of Uploaded Files -->
        <?php
            if($uploadedFiles){
        ?>
        <h2 class="text-xl font-semibold mb-2">Uploaded Files:</h2>
        <?php if (!empty($uploadedFiles)): ?>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">File Name</th>
                    <th class="px-4 py-2">Date Uploaded</th>
                    <th class="px-4 py-2">File Size</th>
                    <th class="px-4 py-2">File Type</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($uploadedFiles as $file): ?>
                <tr>
                    <td class="px-4 py-2">
                        <?php echo $file['original_name']; ?>
                    </td>
                    <td class="px-4 py-2">
                        <?php echo $file['upload_timestamp']; ?>
                    </td>
                    <td class="px-4 py-2">
                        <?php echo formatFileSize($file['file_size']); ?>
                    </td>
                    <td class="px-4 py-2">
                        <?php echo $file['file_type']; ?>
                    </td>
                    <td class="px-4 py-2">
                        <a href="download.php?filename=<?php echo urlencode($file['filename']); ?>" class="text-blue-500 hover:underline">Download</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        endif;
    } else{ ?>
        <p>No files uploaded yet.</p>
        <?php } ?>
        <br>
        <a href="logout.php" class="text-blue-500 hover:underline">Logout</a>
    </div>
</body>
</html>
