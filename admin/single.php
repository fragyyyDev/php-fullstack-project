<?php
include "../db/db.php";
session_start();

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id = $_GET["id"];
        } else {
            $id = 0;
        }

        $sql = "SELECT * FROM cms_users WHERE ID = :id";
        $ins_data = [":id" => $id];

        // Log the action
        $log_sql = "INSERT INTO cms_log (userID, type, message, value) VALUES (:userID, :type, :message, :value)";
        $log_data = [
            ":userID" => $_SESSION['ID'],
            ":type" => 'SELECT',
            ":message" => 'User details viewed by admin',
            ":value" => "User ID: $id"
        ];
        $log_con = $db->prepare($log_sql);
        $log_con->execute($log_data);

        $con = $db->prepare($sql);
        $con->execute($ins_data);
        $dataProfileInfo = $con->fetchAll(PDO::FETCH_ASSOC);

        $sqlGetMetaInfo = 'SELECT * FROM `cms_users_meta` WHERE userID = :id';
        $con = $db->prepare($sqlGetMetaInfo);
        $con->execute($ins_data);
        $dataMeta = $con->fetchAll(PDO::FETCH_ASSOC);

        $sqlGetPfp = 'SELECT * FROM `cms_pfp` WHERE userID = :id';
        $con = $db->prepare($sqlGetPfp);
        $con->execute($ins_data);
        $dataPfp = $con->fetchAll(PDO::FETCH_ASSOC);

        $username = $date = '';

        // Display profile information
        echo "<div class='container mx-auto p-6 bg-gray-800 text-gray-100 rounded-lg shadow-lg'>";
        echo "<h2 class='text-2xl font-bold mb-4'>User Profile</h2>";
        echo "<p class='p-4 bg-gray-700 rounded-lg shadow-md mb-4'>Email: " . htmlspecialchars($dataProfileInfo[0]['email']) . '</p>'; 
        echo "<p class='p-4 bg-gray-700 rounded-lg shadow-md mb-4'>User Since: " . htmlspecialchars($dataProfileInfo[0]['time']) . '</p>';
        echo "<p class='p-4 bg-gray-700 rounded-lg shadow-md mb-4'>Profile Photo:</p>";
        if ($dataPfp) {
            echo "<img class='w-24 h-24 rounded-full mx-auto' src='../pfp/" . htmlspecialchars($dataPfp[0]['userID']) . "." . htmlspecialchars($dataPfp[0]['extension']) . "'>";
        }
        foreach ($dataMeta as $vMeta) {
            if ($vMeta['keyWord'] == 'username') {
                echo "<p class='p-4 bg-gray-700 rounded-lg shadow-md mb-4'>Username: " . htmlspecialchars($vMeta['value']);
                $username = $vMeta['value'];
            } elseif ($vMeta['keyWord'] == 'date') {
                echo "<p class='p-4 bg-gray-700 rounded-lg shadow-md mb-4'>Date of Birth: " . htmlspecialchars($vMeta['value']);
                $date = $vMeta['value'];
            }
        }
        echo "<div class='flex justify-center mt-6'>";
        echo "<a href='index.php' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'>Go Back to Admin Menu</a>";
        echo "</div></div>";
    } else {
        header("Location: ../client/register.php");
        exit();
    }
} else {
    header("Location: ../client/register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-gray-900 text-gray-100 min-h-screen flex items-center justify-center'>
</body>
</html>
