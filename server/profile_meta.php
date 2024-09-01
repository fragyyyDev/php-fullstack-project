<?php

include "../db/db.php"; 
include "../functions/hash.php"; 
include "../functions/checkEmpty.php"; 
include "../functions/setError.php"; 
session_start(); 

$username = $date = $password = $email = ""; 

if(isset($_SESSION['hashedPass'])) { 
    $password = $_SESSION['hashedPass']; 
} 
if(isset($_SESSION['email'])) { 
    $email = $_SESSION['email']; 
} 

$ins_dataID = [ 
    ":password" => $password, 
    ":email" => $email, 
]; 

$sqlGetUserId = 'SELECT ID FROM `cms_users` WHERE email = :email AND pass = :password'; 
$con = $db->prepare($sqlGetUserId); 
$con->execute($ins_dataID); 
$dataID = $con->fetch(PDO::FETCH_ASSOC); 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = ($_POST["username"]) ? $_POST["username"] : null; 
    $date = ($_POST["date"]) ? $_POST["date"] : null; 
    $error = false; 
 
    if(checkEmpty($username)){ 
        setError("username", " nebylo zadano.."); 
        $error = true; 
    } 
 
    if(checkEmpty($date)){ 
        setError("datum", "nebylo zadano.."); 
        $error = true; 
    } 

    // Check for existing records in cms_users_meta
    $sqlAreThereRecords = "SELECT * FROM `cms_users_meta` WHERE userID = :userID";
    $ins_dataRecords = [":userID" => $dataID['ID']];
    $con = $db->prepare($sqlAreThereRecords); 
    $con->execute($ins_dataRecords); 
    $dataRecords = $con->fetchAll(PDO::FETCH_ASSOC);

    if (!$error) {
        if(empty($dataRecords)) {
            // Insert USERNAME and DATE if no records exist
            $sql = "INSERT INTO `cms_users_meta` (`informationID`, `keyWord`, `value`, `userID`, `time`) VALUES 
                    (NULL, :meta_key, :value, :userID, null)"; 

            $ins_data = [
                ':meta_key' => 'username', 
                ':value' => $username, 
                ':userID' => $dataID['ID'], 
            ]; 
            $con = $db->prepare($sql); 
            $con->execute($ins_data); 

            $ins_data[':meta_key'] = 'date';
            $ins_data[':value'] = $date;
            $con->execute($ins_data); 
        } else {
            // Update existing USERNAME and DATE records
            $sql = "UPDATE `cms_users_meta` SET `value` = :value WHERE `userID` = :userID AND `keyWord` = :meta_key";
            $ins_data = [
                ':meta_key' => 'username',
                ':value' => $username,
                ':userID' => $dataID['ID'],
            ];
            $con = $db->prepare($sql);
            $con->execute($ins_data);

            $ins_data[':meta_key'] = 'date';
            $ins_data[':value'] = $date;
            $con->execute($ins_data);
        }

        // Handle Profile Picture Upload
        if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] == 0) {
            $target_dir = "../pfp/"; // Store in the 'pfp' directory
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0775, true); // Create the directory if it doesn't exist
            }

            $imageFileType = strtolower(pathinfo($_FILES["pfp"]["name"], PATHINFO_EXTENSION));
            $new_file_name = $dataID['ID'] . '.' . $imageFileType; // Rename file to userID
            $target_file = $target_dir . $new_file_name;

            $uploadOk = 1;
            $check = getimagesize($_FILES["pfp"]["tmp_name"]);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
                $error = true;
            }

            if ($_FILES["pfp"]["size"] > 5000000) {
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
                if (move_uploaded_file($_FILES["pfp"]["tmp_name"], $target_file)) {
                    // Check if a profile picture entry exists
                    $sqlCheckPfp = "SELECT * FROM `cms_pfp` WHERE `userID` = :userID";
                    $con = $db->prepare($sqlCheckPfp);
                    $con->execute([":userID" => $dataID['ID']]);
                    $pfpExists = $con->fetch(PDO::FETCH_ASSOC);

                    if (!$pfpExists) {
                        // Insert new profile picture entry
                        $sqlInsertPfp = "INSERT INTO `cms_pfp` (`ID`, `userID`, `directory`, `extension`, `time`) 
                                         VALUES (NULL, :userID, :directory, :extension, NULL)";
                        $con = $db->prepare($sqlInsertPfp);
                        $con->execute([
                            ":userID" => $dataID['ID'], 
                            ":directory" => $target_file,
                            ":extension" => $imageFileType
                        ]);
                    } else {
                        // Update existing profile picture entry
                        $sqlUpdatePfp = "UPDATE `cms_pfp` SET `directory` = :directory, `extension` = :extension, `time` = NULL
                                         WHERE `userID` = :userID";
                        $con = $db->prepare($sqlUpdatePfp);
                        $con->execute([
                            ":directory" => $target_file, 
                            ":extension" => $imageFileType,
                            ":userID" => $dataID['ID']
                        ]);
                    }

                    echo "The file has been uploaded and the path stored in the database.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    $error = true;
                }
            }
        }

        if (!$error) {
            header('Location: ../client/profile.php');
        }
    } else { 
        echo 'ERROR HAPPENED'; 
    }
}
?>