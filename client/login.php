
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2> Login welcome</h2>
    <form action="../server/loginaction.php" method='post'>
        <input type="email" name='email' placeholder='Email'>
        <input type="password" name='password' placeholder='password'>
        <input type="submit" value="Odesli to">
    </form>
    <h3>Are you not our user yet? No problem register here</h3>
    <a href="register.php">Register</a>
    <?php
        session_start();
        echo '<br>';
        echo "EXAMPLE ADMIN : admin@admin.com     admin123";
        echo '<br>';
        echo "EXAMPLE CLIENT : abc@abc     abc";

        if(isset($_SESSION["error"])){
            foreach($_SESSION["error"] as $key => $message) {
                echo "<p>$key: $message</p>";
            }
            unset($_SESSION["error"]);
        }
    
    ?>
</body>
</html>