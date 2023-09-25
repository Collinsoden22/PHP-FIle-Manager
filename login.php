<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>File Manager - Login</title>
</head>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">

<body class="bg-gray-100 m-8">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4">Login</h1>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
            try {

                include_once('includes/config.php');
                include_once('includes/functions.php');

                $username = htmlentities(strip_tags($_POST['username']));
                $password = $_POST['password'];

                $user = loginUser($username, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Login failed. Please check your username and password.';
                    header("Location: login.php?error=$error");
                    exit;
                }
            } catch (Exception $error) {
                $error = 'Server manfunctioned, try again.';
                header("Location: login.php?error=$error");
                exit;
            }
        }
        ?>
        <form action="login.php" method="post" class="m-8">
            <?php
            if ($_GET['error']) {
                $error = htmlentities($_GET['error']);
                echo "<p class='text-rose-900 text-center'>$error <p>";
            }
            if ($_GET['msg']) {
                $msg = htmlentities($_GET['msg']);
                echo "<p class='text-green-900 text-center'>$msg <p>";
            }
            ?>
            <label for="username" class="block mb-2">Username:</label>
            <input type="text" name="username" required class="py-2 px-3 border rounded w-full">
            <br>
            <label for="password" class="block mb-2">Password:</label>
            <input type="password" name="password" required class="py-2 px-3 border rounded w-full">
            <br>
            <input type="submit" value="Login" class="my-5 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
        </form>
        <p class="text-gray-700">Don't have an account? <a href="register.php" class="text-blue-500 hover:underline">Register here</a></p>
    </div>
</body>

</html>