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

             // posilani do logu 
             $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
             $log_data = [
                 ":userID" => $_SESSION['ID'],
                 ":type" => 'DELETE',
                 ":message" => 'Post deleted by admin',
                 ":value" => "Post ID: " . $id
             ];
             $log_con = $db->prepare($log_sql);
             $log_con->execute($log_data);
        }
    }
}


?>