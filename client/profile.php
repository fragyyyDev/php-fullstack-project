<?php 
include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
session_start();

$htmlClient = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Document</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
    
    .hideThis {
        display: none;
    }

    </style>
</head>
<body>
<div class='h-screen w-screen'>
<div class='w-screen h-16 flex items-center justify-center'>
    <ul class='flex bg-white w-screen gap-[100px] items-center justify-center w-screen'>
        <li class='bg-gray-300'><a href='main.php'>Main Page</a></li>
        <li class='bg-gray-300'><a href='profile.php'>Profile</a></li>
    </ul>
</div>
<button  class='bg-gray-400 shadow-xl p-4' id='setupProfile'><a href='settings.php'>Setup profile info</a></button>
";

if(isset($_SESSION['hashedPass'])){
    $password = $_SESSION['hashedPass'];
}
if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}



//-------------------------------------------------

if(isset($_SESSION['adminMODE']) AND isset($_SESSION['LOGGED'])){
    if($_SESSION['adminMODE'] == true AND $_SESSION['LOGGED'] == true){
        echo $html_admin;
    }
} else {
    if(isset($_SESSION['LOGGED'])){
        if($_SESSION['LOGGED'] == true){
            echo $htmlClient;
        }
    }
}

$ins_dataProfile = [
    ":password" => $password,
    ":email" => $email,
];

$sqlGetProfileInfo = 'SELECT * FROM `cms_users` WHERE pass = :password AND email = :email';
$con = $db->prepare($sqlGetProfileInfo);
$con->execute($ins_dataProfile);
$dataProfileInfo = $con->fetchAll(PDO::FETCH_ASSOC);


$ins_dataProfileID = [
    ":userID" => $dataProfileInfo[0]['ID']
];



$sqlGetMetaInfo = 'SELECT * FROM `cms_users_meta` WHERE userID = :userID';
$con = $db->prepare($sqlGetMetaInfo);
$con->execute($ins_dataProfileID);
$dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);

$sqlGetPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
$con = $db->prepare($sqlGetPfp);
$con->execute($ins_dataProfileID);
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

$sqlGetPosts = 'SELECT * FROM `cms_posts` WHERE authorID = :userID';
$con = $db->prepare($sqlGetPosts);
$con->execute($ins_dataProfileID);
$dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);



echo '<div class="w-screen bg-white rounded-lg shadow-lg overflow-hidden flex items-center flex-column justify-center">';
echo '    <div class="divide-y divide-gray-200">';

foreach ($dataPosts as $post) {
    echo '        <div class="p-4">';
    
    // Author section
    echo '            <div class="flex items-center mb-4">';
    echo "<img  class='w-16 h-16 rounded-full' src='../pfp/" . $dataPfp[0]['userID'] . "." . $dataPfp[0]['extension'] . "'>";
    echo '                <div class="font-semibold text-gray-800 ml-8">' . htmlspecialchars($username) . '</div>'; // Display authorID or replace with actual username
    echo '            </div>';

    // Image section
    if (!empty($post['image'])) {
        $imagePath = '../server/' . htmlspecialchars($post['image']);
        echo '            <img src="../server/' . $post['image'] . '" alt="' . htmlspecialchars($post['title']) . '" class="w-[400px] h-auto rounded-lg mb-4">';
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
echo '</div>
</div>
</body>
</html>';

;
?>