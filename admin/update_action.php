<?php 
session_start();
include "../db/db.php";

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {

    } else {
        header("Location: ../client/register.php");
    }
} else {
    header("Location: ../client/register.php");
}


    $id = $_SESSION["id"] ?? null;
    $page = $_SESSION["page"] ?? null;

    var_dump($page);
    var_dump($id);

if(isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["isAdmin"])){    
    $sql = "UPDATE cms_users SET email = :email, pass = :password , admin = :isAdmin WHERE ID = :id";
    $ins = [
        ":id" => $id,
        ":email" => $_POST["email"],
        ":password" => $_POST["password"],
        ":isAdmin" => $_POST["isAdmin"],
    ];
    $con = $db->prepare($sql);
    $con->execute($ins);

    // posilani do logu 
    $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
    $log_data = [
        ":userID" => $_SESSION['ID'],
        ":type" => 'UPDATE',
        ":message" => 'Admin updated userINFO to email: ' . $_POST["email"]  . ', password: ' . $_POST["password"] . ', admin status: ' . $_POST["isAdmin"],
        ":value" => "User ID: $id"
    ];
    $log_con = $db->prepare($log_sql);
    $log_con->execute($log_data);

    header("location: index.php?p=".$page."");
}

?>