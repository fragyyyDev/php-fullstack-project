<?php 

include "../db/db.php";
session_start();

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
        if(isset($_GET) && is_numeric($_GET["id"]) && is_numeric($_GET["page"])){
            $id = $_GET['id'];
            $page = $_GET['page'];
            $sql = "DELETE FROM cms_posts WHERE postID = :id";
            $ins = [
                ":id" => $id
            ];
            $con = $db->prepare($sql);
            $con->execute($ins);
            var_dump($_GET);
            header("location: index.php?p=".$page."");
        }
    }
}


?>