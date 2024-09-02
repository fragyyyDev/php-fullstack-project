<?php 
session_start();


if (isset($_SESSION['LOGGED']) && $_SESSION['LOGGED'] == true) {
    echo $htmlClient;
} else {
    header("Location: register.php");
}



if (isset($_SESSION['LOGGED'])) { // Adjust this check based on your session variable
    echo '<a href="../server/logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Log Out</a>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action='../server/profile_meta.php' method='post' enctype='multipart/form-data' class='hideThis bg-gray-400 shadow-xl'>
    <input type='text' name='username' placeholder='Enter your preferred username' required>
    <input type='date' name='date' placeholder='Enter your birthdate' required>
    <input type='file' name='pfp' accept='image/*' required>
    <input type='submit' value='Send info' id='submitButton'>
</form>
</body>
</html>

