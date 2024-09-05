<?php 
include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
include "../functions/getPfpDirFromUserId.php";

session_start();


if (isset($_SESSION['hashedPass']) && isset($_SESSION['email'])) {
    $ins_dataID = [
        ":password" => $_SESSION['hashedPass'],
        ":email" => $_SESSION['email'],
    ];

    $sqlGetUserId = 'SELECT ID FROM `cms_users` WHERE email = :email AND pass = :password';
    $con = $db->prepare($sqlGetUserId);
    $con->execute($ins_dataID);
    $dataID = $con->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($dataID)) {
        $userID = $dataID[0]['ID'];

        $sqlGetMetaData = "SELECT * FROM `cms_users_meta` WHERE userID = :userID";
        $ins_dataMeta = [":userID" => $userID];
        $con = $db->prepare($sqlGetMetaData);
        $con->execute($ins_dataMeta);
        $dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $userID = null;
    }
}

$sqlGetPosts = 'SELECT * FROM `cms_posts`';
$con = $db->prepare($sqlGetPosts);
$con->execute();
$dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);

//----------------------------
// HTML structure
//----------------------------
$htmlClient = "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Posts</title>
<script src='https://cdn.tailwindcss.com'></script>
</head>
<body>
<div class='header w-screen h-16 flex items-center justify-center'>
    <ul class='flex bg-white gap-50 items-center justify-center w-screen'>
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
        <input type='submit' value='Submit' id='submitButton'>
    </form>
</div>";

if (isset($_SESSION['LOGGED']) && $_SESSION['LOGGED'] == true) {
    echo $htmlClient;
} else {
    header("Location: register.php");
}

//----------------------------
// Display posts and comments
//----------------------------
echo '<div class="w-screen bg-white rounded-lg shadow-lg flex flex-col items-center justify-center">';

foreach ($dataPosts as $post) {
    echo '<div class="w-[80%] bg-gray-100 rounded-lg p-4 my-4 flex flex-col items-center justify-center">';
    
  
    if ($userID) {
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
    
    echo '<div class="font-semibold text-gray-800 mb-2">';
    echo '<a href="profiles.php?post=' . htmlspecialchars($post['authorID']) . '">' . htmlspecialchars($username) . '</a>';
    echo '</div>';


    if (!empty($post['image'])) {
        $imagePath = '../server/' . htmlspecialchars($post['image']); // Use the correct path for the image
        echo '<img src="' . $imagePath . '" alt="Post Image" class="w-[400px] h-auto rounded-lg mb-4">';
    } else {
        echo '<p class="text-gray-500">No image available.</p>';
    }


    echo '<div class="mb-2"><h3 class="text-xl font-bold">' . htmlspecialchars($post['title']) . '</h3>';
    echo '<p class="text-gray-700">' . nl2br(htmlspecialchars($post['text'])) . '</p>';
    echo '</div>';
    echo '<div class="text-gray-500 text-sm">' . htmlspecialchars($post['time']) . '</div>';

    //----------------------------
    // Display comments
    //----------------------------
    echo '<div class="w-full bg-white mt-4 p-2 rounded flex items-center flex-col">';
    $sqlGetComments = 'SELECT * FROM `cms_comments` WHERE postID = :postID';
    $con = $db->prepare($sqlGetComments);
    $con->execute([':postID' => $post['postID']]);
    $comments = $con->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($comments as $comment) {
        echo '<div class="comment flex items-center mb-2">';
        
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
        echo '<span class="font-bold">' . htmlspecialchars($commenterName) . '</span>';
        echo '<p>' . htmlspecialchars($comment['message']) . '</p>';
        echo '<span class="text-gray-500 text-sm">' . htmlspecialchars($comment['time']) . '</span>';
        echo '</div>';
        echo '</div>';
    }
    
    //----------------------------
    //  comment form
    //----------------------------
    echo '<button class="bg-gray-200 hover:bg-gray-300 text-sm p-2 rounded" onclick="showCommentForm(' . $post['postID'] . ')">Add Comment</button>';
    echo '<div id="commentForm-' . $post['postID'] . '" class="hidden mt-2">';
    echo '<form action="../server/createcomment.php" method="POST">';
    echo '<input type="hidden" name="postID" value="' . htmlspecialchars($post['postID']) . '">';
    echo '<textarea name="comment" placeholder="Write a comment..." class="w-full p-2 border rounded"></textarea>';
    echo '<button type="submit" class="bg-blue-500 text-white p-2 rounded mt-2">Submit</button>';
    echo '</form>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
}
echo '</div>'; 
?>

<script> 
    function showCommentForm(postID) { document.getElementById('commentForm-' + postID).classList.toggle('hidden'); } 
    document.querySelector('#makePost').addEventListener('click', function(e) {
        document.querySelector('.hideThis').classList.remove('hidden'); 
        document.querySelector('#makePost').style.display = 'none'; 
    });
    document.querySelector('#submitButton').addEventListener('click', function(e) {
        document.querySelector('.hideThis').classList.add('hidden');
        document.querySelector('#makePost').style.display = 'block';
    });
</script>
