<?php 

include "../db/db.php";

if(isset($_GET) && is_numeric($_GET["id"]) && is_numeric($_GET["page"])){
    $id = $_GET['id'];
    $page = $_GET['page'];
    $sql = "DELETE FROM cms_users WHERE ID = :id";
    $ins = [
        ":id" => $id
    ];
    $con = $db->prepare($sql);
    $con->execute($ins);
    var_dump($_GET);
    header("location: index.php?p=".$page."");
}

?>