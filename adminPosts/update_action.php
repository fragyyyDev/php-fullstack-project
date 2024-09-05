<?php 
session_start();
include "../db/db.php";

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] != 1 || $_SESSION['LOGGED'] !== true) {
        header("Location: ../client/register.php");
        exit();
    }
} else {
    header("Location: ../client/register.php");
    exit();
}

$id = $_SESSION["id"] ?? null;
$page = $_SESSION["page"] ?? null;

var_dump($page);
var_dump($id);

if ($id !== null && isset($_POST["title"]) && isset($_POST["text"]) && isset($_POST["postID"]) && isset($_POST["authorID"])) {    
    $imagePath = $_POST["image"] ?? null;
    
    if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])) {
        $target_dir = "../server/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0775, true);
        }

        $random_file_name = uniqid();
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_file_name = $random_file_name . '.' . $imageFileType;
        $target_file = $target_dir . $new_file_name;

        $uploadOk = 1;
        $error = false;

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
            $error = true;
        }

        if ($_FILES["image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
            $error = true;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
            $error = true;
        }


        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $imagePath = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
                $error = true;
            }
        } else {
            echo "Sorry, your file was not uploaded.";
            $error = true;
        }
    }

    if (!$error) {
        $sql = "UPDATE cms_posts SET title = :title, text = :text, image = :image WHERE postID = :postID AND authorID = :authorID";

        $ins = [
            ":title" => $_POST["title"],
            ":text" => $_POST["text"],
            ":image" => $imagePath, 
            ":postID" => $_POST["postID"],
            ":authorID" => $_POST["authorID"],
        ];

        $con = $db->prepare($sql);
        $con->execute($ins);

        // posilani do logu 
        $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
        $log_data = [
            ":userID" => $_SESSION['ID'],
            ":type" => 'UPDATE',
            ":message" => 'Post updated by admin' . $_POST['title'] . $_POST['text'] . $imagePath . $_POST['postID'] . $_POST['authorID'],
            ":value" => "Post ID: " . $id
        ];
        $log_con = $db->prepare($log_sql);
        $log_con->execute($log_data);


        $goBackUrl = $_SESSION['redirectBackUrl'];
        header('Location:' . $goBackUrl);
        exit();
    }
} else {
    echo "Missing required fields.";
}

