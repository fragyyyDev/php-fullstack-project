<?php
ob_start();

include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";

session_start();
$email = $password = $passwordConfirm = "";
$_SESSION['LOGGED'] = false; 

$passC = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = ($_POST["email"]) ?? null;
    $password = ($_POST["password"]) ?? null;
    $passwordConfirm = ($_POST["passwordConfirm"]) ?? null;
    $error = false;

    if (checkEmpty($email)) {
        setError("email", "email nebylo zadano..");
        $error = true;
    }

    if (checkEmpty($password)) {
        setError("password", "password nebylo zadano..");
        $error = true;
        $passC++;
    }

    if (checkEmpty($passwordConfirm)) {
        setError("passwordconfirm", "Nebyl zadan confirm..");
        $error = true;
        $passC++;
    }

    if ($passC == 0) {
        if ($password != $passwordConfirm) {
            $error = true;
            setError('hesla', 'se neshoduji');
        }
    }

    if (isset($email)) {
        $ins_data = [
            ":email" => $email,
        ];
        $sqlFindEmail = 'SELECT * FROM cms_users WHERE email = :email';
        $con = $db->prepare($sqlFindEmail);
        $con->execute($ins_data);
        $data = $con->fetchAll(PDO::FETCH_ASSOC);
        if (count($data) > 0) {
            setError("email2", "email je již registrovaný..");
            $error = true;
        }
    }

    if ($error == false) {
        $passwordHashed = createHash($password);
        $ins_data = [
            ":email" => $email,
            ":password" => $passwordHashed
        ];
        $sql = "INSERT INTO `cms_users` (`ID`, `email`, `pass`, `time`, `admin`) VALUES (NULL, :email, :password, CURRENT_TIMESTAMP, 0)";
        $con = $db->prepare($sql);
        $con->execute($ins_data);
        $db->lastInsertId();
        $_SESSION['LOGGED'] = true;
        $_SESSION['hashedPass'] = $passwordHashed;
        $_SESSION['email'] = $email;
        $_SESSION['TIME'] = time();
        $_SESSION['admin'] = 0;
        header("Location: ../client/main.php");
        exit(); 
    } else {
        header("Location: ../client/register.php");
        exit(); 
    }
}

ob_end_flush(); 