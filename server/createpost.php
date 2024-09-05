<?php
include "../db/db.php";
include "../functions/hash.php";
include "../functions/checkEmpty.php";
include "../functions/setError.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = ($_POST["title"]) ?? null;
    $text = ($_POST["text"]) ?? null;
    $error = false;

    if (checkEmpty($title)) {
        $error = true;
        setError("Error", "Vyplň prosím title");
    }

    if (checkEmpty($text)) {
        $error = true;
        setError("Error", "Vyplň prosím text");
    }

    // Ensure images directory exists and is writable
    $target_dir = "images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0775, true); // Create the directory if it doesn't exist
    }

    // Generate a random integer as the new filename
    $random_file_name = uniqid(); // You can also use `random_int(1000, 9999)` for shorter integers
    $imageFileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
    $new_file_name = $random_file_name . '.' . $imageFileType;
    $target_file = $target_dir . $new_file_name;

    $uploadOk = 1;

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
        $error = true;
    }

    // Check file size (e.g., 5MB limit)
    if ($_FILES["file"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
        $error = true;
    }

    // Allow certain file formats (e.g., jpg, png, jpeg, gif)
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
        $error = true;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        $error = true;
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $stmt = $db->prepare("INSERT INTO cms_posts (authorID, title, text, image) VALUES (:userID, :title, :text, :image)");
            $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
            $log_data = [
                ":userID" => $_SESSION['ID'],
                ":type" => 'INSERT',
                ":message" => 'Post made by a user',
                ":value" => "Post ID:   " . $title . " " . $text . " " .  $target_file
            ];
              $log_con = $db->prepare($log_sql);
             $log_con->execute($log_data);

            
            if (isset($_SESSION['hashedPass']) && isset($_SESSION['email'])) {
                $password = $_SESSION['hashedPass'];
                $email = $_SESSION['email'];

                $ins_dataID = [
                    ":password" => $password,
                    ":email" => $email,
                ];

                $sqlGetUserId = 'SELECT ID FROM `cms_users` WHERE email = :email AND pass = :password';
                $conID = $db->prepare($sqlGetUserId);
                $conID->execute($ins_dataID);
                $dataID = $conID->fetch(PDO::FETCH_ASSOC);
                $id = $dataID['ID'];

                // Execute the statement
                $stmt->execute([
                    ":title" => $title,
                    ":text" => $text,
                    ":image" => $target_file,
                    ":userID" => $id,
                ]);

                echo "The file has been uploaded and the path stored in the database.";
                header('location: ../client/main.php');
            } else {
                echo "User session data not found.";
                $error = true;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
            $error = true;
        }
    }
} else {
    echo "Error: " . $error;
}
?>
