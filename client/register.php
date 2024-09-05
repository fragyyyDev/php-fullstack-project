<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .bg-container { background-color: #1f2937; } /* Dark Grey */
        .text-main { color: #e5e7eb; } /* Lighter Grey */
        .text-header { color: #60a5fa; } /* Light Blue */
        .bg-primary { background-color: #1e40af; } /* Blue */
        .bg-secondary { background-color: #374151; } /* Medium Grey */
    </style>
</head>
<body class="bg-container text-main min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-secondary p-8 rounded-lg shadow-lg">
        <h2 class="text-center text-3xl text-header mb-6">Register Welcome</h2>
        <form action="../server/registeraction.php" method='post' class="flex flex-col gap-4">
            <input type="email" name="email" placeholder="Email" class="p-2 rounded bg-gray-200 text-black" required>
            <input type="password" name="password" placeholder="Password" class="p-2 rounded bg-gray-200 text-black" required>
            <input type="password" name="passwordConfirm" placeholder="Confirm your password" class="p-2 rounded bg-gray-200 text-black" required>
            <input type="submit" value="Submit" class="bg-primary text-white p-2 rounded hover:bg-blue-600 cursor-pointer">
        </form>
        <h3 class="mt-6 text-center text-xl text-main">Already have an account?</h3>
        <div class="text-center mt-2">
            <a href="login.php" class="text-header hover:text-blue-400">Login Here</a>
        </div>
        <div class="mt-4">
            <?php
                if(isset($_SESSION["error"])) {
                    foreach($_SESSION["error"] as $key => $message) {
                        echo "<p class='text-red-500'>$key: $message</p>";
                    }
                    unset($_SESSION["error"]);
                }
            ?>
        </div>
    </div>
</body>
</html>
