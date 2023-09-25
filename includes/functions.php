<?php
require_once('includes/config.php');
function registerUser($username, $password)
{
    global $db; // Using the SQLite database connection from config.php
    if(checkUserExist($username)) {
        return false;
    };
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $stmt->bindValue(2, $hashedPassword, SQLITE3_TEXT);

    return $stmt->execute();
}

function checkUserExist($username)
{
    global $db;

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $fetch_result = $result->fetchArray(SQLITE3_NUM);

    return $fetch_result; // If count is greater than 0, the username exists.
}


function loginUser($username, $password)
{
    try {
    global $db; // Using the SQLite database connection from config.php

    $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Error preparing SQL statement: " . $db->lastErrorMsg());
    }
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    $row = $result->fetchArray(SQLITE3_ASSOC);

    if (!$row) {
        throw new Exception("Error executing SQL query: " . $db->lastErrorMsg());
    }
    if ($row && password_verify($password, $row['password'])) {
        return ['id' => $row['id'], 'username' => $row['username']];
    }
    return null;
} catch (Exception $e){
  // Handle the error here (log, display, etc.)
  error_log("Error logging in with $username: " . $e->getMessage());
  return false;
}
}

function isValidFile($file)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    return in_array($file['type'], $allowedTypes) && $file['size'] > 0;
}

function generateUniqueFilename($file)
{
    $timestamp = time();
    $uuid = uniqid();
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    return "{$timestamp}_{$uuid}.{$extension}";
}
function saveFileInfoToDatabase($originalName, $newFilename, $fileSize, $fileType, $uploaderUsername)
{
    global $db; // Using the SQLite database connection from config.php

    $stmt = $db->prepare("INSERT INTO files (original_name, filename, file_size, file_type, uploader_username) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $originalName, SQLITE3_TEXT);
    $stmt->bindValue(2, $newFilename, SQLITE3_TEXT);
    $stmt->bindValue(3, $fileSize, SQLITE3_TEXT);
    $stmt->bindValue(4, $fileType, SQLITE3_TEXT);
    $stmt->bindValue(5, $uploaderUsername, SQLITE3_TEXT);

    return $stmt->execute();
}

function formatFileSize($size)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}

function listUploadedFiles($uploaderUsername)
{
    global $db;

    try {
        $stmt = $db->prepare("SELECT original_name, filename, upload_timestamp, file_size, file_type, uploader_username FROM files WHERE uploader_username = ?");
        if (!$stmt) {
            throw new Exception("Error preparing SQL statement: " . $db->lastErrorMsg());
        }

        $stmt->bindValue(1, $uploaderUsername, SQLITE3_TEXT);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("Error executing SQL query: " . $db->lastErrorMsg());
        }

        $files = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $files[] = $row;
        }

        return $files;
    } catch (Exception $error) {
        // Handling the error here (loging to console)
        error_log("Error fetching files for user $uploaderUsername: " . $error->getMessage());
        return false;
    }
}



function getFileInfo($filename, $uploaderUsername)
{
    global $db;
    // Get uploaded file information
    $stmt = $db->prepare("SELECT original_name, file_type FROM files WHERE filename = ? AND uploader_username = ?");
    $stmt->bindValue(1, $filename, SQLITE3_TEXT);
    $stmt->bindValue(2, $uploaderUsername, SQLITE3_TEXT);
    $result = $stmt->execute();

    return $result->fetchArray(SQLITE3_ASSOC);
}

?>
