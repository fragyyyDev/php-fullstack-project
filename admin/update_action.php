<?php 
session_start();
include "../db/db.php";


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
    header("location: index.php?p=".$page."");
}

?>