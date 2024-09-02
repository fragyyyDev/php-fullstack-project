<?php 
include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
session_start();

if (isset($_SESSION['hashedPass'])) {
    $password = $_SESSION['hashedPass'];
}
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

if (isset($_SESSION['hashedPass']) && isset($_SESSION['email'])) {
    $ins_dataID = [
        ":password" => $_SESSION['hashedPass'],
        ":email" => $_SESSION['email'],
    ];

    // Getting userID
    $sqlGetUserId = 'SELECT ID FROM `cms_users` WHERE email = :email AND pass = :password';
    $con = $db->prepare($sqlGetUserId);
    $con->execute($ins_dataID);
    $dataID = $con->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($dataID)) {
        $userID = $dataID[0]['ID'];

        // Fetch personal metaData for username
        $sqlGetMetaData = "SELECT * FROM `cms_users_meta` WHERE userID = :userID";
        $ins_dataMeta = [
            ":userID" => $userID
        ];
        $con = $db->prepare($sqlGetMetaData);
        $con->execute($ins_dataMeta);
        $dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Handle the case where user data is not found
        $userID = null;
    }
}

// Fetch posts
$sqlGetPosts = 'SELECT * FROM `cms_posts`';
$con = $db->prepare($sqlGetPosts);
$con->execute();
$dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);

$htmlClient = "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Document</title>
    <script src='https://cdn.tailwindcss.com'></script>
<style>
</style>
</head>
<body >
<div class='header w-screen h-16 flex items-center justify-center'>
    <ul class='flex bg-white gap-[50px] items-center justify-center w-screen'>
        <li><a href='#'>Main Page</a></li>
        <li><a href='profile.php'>Profile</a></li>
    </ul>
</div>
<div class='w-screen flex items-center justify-center'>
    <button id='makePost'>Make a post</button>
        <form action='../server/createpost.php' method='post' enctype='multipart/form-data' class='hideThis hidden'>
            <input type='text' name='title' placeholder='title'>
            <input type='text' name='text' placeholder='text'>
            <input type='file' name='file' id='file' required>
            <input type='submit' value='Odeslat' id='submitButton'>
        </form>
    </div>
</body>
<script>
    document.querySelector('#makePost').addEventListener('click', function(e){
            document.querySelector('.hideThis').style.display='block'
            document.querySelector('#makePost').style.display='none'
        })
    document.querySelector('#submitButton').addEventListener('click', function(e){
        document.querySelector('.hideThis').style.display='hidden'
    })
</script>
</html>";


function getPfpDirFromUserId($db, $userIDpost) {
    $insDataPfp = [
        ':userID' => $userIDpost,
    ];
    $sqlGetPfpDir = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
    $con = $db->prepare($sqlGetPfpDir);
    $con->execute($insDataPfp);
    $dataPfp = $con->fetch(PDO::FETCH_ASSOC); 

    if ($dataPfp) { 
        $dir = $dataPfp['userID'];
        $extension = $dataPfp['extension'];
        return $dir . '.' . $extension;
    } else {
        return null; 
    }
}

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
        header("Location: admin.php");
    }
} else {
    if (isset($_SESSION['LOGGED']) && $_SESSION['LOGGED'] == true) {
        echo $htmlClient;
    } else {
        header("Location: register.php");
    }
}

echo '<div class="w-screen bg-white rounded-lg shadow-lg overflow-hidden flex items-center flex-column justify-center">';
echo '    <div class="divide-y divide-gray-200">';

foreach ($dataPosts as $post) {
    echo '        <div class="p-4">';
    
    echo '            <div class="flex items-center mb-4">';
    
    if ($userID) {
        $profileImage = getPfpDirFromUserId($db, $post['authorID']);
        if ($profileImage) {
            echo "<img class='w-16 h-16 rounded-full' src='../pfp/" . htmlspecialchars($profileImage) . "'>";
        } else {
            echo "<img class='w-16 h-16 rounded-full' src='../pfp/default.jpg'>"; 
        }
    } else {
        echo '<img src="../pfp/default.jpg" alt="Default Profile Picture" class="w-12 h-12 rounded-full mr-3">';
    }
    if ($post['authorID']) {
        $sqlGetAuthorName = 'SELECT * FROM `cms_users_meta` WHERE userID = :userID';
        $con = $db->prepare($sqlGetAuthorName);
        $con->execute(array(':userID' => $post['authorID']));
        $dataAuthorName = $con->fetchAll(PDO::FETCH_ASSOC);
    
        $username = $post['authorID']; 
    
        foreach ($dataAuthorName as $row) {
            if ($row['keyWord'] == 'username') {
                $username = $row['value'];
                break; 
            }
        }
    
    }
    echo '<div class="font-semibold text-gray-800">';
    echo '<a href="profiles.php?post=' . htmlspecialchars($post['authorID']) . '">';
    echo htmlspecialchars($username);
    echo '</a>';
    echo '</div>';    echo '            </div>';

    // Image section
    if (!empty($post['image'])) {
        $imagePath = '../server/' . htmlspecialchars($post['image']);
        echo '            <img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($post['title']) . '" class="w-[400px] h-auto rounded-lg mb-4">';
    } else {
        echo '            <p class="text-gray-500">No image available.</p>';
    }

    // Post content
    echo '            <div>';
    echo '                <h3 class="text-xl font-bold text-gray-900 mb-2">' . htmlspecialchars($post['title']) . '</h3>';
    echo '                <p class="text-gray-700 mb-2">' . nl2br(htmlspecialchars($post['text'])) . '</p>';
    echo '            </div>';

    // Post timestamp
    echo '            <div class="text-gray-500 text-sm">' . htmlspecialchars($post['time']) . '</div>';
    echo '        </div>';
}

echo '    </div>';
echo '</div>';
?>
