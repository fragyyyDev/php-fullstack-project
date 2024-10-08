<?php

include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
session_start();
$email = $password  = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = ($_POST["email"]) ?? null;
    $password = ($_POST["password"]) ?? null ;
    $error = false;

    if(checkEmpty($email)){
        setError("email", "email nebylo zadano..");
        $error = true;
    }

    if(checkEmpty($password)){
        setError("password", "password nebylo zadano..");
        $error = true;
    }


    if (!$error) {
        $passwordHashed = createHash($password);
        $ins_data = [
            ":email" => $email,
            ":password" => $passwordHashed,
        ];
        $sql = "SELECT * FROM `cms_users` WHERE email = :email AND pass = :password";
        $con = $db->prepare($sql);
        $con->execute($ins_data);
        $data = $con->fetchAll(PDO::FETCH_ASSOC);
        if(checkEmpty($data)){
            setError("Error", "Spatne Prihlasovaci udaje");
            header( "refresh:2;url=../client/login.php" );
        } else {
            if($data[0]['admin'] == 1){
                $_SESSION["admin"] = 1;
            };
            $_SESSION["LOGGED"] = true;
            $_SESSION['ID'] = $data[0]['ID'];
            $_SESSION['TIME'] = time();
            $_SESSION['hashedPass'] = $passwordHashed;
            $_SESSION['email'] = $email;

            $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
            $log_data = [
                ":userID" => $_SESSION['ID'],
                ":type" => 'INSERT',
                ":message" => ' User logged in',
                ":value" => "Details: " . $passwordHashed . $email
            ];
            $log_con = $db->prepare($log_sql);
            $log_con->execute($log_data);
            echo 'Logged in succesfully please wait while we redirect you to the main page';
            header( "refresh:2;url=../client/main.php" );
        }
    } 
}