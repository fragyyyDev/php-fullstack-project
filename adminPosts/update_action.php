<?php 
session_start();
include "../db/db.php";

// Ensure user is an admin and logged in
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

// Debugging: Check if ID and page are set correctly
var_dump($page);
var_dump($id);

if ($id !== null && isset($_POST["title"]) && isset($_POST["text"]) && isset($_POST["postID"]) && isset($_POST["authorID"])) {    
    // Default image path (if no new image is uploaded)
    $imagePath = $_POST["image"] ?? null;
    
    // Check if a new image file is uploaded
    if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])) {
        // Ensure images directory exists and is writable
        $target_dir = "../server/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0775, true); // Create the directory if it doesn't exist
        }

        // Generate a unique file name
        $random_file_name = uniqid();
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_file_name = $random_file_name . '.' . $imageFileType;
        $target_file = $target_dir . $new_file_name;

        $uploadOk = 1;
        $error = false;

        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
            $error = true;
        }

        // Check file size (e.g., 5MB limit)
        if ($_FILES["image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
            $error = true;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
            $error = true;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $imagePath = $target_file; // Set the path to the new image
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
        $goBackUrl = $_SESSION['redirectBackUrl'];
        header('Location:' . $goBackUrl);
        exit();
    }
} else {
    echo "Missing required fields.";
}

