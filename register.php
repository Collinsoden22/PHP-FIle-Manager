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
    <title>File Manager - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>
<!-- ... -->

<body class="bg-gray-100">
    <div class="container mx-auto p-4 align-middle my-12">
        <h1 class="text-2xl font-semibold mb-4">Register</h1>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
            include_once('includes/config.php');
            include_once('includes/functions.php');

            $username = htmlentities(strip_tags($_POST['username']));
            $password = $_POST['password'];
            $register = registerUser($username, $password);
            if ($register) {
                $msg = "Account succesfully created, login to continue";
                header("Location: login.php?msg=$msg");
                exit;
            } else {
                $msg = "Username already exists, pick another username.";
                header("Location: register.php?error=$msg");
                exit;
            }
        }
        ?>
        <form action="register.php" method="post" class="my-8">
            <?php if ($_GET['error']) {
                $error = htmlentities($_GET['error']);
                echo "<p class='text-rose-900 text-center'>$error <p>";
            } ?>
            <label for="username" class="block mb-2">Username:</label>
            <input type="text" name="username" required class="py-2 px-3 border rounded w-full">
            <br>
            <label for="password" class="block mb-2">Password:</label>
            <input type="password" name="password" required class="py-2 px-3 border rounded w-full">
            <br>
            <input type="submit" value="Register" class="my-5 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
        </form>
        <p class="text-gray-700">Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login here</a></p>
    </div>
</body>
<!-- ... -->

</html>