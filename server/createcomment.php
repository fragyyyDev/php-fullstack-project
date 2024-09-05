<?php
include "../db/db.php";
session_start();

if (isset($_POST['comment'], $_POST['postID']) && isset($_SESSION['ID'])) {
    $comment = $_POST['comment'];
    $postID = $_POST['postID'];
    $userID = $_SESSION['ID'];

    $sqlInsertComment = "INSERT INTO cms_comments (ID, userID, postID, message, time) VALUES (NULL, :userID, :postID, :content, NOW())";
    $stmt = $db->prepare($sqlInsertComment);
    $stmt->execute([
        ':content' => $comment,
        ':postID' => $postID,
        ':userID' => $userID
    ]);

    // posilani do logu 
    $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
    $log_data = [
        ":userID" => $userID,
        ":type" => 'INSERT',
        ":message" => 'Post commented by' . $userID,
        ":value" => "Post ID: " . $postID
    ];
    $log_con = $db->prepare($log_sql);
    $log_con->execute($log_data);

    header('Location: ' . $_SESSION['redirectBackUrl']);
} else {
    echo "Error: Missing required fields or not logged in.";
}
?>