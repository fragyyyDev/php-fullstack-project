
<?php

include "../db/db.php";
session_start();

//-----------------------------------------------------------------------------------------

if(isset($_GET) && is_numeric($_GET["id"]) && is_numeric($_GET["page"]) ){
    $id = $_GET['id'];
    $page = $_GET['page'];
    $_SESSION["id"] = $id;
    $_SESSION["page"] = $page;
    $sql = "SELECT * FROM cms_users WHERE ID = :id";
    $ins = [
        ':id'=>$id
    ];
    $con = $db->prepare($sql);
    $con->execute($ins);
    $data = $con->fetchAll(PDO::FETCH_ASSOC);
    if(count($data) == 1 && isset($data[0]["email"]) && isset($data[0]["password"]) && isset($data[0]["isAdmin"])){
        $email = $data[0]["email"];
        $password = $data[0]["password"];
        $isAdmin = $data[0]["isAdmin"];
    }
}

//-----------------------------------------------------------------------------------------


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
            <form action="update_action.php" method="post" class="formular" >
                <p>Email</p>
                <input type="email" name="email" class="inputy" <?php echo "value=" ?><?php
                    if(isset($email)){
                        echo $email;
                    }
                ?>
                > <br>
                <p>PASSWORD</p>
                <input type="password" name="password" class="inputy"<?php echo "value=" ?><?php
                    if(isset($password)){
                        echo $password;
                    }
                ?>
                ><br>
                <p>isAdmin</p>
                <input type="text" name="isAdmin" class="inputy" <?php echo "value=" ?><?php
                    if(isset($isAdmin)){
                        echo $isAdmin;
                    }
                ?>
                 ><br>
                <input type="submit" value="Klikni" class="submit">
            </form>
</body>
</html>