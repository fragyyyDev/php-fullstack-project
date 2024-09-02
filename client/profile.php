<?php 
include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
session_start();

function getArrayValue($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

if (!isset($_SESSION['LOGGED']) || $_SESSION['LOGGED'] !== true) {
    header("Location: register.php");
    exit();
}

if (!isset($_SESSION['hashedPass']) || !isset($_SESSION['email'])) {
    die("Session variables are not set.");
}

$password = $_SESSION['hashedPass'];
$email = $_SESSION['email'];

$htmlClient = "
<!DOCTYPE html>
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
    <button class='bg-gray-400 shadow-xl p-4' id='setupProfile'>
        <a href='settings.php'>Setup profile info</a>
    </button>
";

echo $htmlClient;

$ins_dataProfile = [
    ":password" => $password,
    ":email" => $email,
];

$sqlGetProfileInfo = 'SELECT * FROM `cms_users` WHERE pass = :password AND email = :email';
$con = $db->prepare($sqlGetProfileInfo);
$con->execute($ins_dataProfile);
$dataProfileInfo = $con->fetch(PDO::FETCH_ASSOC);

if (!$dataProfileInfo) {
    die("Profile information not found.");
}

$_SESSION['ID'] = $dataProfileInfo['ID'];

$ins_dataProfileID = [
    ":userID" => $dataProfileInfo['ID']
];

$sqlGetMetaInfo = 'SELECT * FROM `cms_users_meta` WHERE userID = :userID';
$con = $db->prepare($sqlGetMetaInfo);
$con->execute($ins_dataProfileID);
$dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);

$sqlGetPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
$con = $db->prepare($sqlGetPfp);
$con->execute($ins_dataProfileID);
$dataPfp = $con->fetch(PDO::FETCH_ASSOC);

echo "<p class='p-4 bg-gray-200 shadow-lg text-center'> Email = " . htmlspecialchars($dataProfileInfo['email']) . "</p>"; 
echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>User since = " . htmlspecialchars($dataProfileInfo['time']) . "</p>";
echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>My Profile photo</p>";

if ($dataPfp) {
    echo "<img class='w-16 h-16 rounded-full' src='../pfp/" . htmlspecialchars($dataPfp['userID']) . "." . htmlspecialchars($dataPfp['extension']) . "'>";
} else {
    echo "<img class='w-16 h-16 rounded-full' src='../pfp/default.jpg'>";
}

foreach ($dataMeta as $meta) {
    $keyWord = htmlspecialchars($meta['keyWord']);
    $value = htmlspecialchars($meta['value']);
    
    if ($keyWord == 'username') {
        echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>Username = " . $value . "</p>";
    } elseif ($keyWord == 'date') {
        echo "<p class='p-4 bg-gray-200 shadow-lg text-center'>Date of " . $value . "</p>";
    }
}

$sqlGetPosts = 'SELECT * FROM `cms_posts` WHERE authorID = :userID';
$con = $db->prepare($sqlGetPosts);
$con->execute($ins_dataProfileID);
$dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="w-screen bg-white rounded-lg shadow-lg overflow-hidden flex items-center flex-column justify-center">';
echo '    <div class="divide-y divide-gray-200">';

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
    
    $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $_SESSION['redirectBackUrl'] = $currentUrl;
    echo '<button class="bg-red-500 hover:bg-red-700 text-white font-bold">';
    echo '<a href="../admin/deletepost.php?id=' . $postID . '">Delete post</a>';
    echo '</button>';
    
    echo '        </div>';
}

echo '    </div>';
echo '</div>
</div>
</body>
</html>';
?>