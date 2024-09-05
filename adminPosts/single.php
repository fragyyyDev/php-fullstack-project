<?php
include "../db/db.php";
include "../functions/getPfpDirFromUserId.php";
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
    echo '<div class="container bg-gray-900 text-gray-100 rounded-lg p-4 my-4 flex flex-col items-center justify-center mt-32">';
    
    if ($dataProfileInfo['ID']) {
        $profileImage = getPfpDirFromUserId($db, $post['authorID']);
        echo "<img class='w-16 h-16 rounded-full mb-4' src='../pfp/" . htmlspecialchars($profileImage ?? 'default.jpg') . "'>";
    } else {
        echo '<img src="../pfp/default.jpg" alt="Default Profile Picture" class="w-16 h-16 rounded-full mb-4">';
    }

    $sqlGetAuthorName = 'SELECT * FROM `cms_users_meta` WHERE userID = :userID';
    $con = $db->prepare($sqlGetAuthorName);
    $con->execute([':userID' => $post['authorID']]);
    $dataAuthorName = $con->fetchAll(PDO::FETCH_ASSOC);
    $username = $post['authorID']; 
    
    foreach ($dataAuthorName as $row) {
        if ($row['keyWord'] == 'username') {
            $username = $row['value'];
            break;
        }
    }
    
    echo '<div class="font-semibold text-blue-400 mb-2">';
    echo '<a href="profiles.php?post=' . htmlspecialchars($post['authorID']) . '">' . htmlspecialchars($username) . '</a>';
    echo '</div>';

    if (!empty($post['image'])) {
        $imagePath = '../server/' . htmlspecialchars($post['image']); // Use the correct path for the image
        echo '<img src="' . $imagePath . '" alt="Post Image" class="w-[480px] h-auto rounded-lg mb-4">';
    } else {
        echo '<p class="text-gray-500">No image available.</p>';
    }

    echo '<div class="mb-2"><h3 class="text-xl font-bold text-white">' . htmlspecialchars($post['title']) . '</h3>';
    echo '<p class="text-gray-300">' . nl2br(htmlspecialchars($post['text'])) . '</p>';
    echo '</div>';
    echo '<div class="text-gray-500 text-sm">' . htmlspecialchars($post['time']) . '</div>';

    //----------------------------
    // Display comments
    //----------------------------
    echo '<div class="w-full bg-gray-800 mt-4 p-2 rounded flex flex-col">';
    $sqlGetComments = 'SELECT * FROM `cms_comments` WHERE postID = :postID';
    $con = $db->prepare($sqlGetComments);
    $con->execute([':postID' => $post['postID']]);
    $comments = $con->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($comments as $comment) {
        echo '<div class="comment flex items-start mb-2">';
        
        $sqlGetCommenterPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
        $con = $db->prepare($sqlGetCommenterPfp);
        $con->execute([':userID' => $comment['userID']]);
        $commenterPfpData = $con->fetch(PDO::FETCH_ASSOC);
        $commenterPfp = $commenterPfpData ? $commenterPfpData['userID'] . '.' . $commenterPfpData['extension'] : 'default.jpg';
    
        echo "<img class='w-8 h-8 rounded-full' src='../pfp/" . htmlspecialchars($commenterPfp) . "'>";
    
        $sqlGetCommenterName = 'SELECT * FROM `cms_users_meta` WHERE userID = :userID';
        $con = $db->prepare($sqlGetCommenterName);
        $con->execute([':userID' => $comment['userID']]); 
        $commenterMeta = $con->fetchAll(PDO::FETCH_ASSOC);
        
        $commenterName = $comment['userID']; 
        foreach ($commenterMeta as $meta) {
            if ($meta['keyWord'] == 'username') {
                $commenterName = $meta['value'];
                break;
            }
        }
    
        echo '<div class="ml-3">';
        echo '<span class="font-bold text-blue-400">' . htmlspecialchars($commenterName) . '</span>';
        echo '<p class="text-gray-300">' . htmlspecialchars($comment['message']) . '</p>';
        echo '<span class="text-gray-500 text-sm">' . htmlspecialchars($comment['time']) . '</span>';
        echo '</div>';
        echo '</div>';
    }
    
    //----------------------------
    //  Comment form
    //----------------------------
    echo '<button class="bg-gray-700 hover:bg-gray-600 text-white text-sm p-2 rounded" onclick="showCommentForm(' . $post['postID'] . ')">Add Comment</button>';
    echo '<div id="commentForm-' . $post['postID'] . '" class="hidden mt-2">';
    echo '<form action="../server/createcomment.php" method="POST">';
    echo '<input type="hidden" name="postID" value="' . htmlspecialchars($post['postID']) . '">';
    echo '<textarea name="comment" placeholder="Write a comment..." class="w-full p-2 border border-gray-600 rounded bg-gray-800 text-gray-100"></textarea>';
    echo '<button type="submit" class="bg-blue-600 text-white p-2 rounded mt-2">Submit</button>';
    echo '</form>';
    echo '</div>';
    
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-900 text-gray-100 flex justify-center items-center'>
    <div class='flex justify-center'>
    </div>
</body>
</html>

<script> 
    function showCommentForm(postID) { 
        document.getElementById('commentForm-' + postID).classList.toggle('hidden'); 
    } 
</script>
