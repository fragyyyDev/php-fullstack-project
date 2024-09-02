<?php
    include "../db/db.php";
    session_start();

    if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
        if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
    
        } else {
            header("Location: ../client/register.php");
        }
    } else {
        header("Location: ../client/register.php");
    }
    

    if(isset($_GET["id"]) AND is_numeric($_GET["id"])){
        $id = $_GET["id"];
    } else {
        $id = 0;
    }

    $sql = "SELECT * FROM cms_posts WHERE postID = :id";
    $ins_data = [
        ":id" => $id,
    ];

    $con = $db->prepare($sql);
    $con->execute($ins_data);
    var_dump($con->fetchAll(PDO::FETCH_ASSOC));

