<?php 
session_start();

if (isset($_SESSION['LOGGED']) && $_SESSION['LOGGED'] == true) {
    // User is logged in
} else {
    header("Location: register.php");
}

if (isset($_SESSION['LOGGED'])) { 
    echo '<div class="flex justify-end p-4">';
    echo '<a href="../server/logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Log Out</a>';
    echo '</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Info</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .bg-container { background-color: #1f2937; } /* Dark Grey */
        .text-main { color: #e5e7eb; } /* Light Grey */
        .text-header { color: #60a5fa; } /* Light Blue */
        .bg-primary { background-color: #1e40af; } /* Blue */
        .bg-secondary { background-color: #374151; } /* Medium Grey */
    </style>
</head>
<body class="bg-container text-main min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-secondary p-8 rounded-lg shadow-lg">
        <h2 class="text-center text-3xl text-header mb-6">Update Profile Info</h2>
        <form action='../server/profile_meta.php' method='post' enctype='multipart/form-data' class='flex flex-col gap-4'>
            <input type='text' name='username' placeholder='Enter your preferred username' class='p-2 rounded bg-gray-200 text-black' required>
            <input type='date' name='date' placeholder='Enter your birthdate' class='p-2 rounded bg-gray-200 text-black' required>
            <input type='file' name='pfp' accept='image/*' class='p-2 bg-gray-200 rounded' required>
            <input type='submit' value='Send Info' class='bg-primary text-white p-2 rounded hover:bg-blue-600 cursor-pointer'>
        </form>
    </div>
</body>
</html>
