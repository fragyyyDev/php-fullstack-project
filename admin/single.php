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
            
        
            
            $sqlGetProfileInfo = 'SELECT * FROM `cms_users` WHERE ID = :id';
            $con = $db->prepare($sqlGetProfileInfo);
            $con->execute($ins_data);
            $dataProfileInfo = $con->fetchAll(PDO::FETCH_ASSOC);
            
            $sqlGetMetaInfo = 'SELECT * FROM `cms_users_meta` WHERE userID = :id';
            $con = $db->prepare($sqlGetMetaInfo);
            $con->execute($ins_data);
            $dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);
            
            $sqlGetPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :id';
            $con = $db->prepare($sqlGetPfp);
            $con->execute($ins_data);
            $dataPfp = $con->fetchAll(PDO::FETCH_ASSOC);
            
            
            echo "<p class='p-4 bg-gray-200 shadow-lg text-center'> Email = " . $dataProfileInfo[0]['email'] . '</p>'; 
            echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>User since = " . $dataProfileInfo[0]['time'] . '</p>';
            echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>My Profile photo</p>";
            if($dataPfp){
                echo "<img  class='w-16 h-16 rounded-full' src='../pfp/" . $dataPfp[0]['userID'] . "." . $dataPfp[0]['extension'] . "'>";
            }
            foreach($dataMeta as $kMeta => $vMeta){
                if($vMeta['keyWord'] == 'username'){
                    echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>Username =" . $vMeta['value'];
                    $username = $vMeta['value'];
                } elseif ( $vMeta['keyWord'] == 'date'){
                    echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>Date of " . $vMeta['value'];
                    $date = $vMeta['value'];
                }
            }
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo '<a href="index.php">go back to admin menu</a>';
        } else {
            header("Location: ../client/register.php");
            exit();
        }
    } else {
        header("Location: ../client/register.php");
        exit();
    }
    


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='flex justify-center flex-col'>
    
</body>
</html>