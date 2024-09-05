<?php 
include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
include "../functions/getPfpDirFromUserId.php";

session_start();
$_SESSION['redirectBackUrl'] = $_SERVER['REQUEST_URI'];


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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <header class="w-full bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <ul class="flex space-x-8">
                <li><a href="#" class="text-gray-300 hover:text-white">Main Page</a></li>
                <li><a href="profile.php" class="text-gray-300 hover:text-white">Profile</a></li>
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                    <li><a href="../admin/index.php" class="text-gray-300 hover:text-white">Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    <main class="container mx-auto p-4">
        <?php if (isset($_SESSION['LOGGED']) && $_SESSION['LOGGED'] == true): ?>
            <div class="flex justify-center mb-4">
                <button id="makePost" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Make a Post</button>
                <form action="../server/createpost.php" method="post" enctype="multipart/form-data" class="hideThis hidden ml-4">
                    <input type="text" name="title" placeholder="Title" class="p-2 border border-gray-700 rounded mb-2 block w-full">
                    <input type="text" name="text" placeholder="Text" class="p-2 border border-gray-700 rounded mb-2 block w-full">
                    <input type="file" name="file" id="file" required class="mb-2">
                    <input type="submit" value="Submit" id="submitButton" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
                </form>
            </div>
        <?php else: ?>
            <p class="text-red-500 text-center">You need to be logged in to view this page. <a href="register.php" class="underline">Register here</a>.</p>
        <?php endif; ?>

        <div class="bg-gray-800 p-4 rounded-lg shadow-lg">
            <?php foreach ($dataPosts as $post): ?>
                <div class="bg-gray-700 p-4 rounded-lg mb-4">
                    <?php if ($userID): 
                        $profileImage = getPfpDirFromUserId($db, $post['authorID']);
                        $profileImageSrc = htmlspecialchars($profileImage ?? 'default.jpg');
                    ?>
                        <img class="w-16 h-16 rounded-full mb-4" src="../pfp/<?php echo $profileImageSrc; ?>" alt="Profile Picture">
                    <?php else: ?>
                        <img class="w-16 h-16 rounded-full mb-4" src="../pfp/default.jpg" alt="Default Profile Picture">
                    <?php endif; ?>

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
                    <div class="font-semibold text-gray-100 mb-2">
                        <a href="profiles.php?post=<?php echo htmlspecialchars($post['authorID']); ?>" class="hover:underline"><?php echo htmlspecialchars($username); ?></a>
                    </div>

                    <?php if (!empty($post['image'])): ?>
                        <img src="../server/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="w-full max-w-md mx-auto rounded-lg mb-4">
                    <?php else: ?>
                        <p class="text-gray-500">No image available.</p>
                    <?php endif; ?>

                    <div class="mb-2">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="text-gray-300"><?php echo nl2br(htmlspecialchars($post['text'])); ?></p>
                    </div>
                    <div class="text-gray-500 text-sm"><?php echo htmlspecialchars($post['time']); ?></div>

                    <div class="w-full bg-gray-900 mt-4 p-2 rounded flex flex-col">
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
                                <img class="w-8 h-8 rounded-full" src="../pfp/<?php echo htmlspecialchars($commenterPfp); ?>" alt="Commenter Profile Picture">

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
                                    <span class="font-bold"><?php echo htmlspecialchars($commenterName); ?></span>
                                    <p><?php echo htmlspecialchars($comment['message']); ?></p>
                                    <span class="text-gray-500 text-sm"><?php echo htmlspecialchars($comment['time']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <button class="bg-gray-600 hover:bg-gray-700 text-sm p-2 rounded" onclick="showCommentForm(<?php echo $post['postID']; ?>)">Add Comment</button>
                        <div id="commentForm-<?php echo $post['postID']; ?>" class="hidden mt-2">
                            <form action="../server/createcomment.php" method="POST">
                                <input type="hidden" name="postID" value="<?php echo htmlspecialchars($post['postID']); ?>">
                                <textarea name="comment" placeholder="Write a comment..." class="w-full p-2 border border-gray-700 rounded"></textarea>
                                <button type="submit" class="bg-blue-600 text-white p-2 rounded mt-2 hover:bg-blue-700">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script>
        function showCommentForm(postID) {
            document.getElementById('commentForm-' + postID).classList.toggle('hidden');
        }
    </script>
</body>
</html>
