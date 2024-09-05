<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        .header { background-color: #1f2937; } /* Dark Grey */
        .text-header { color: #60a5fa; } /* Light Blue */
        .text-main { color: #e5e7eb; } /* Lighter Grey */
        .bg-container { background-color: #111827; } /* Black */
        .bg-primary { background-color: #1e40af; } /* Blue */
        .bg-secondary { background-color: #374151; } /* Medium Grey */
    </style>
</head>
<body class="bg-container text-main">
    <div class='header w-screen h-16 flex items-center justify-center shadow-md'>
        <ul class='flex gap-8 items-center justify-center w-screen'>
            <li><a href='main.php' class="text-header hover:text-white">Main Page</a></li>
            <li><a href='profile.php' class="text-header hover:text-white">My Profile</a></li>
        </ul>
    </div>

    <?php 
    include "../db/db.php";
    include "../functions/getPfpDirFromUserId.php";
    session_start();

    if (!isset($_SESSION['LOGGED']) || $_SESSION['LOGGED'] !== true) {
        header("Location: register.php");
        exit();
    }

    if (!isset($_GET['post'])) {
        header("Location: main.php");
        exit();
    }

    $authorID = htmlspecialchars($_GET['post']);

    $ins_dataProfile = [":userID" => $authorID];

    // Get user profile info
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

    echo "<p class='p-4 bg-secondary shadow-lg text-center'> Email = " . $dataProfileInfo[0]['email'] . '</p>'; 
    echo "<p class='p-4 bg-secondary shadow-lg text-center'>User since = " . $dataProfileInfo[0]['time'] . '</p>';
    echo "<p class='p-4 bg-secondary shadow-lg text-center'>My Profile photo</p>";

    if($dataPfp) {
        echo "<img class='w-16 h-16 rounded-full mx-auto' src='../pfp/" . $dataPfp[0]['userID'] . "." . $dataPfp[0]['extension'] . "'>";
    }

    foreach($dataMeta as $vMeta) {
        if($vMeta['keyWord'] == 'username') {
            echo "<p class='p-4 bg-secondary shadow-lg text-center'>Username = " . $vMeta['value'] . '</p>';
            $username = $vMeta['value'];
        } elseif($vMeta['keyWord'] == 'date') {
            echo "<p class='p-4 bg-secondary shadow-lg text-center'>Date of " . $vMeta['value'] . '</p>';
        }
    }

    $sqlGetPosts = 'SELECT * FROM `cms_posts` WHERE authorID = :userID';
    $con = $db->prepare($sqlGetPosts);
    $con->execute($ins_dataProfile);
    $dataPosts = $con->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="w-screen bg-secondary rounded-lg shadow-lg overflow-hidden flex items-center flex-col justify-center">';
    echo '<div class="divide-y divide-gray-200">';

    foreach ($dataPosts as $post) {
        echo '<div class="w-screen bg-primary rounded-lg p-4 my-4 flex flex-col items-center justify-center">';

        if ($post['authorID']) {
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

        echo '<div class="font-semibold text-header mb-2">';
        echo '<a href="profiles.php?post=' . htmlspecialchars($post['authorID']) . '" class="hover:text-blue-400">' . htmlspecialchars($username) . '</a>';
        echo '</div>';

        if (!empty($post['image'])) {
            $imagePath = '../server/' . htmlspecialchars($post['image']);
            echo '<img src="' . $imagePath . '" alt="Post Image" class="w-[400px] h-auto rounded-lg mb-4">';
        } else {
            echo '<p class="text-gray-500">No image available.</p>';
        }

        echo '<div class="mb-2"><h3 class="text-xl font-bold text-white">' . htmlspecialchars($post['title']) . '</h3>';
        echo '<p class="text-main">' . nl2br(htmlspecialchars($post['text'])) . '</p>';
        echo '</div>';
        echo '<div class="text-gray-500 text-sm">' . htmlspecialchars($post['time']) . '</div>';

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
            echo '<span class="font-bold text-header">' . htmlspecialchars($commenterName) . '</span>';
            echo '<p class="text-main">' . htmlspecialchars($comment['message']) . '</p>';
            echo '<span class="text-gray-500 text-sm">' . htmlspecialchars($comment['time']) . '</span>';
            echo '</div>';
            echo '</div>';
        }

        echo '<button class="bg-secondary hover:bg-primary text-white text-sm p-2 rounded" onclick="showCommentForm(' . $post['postID'] . ')">Add Comment</button>';
        echo '<div id="commentForm-' . $post['postID'] . '" class="hidden mt-2">';
        echo '<form action="../server/createcomment.php" method="POST">';
        echo '<input type="hidden" name="postID" value="' . htmlspecialchars($post['postID']) . '">';
        echo '<textarea name="comment" placeholder="Write a comment..." class="w-full p-2 border rounded bg-secondary text-white"></textarea>';
        echo '<button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded mt-2">Submit</button>';
        echo '</form>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    ?>

    <script> 
        function showCommentForm(postID) { document.getElementById('commentForm-' + postID).classList.toggle('hidden'); } 
    </script>
</body>
</html>
