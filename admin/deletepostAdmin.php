<?php 
session_start();
include "../db/db.php";

if(isset($_SESSION['admin'])){
    if($_SESSION['admin'] == 1 ){
        if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_SESSION['ID'])) {
            $id = $_GET['id'];
            $sql = "DELETE FROM cms_posts WHERE postID = :id";
            $ins = [
                ":id" => $id
            ];
            $con = $db->prepare($sql);
            $con->execute($ins);
            $goBackUrl = $_SESSION['redirectBackUrl'];
            header('Location:' . $goBackUrl);
            exit();
        } else {
            $goBackUrl = $_SESSION['redirectBackUrl'];
        }
    } else {
        echo 'you are not an admin';
    }
} else {
    echo 'you are not logged in as an admin';
}
?>