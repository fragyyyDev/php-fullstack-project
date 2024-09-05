<?php 
include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
include "../functions/getPfpDirFromUserId.php";

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

// Get user profile information
$sqlGetProfileInfo = 'SELECT * FROM `cms_users` WHERE pass = :password AND email = :email';
$con = $db->prepare($sqlGetProfileInfo);
$con->execute([':password' => $password, ':email' => $email]);
$dataProfileInfo = $con->fetch(PDO::FETCH_ASSOC);

if (!$dataProfileInfo) {
    die("Profile information not found.");
}

$_SESSION['ID'] = $dataProfileInfo['ID'];

// Get meta information and profile picture
$sqlGetMetaInfo = 'SELECT * FROM `cms_users_meta` WHERE userID = :userID';
$sqlGetPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
$con = $db->prepare($sqlGetMetaInfo);
$con->execute([':userID' => $dataProfileInfo['ID']]);
$dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);

$con = $db->prepare($sqlGetPfp);
$con->execute([':userID' => $dataProfileInfo['ID']]);
$dataPfp = $con->fetch(PDO::FETCH_ASSOC);

// Get user posts
$sqlGetPosts = 'SELECT * FROM `cms_posts` WHERE authorID = :userID';
$con = $db->prepare($sqlGetPosts);
$con->execute([':userID' => $dataProfileInfo['ID']]);
$dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Profile Page</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .hideThis { display: none; }
        .header { background-color: #1f2937; } /* Dark Grey */
        .text-header { color: #60a5fa; } /* Light Blue */
        .text-main { color: #e5e7eb; } /* Lighter Grey */
        .bg-container { background-color: #111827; } /* Black */
        .bg-primary { background-color: #1e40af; } /* Blue */
        .bg-secondary { background-color: #374151; } /* Medium Grey */
    </style>
</head>
<body class="bg-container text-main">
    <div class="h-screen w-screen">
        <div class="header w-screen h-16 flex items-center justify-center shadow-md">
            <ul class="flex w-screen gap-8 items-center justify-center">
                <li><a href="main.php" class="text-header hover:text-white">Main Page</a></li>
                <li><a href="profile.php" class="text-header hover:text-white">Profile</a></li>
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                    <li><a href="../admin/index.php" class="text-header hover:text-white">Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="flex items-center justify-center mt-4">
            <button class="bg-primary text-white shadow-xl p-4 rounded" id='setupProfile'>
                <a href='settings.php' class="no-underline text-white">Setup profile info</a>
            </button>
        </div>
        <div class="w-screen max-w-5xl mx-auto mt-8">
            <p class="p-4 bg-secondary shadow-lg rounded mb-4 text-center">Email = <?= htmlspecialchars($dataProfileInfo['email']) ?></p>
            <p class="p-4 bg-secondary shadow-lg rounded mb-4 text-center">User since = <?= htmlspecialchars($dataProfileInfo['time']) ?></p>
            <p class="p-4 bg-secondary shadow-lg rounded mb-4 text-center">My Profile photo</p>
            <img class='w-16 h-16 rounded-full mx-auto mb-4' src='../pfp/<?= htmlspecialchars($dataPfp['userID'] ?? 'default') . "." . htmlspecialchars($dataPfp['extension'] ?? 'jpg') ?>' alt='Profile Picture'>
            
            <?php foreach ($dataMeta as $meta): ?>
                <?php if ($meta['keyWord'] == 'username'): ?>
                    <p class="p-4 bg-secondary shadow-lg rounded mb-4 text-center">Username = <?= htmlspecialchars($meta['value']) ?></p>
                <?php elseif ($meta['keyWord'] == 'date'): ?>
                    <p class="p-4 bg-secondary shadow-lg rounded mb-4 text-center">Date of <?= htmlspecialchars($meta['value']) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <div class="bg-primary rounded-lg shadow-lg p-4">
                <?php foreach ($dataPosts as $post): ?>
                    <div class="bg-secondary rounded-lg p-4 mb-4">
                        <div class="flex items-center mb-4">
                            <img class="w-16 h-16 rounded-full mr-4" src="../pfp/<?= htmlspecialchars(getPfpDirFromUserId($db, $post['authorID']) ?? 'default.jpg') ?>" alt="Profile Picture">
                            <div>
                                <?php
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
                                ?>
                                <div class="font-semibold text-header mb-2">
                                    <a href="profiles.php?post=<?= htmlspecialchars($post['authorID']) ?>" class="hover:text-blue-400"><?= htmlspecialchars($username) ?></a>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($post['image'])): ?>
                            <img src="../server/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="w-full max-w-md mx-auto rounded-lg mb-4">
                        <?php else: ?>
                            <p class="text-gray-500">No image available.</p>
                        <?php endif; ?>
                        <div class="mb-2">
                            <h3 class="text-xl font-bold text-white"><?= htmlspecialchars($post['title']) ?></h3>
                            <p class="text-main"><?= nl2br(htmlspecialchars($post['text'])) ?></p>
                        </div>
                        <div class="text-gray-500 text-sm"><?= htmlspecialchars($post['time']) ?></div>
                        <div class="w-full bg-white mt-4 p-2 rounded flex items-center flex-col">
                            <?php
                            $sqlGetComments = 'SELECT * FROM `cms_comments` WHERE postID = :postID';
                            $con = $db->prepare($sqlGetComments);
                            $con->execute([':postID' => $post['postID']]);
                            $comments = $con->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment flex items-center mb-2">
                                    <?php
                                    $sqlGetCommenterPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
                                    $con = $db->prepare($sqlGetCommenterPfp);
                                    $con->execute([':userID' => $comment['userID']]);
                                    $commenterPfpData = $con->fetch(PDO::FETCH_ASSOC);
                                    $commenterPfp = $commenterPfpData ? $commenterPfpData['userID'] . '.' . $commenterPfpData['extension'] : 'default.jpg';
                                    ?>
                                    <img class='w-8 h-8 rounded-full' src='../pfp/<?= htmlspecialchars($commenterPfp) ?>' alt='Commenter Profile Picture'>
                                    <?php
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
                                    ?>
                                    <div class="ml-3">
                                        <span class="font-bold text-header"><?= htmlspecialchars($commenterName) ?></span>
                                        <p class="text-main"><?= htmlspecialchars($comment['message']) ?></p>
                                        <span class="text-gray-500 text-sm"><?= htmlspecialchars($comment['time']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
