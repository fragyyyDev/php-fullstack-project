<?php

session_start();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2> Register welcome</h2>
    <form action="../server/registeraction.php" method='post'>
        <input type="email" name='email' placeholder='Email'>
        <input type="password" name='password' placeholder='password'>
        <input type="password" name='passwordConfirm' placeholder='confirm your password'>
        <input type="submit" value="Odesli to">
    </form>
    <?php
    if(isset($_SESSION["error"])){
        foreach($_SESSION["error"] as $key => $message) {
            echo "<p>$key: $message</p>";
        }
        unset($_SESSION["error"]);
    }
    ?>
</body>
</html>