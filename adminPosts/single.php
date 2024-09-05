<?php
    include "../db/db.php";
    session_start();

    if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
        if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
    
        } else {
            header("Location: ../client/register.php");
        }
    } else {
        header("Location: ../client/register.php");
    }
    

    if(isset($_GET["id"]) AND is_numeric($_GET["id"])){
        $id = $_GET["id"];
    } else {
        $id = 0;
    }


    $ins_dataProfile = [
        ":password" => $_SESSION['hashedPass'],
        ":email" => $_SESSION['email'],
    ];
    
    $sqlGetProfileInfo = 'SELECT * FROM `cms_users` WHERE pass = :password AND email = :email';
    $con = $db->prepare($sqlGetProfileInfo);
    $con->execute($ins_dataProfile);
    $dataProfileInfo = $con->fetch(PDO::FETCH_ASSOC);


    $sql = "SELECT * FROM cms_posts WHERE postID = :id";
    $ins_data = [
        ":id" => $id,
    ];

    $con = $db->prepare($sql);
    $con->execute($ins_data);
    $dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);

    // posilani do logu 
    $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
    $log_data = [
        ":userID" => $_SESSION['ID'],
        ":type" => 'SELECT',
        ":message" => 'Post details viewed by admin',
        ":value" => "Post ID: " . $id
    ];
    $log_con = $db->prepare($log_sql);
    $log_con->execute($log_data);

    $ins_dataProfileID = [
        ":userID" => $dataProfileInfo['ID']
    ]; 

    $sqlGetPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
    $con = $db->prepare($sqlGetPfp);
    $con->execute($ins_dataProfileID);
    $dataPfp = $con->fetch(PDO::FETCH_ASSOC);

    foreach ($dataPosts as $post) {
        $postTitle = htmlspecialchars($post['title']);
        $postText = nl2br(htmlspecialchars($post['text']));
        $postTime = htmlspecialchars($post['time']);
        $postID = htmlspecialchars($post['postID']);
        
        $username = "Unknown Author";
        if ($post['authorID']) {
            $sqlGetAuthorName = 'SELECT value FROM `cms_users_meta` WHERE userID = :userID AND keyWord = "username"';
            $con = $db->prepare($sqlGetAuthorName);
            $con->execute(array(':userID' => $post['authorID']));
            $authorNameResult = $con->fetch(PDO::FETCH_ASSOC);
            if ($authorNameResult) {
                $username = htmlspecialchars($authorNameResult['value']);
            } else {
                $username = $post['authorID'];   
            }
        }
    
        echo '        <div class="p-4">';
        echo '            <div class="flex items-center mb-4">';
        
        if ($dataPfp) {
            echo "<img class='w-16 h-16 rounded-full' src='../pfp/" . htmlspecialchars($dataPfp['userID']) . "." . htmlspecialchars($dataPfp['extension']) . "'>";
        } else {
            echo "<img class='w-16 h-16 rounded-full' src='../pfp/default.jpg'>";
        }
        
        echo '                <div class="font-semibold text-gray-800 ml-8">' . $username . '</div>';
        echo '            </div>';
    
        if (!empty($post['image'])) {
            echo '            <img src="../server/' . htmlspecialchars($post['image']) . '" alt="' . $postTitle . '" class="w-[400px] h-auto rounded-lg mb-4">';
        } else {
            echo '            <p class="text-gray-500">No image available.</p>';
        }
    
        echo '            <div>';
        echo '                <h3 class="text-xl font-bold text-gray-900 mb-2">' . $postTitle . '</h3>';
        echo '                <p class="text-gray-700 mb-2">' . $postText . '</p>';
        echo '            </div>';
    
        echo '            <div class="text-gray-500 text-sm">' . $postTime . '</div>';
                
        echo '        </div>';
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
<body class='flex justify-center'>
    
</body>
</html>