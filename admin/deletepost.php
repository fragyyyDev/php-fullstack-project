<?php 
session_start();
include "../db/db.php";

if(isset($_GET) && is_numeric($_GET["id"])){
    $id = $_GET['id'];
    $sql = "DELETE FROM cms_posts WHERE postID = :id";
    $ins = [
        ":id" => $id
    ];
    $con = $db->prepare($sql);
    $con->execute($ins);
    $goBackUrl = $_SESSION['redirectBackUrl'];
    header('Location:' . $goBackUrl);
}

?>