<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body>
    <h1>Welcome to someones profile</h1>
    <div class='header w-screen h-16 flex items-center justify-center'>
    <ul class='flex bg-white gap-[50px] items-center justify-center w-screen'>
        <li><a href='main.php'>Main Page</a></li>
        <li><a href='profile.php'>My Profile</a></li>
    </ul>
</div>
</body>
</html>

<?php 
    include "../db/db.php";
    session_start();

    if (isset($_SESSION['LOGGED']) && $_SESSION['LOGGED'] == true) {
        echo $htmlClient;
    } else {
        header("Location: register.php");
    }


if (isset($_GET['post'])) {
    $authorID = htmlspecialchars($_GET['post']);
} else {
    header("Location: main.php");
}

//---


$ins_dataProfile = [
    ":userID" => $authorID,
];

$sqlGetProfileInfo = 'SELECT * FROM `cms_users` WHERE ID = :userID';
$con = $db->prepare($sqlGetProfileInfo);
$con->execute($ins_dataProfile);
$dataProfileInfo = $con->fetchAll(PDO::FETCH_ASSOC);

$sqlGetMetaInfo = 'SELECT * FROM `cms_users_meta` WHERE userID = :userID';
$con = $db->prepare($sqlGetMetaInfo);
$con->execute($ins_dataProfile);
$dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);

$sqlGetPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
$con = $db->prepare($sqlGetPfp);
$con->execute($ins_dataProfile);
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
$con->execute($ins_dataProfile);
$dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);



echo '<div class="w-screen bg-white rounded-lg shadow-lg overflow-hidden flex items-center flex-column justify-center">';
echo '    <div class="divide-y divide-gray-200">';


foreach ($dataPosts as $post) {

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

    echo '        <div class="p-4">';
    
    // Author section
    echo '            <div class="flex items-center mb-4">';
    if($dataPfp){
        echo "<img  class='w-16 h-16 rounded-full' src='../pfp/" . $dataPfp[0]['userID'] . "." . $dataPfp[0]['extension'] . "'>";
    } else {
        echo "<img  class='w-16 h-16 rounded-full' src='../pfp/default.jpg'>"; 
    }
    echo '                <div class="font-semibold text-gray-800 ml-8">' . htmlspecialchars($username) . '</div>'; 
    echo '            </div>';

    // Image section
    if (!empty($post['image'])) {
        $imagePath = '../server/' . htmlspecialchars($post['image']);
        echo '            <img src="../server/' . $post['image'] . '" alt="' . htmlspecialchars($post['title']) . '" class="w-[400px] h-auto rounded-lg mb-4">';
    } else {
        echo '            <p class="text-gray-500">No image available.</p>';
    }

    echo '            <div>';
    echo '                <h3 class="text-xl font-bold text-gray-900 mb-2">' . htmlspecialchars($post['title']) . '</h3>';
    echo '                <p class="text-gray-700 mb-2">' . nl2br(htmlspecialchars($post['text'])) . '</p>';
    echo '            </div>';
   
    echo '            <div class="text-gray-500 text-sm">' . htmlspecialchars($post['time']) . '</div>';
    echo '        </div>';
   

    if(isset($_SESSION['admin'])){
        if($_SESSION['admin'] == 1){
            $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $_SESSION['redirectBackUrl'] = $currentUrl;
            echo '<button class="bg-red-500 hover:bg-red-700 text-white font-bold">';
            echo '<a href="../admin/deletepost.php?id=' . $post['postID'] . '">Delete post</a>';
            echo '</button>';
        }
    }
}


echo '    </div>';
echo '</div>
</div>
</body>
</html>';

;
?>