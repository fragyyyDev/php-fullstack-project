<?php 
session_start();
include "../db/db.php";

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

    if ($post) { 
        $sql = "DELETE FROM cms_posts WHERE postID = :id";
        $ins = [
            ":id" => $id
        ];
        $con = $db->prepare($sql);
        $con->execute($ins);

        $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
        $log_data = [
            ":userID" => $userID,
            ":type" => 'DELETE',
            ":message" => 'Post has been deleted by his owner',
            ":value" => "Post ID: $id"
        ];
        $log_con = $db->prepare($log_sql);
        $log_con->execute($log_data);

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

