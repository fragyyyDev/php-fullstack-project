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

    // Redirect back to main page
    header('Location: ' . $_SESSION['redirectBackUrl']);
} else {
    echo "Error: Missing required fields or not logged in.";
}
?>