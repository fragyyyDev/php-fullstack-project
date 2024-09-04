<?php
    include "../db/db.php";
    session_start();

    if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
        if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
            if(isset($_GET["id"]) AND is_numeric($_GET["id"])){
                $id = $_GET["id"];
            } else {
                $id = 0;
            }
        
            $sql = "SELECT * FROM cms_users WHERE ID = :id";
            $ins_data = [
                ":id" => $id,
            ];
        
            // posilani do logu 
            $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
            $log_data = [
                ":userID" => $_SESSION['ID'],
                ":type" => 'SELECT',
                ":message" => 'User details viewed by admin',
                ":value" => "User ID: $id"
            ];
            $log_con = $db->prepare($log_sql);
            $log_con->execute($log_data);

            $con = $db->prepare($sql);
            $con->execute($ins_data);
            var_dump($con->fetchAll(PDO::FETCH_ASSOC));        
        } else {
            header("Location: ../client/register.php");
            exit();
        }
    } else {
        header("Location: ../client/register.php");
        exit();
    }
    


    