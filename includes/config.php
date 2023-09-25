<?php
// Database file path
$databaseFile = 'fileManager.db';

// Check if the database file exists
if (!file_exists($databaseFile)) {
    // Create a new SQLite database
    $db = new SQLite3($databaseFile);

    if (!$db) {
        die("Failed to find database");
    }
 // Create 'users' table
 $createUsersTableQuery = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY,
    username TEXT NOT NULL,
    password TEXT(255) NOT NULL
)";

if (!$db->exec($createUsersTableQuery)) {
    die("Failed to create users table");
}

// Create 'files' table for file uploads
$createFilesTableQuery = "CREATE TABLE IF NOT EXISTS files (
    id INTEGER PRIMARY KEY,
    original_name TEXT NOT NULL,
    filename TEXT NOT NULL,
    upload_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_size INTEGER NOT NULL,
    file_type TEXT NOT NULL DEFAULT 'pdf',
    uploader_username TEXT NOT NULL,
    FOREIGN KEY (uploader_username) REFERENCES users(username)
)";

if (!$db->exec($createFilesTableQuery)) {
    die("Failed to create files table");
}

// Insert an initial user for testing (change username and password as needed)
$initialUsername = "admin";
$initialPassword = password_hash("password123", PASSWORD_BCRYPT); // Hash the password

$insertUserQuery = "INSERT INTO users (username, password) VALUES ('$initialUsername', '$initialPassword')";

if (!$db->exec($insertUserQuery)) {
    die("Failed to insert initial user");
}

$db->close();

}

// Database connection
$db = new SQLite3($databaseFile);

if (!$db) {
    die("Failed to connect to the database");
}
?>
