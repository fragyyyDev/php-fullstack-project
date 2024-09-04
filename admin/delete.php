<?php
session_start();
include "../db/db.php";


if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
        if(isset($_GET) && is_numeric($_GET["id"]) && is_numeric($_GET["page"])){
            $id = $_GET['id'];
            $page = $_GET['page'];

            $sql = "DELETE FROM cms_users WHERE ID = :id";
            $ins = [
                ":id" => $id
            ];
            $con = $db->prepare($sql);
            $con->execute($ins);

            // posilani do logu 
            $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
            $log_data = [
                ":userID" => $_SESSION['ID'],
                ":type" => 'DELETE',
                ":message" => 'User deleted by admin',
                ":value" => "User ID: $id"
            ];
            $log_con = $db->prepare($log_sql);
            $log_con->execute($log_data);
            header("location: index.php?p=".$page."");
        } else {
            echo "Invalid request";
        }
    } else {
        echo "You are not logged in or you are not an admin";
    }
} else {
    echo "You are not logged in or you are not an admin";
}

?>