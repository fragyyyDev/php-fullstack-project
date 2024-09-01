<?php
    include "../db/db.php";

    if(isset($_GET["id"]) AND is_numeric($_GET["id"])){
        $id = $_GET["id"];
    } else {
        $id = 0;
    }

    $sql = "SELECT * FROM cms_users WHERE ID = :id";
    $ins_data = [
        ":id" => $id,
    ];

    $con = $db->prepare($sql);
    $con->execute($ins_data);
    var_dump($con->fetchAll(PDO::FETCH_ASSOC));


    