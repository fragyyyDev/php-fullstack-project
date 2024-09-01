<?php 



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