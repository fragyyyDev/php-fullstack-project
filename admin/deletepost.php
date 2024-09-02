<?php 
session_start();
include "../db/db.php";
// dodelat to, ze to kontroluje jestli je post toho dotycnyho jinak nekdo si da do kotvy ?id=5 a smaze nekomu post

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_SESSION['ID'])) {
    $id = $_GET['id'];
    $userID = $_SESSION['ID'];
    $sqlCheckOwnership = "SELECT * FROM cms_posts WHERE postID = :id AND authorID = :userID";
    $checkIns = [
        ":id" => $id,
        ":userID" => $userID
    ];
    $checkCon = $db->prepare($sqlCheckOwnership);
    $checkCon->execute($checkIns);
    $post = $checkCon->fetch(PDO::FETCH_ASSOC);

    if ($post) { // If the post exists and is owned by the logged-in user
        $sql = "DELETE FROM cms_posts WHERE postID = :id";
        $ins = [
            ":id" => $id
        ];
        $con = $db->prepare($sql);
        $con->execute($ins);

        // Redirect back to the previous page
        $goBackUrl = $_SESSION['redirectBackUrl'];
        header('Location:' . $goBackUrl);
        exit();
    } else {
        echo 'You do not have permission to delete this post.';
    }
} else {
    echo 'No post to delete or invalid post ID. or you are not logged in and we cant figure out if its your post';
}
?>