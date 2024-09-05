<?php
include "../db/db.php";
session_start();

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] != 1 || $_SESSION['LOGGED'] != true) {
        header("Location: ../client/register.php");
        exit();
    }
} else {
    header("Location: ../client/register.php");
    exit();
}

//-----------------------------------------------------------------------------------------

if (isset($_GET) && is_numeric($_GET["id"]) && is_numeric($_GET["page"])) {
    $id = $_GET['id'];
    $page = $_GET['page'];
    $_SESSION["id"] = $id;
    $_SESSION["page"] = $page;
    
    $sql = "SELECT * FROM cms_users WHERE ID = :id";
    $ins = [':id' => $id];
    $con = $db->prepare($sql);
    $con->execute($ins);
    $data = $con->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($data) == 1 && isset($data[0]["email"]) && isset($data[0]["password"]) && isset($data[0]["admin"])) {
        $email = $data[0]["email"];
        $password = $data[0]["password"];
        $isAdmin = $data[0]["admin"];
    }
}

//-----------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Update User</h2>
        <form action="update_action.php" method="post" class="space-y-4">
            <div>
                <label for="email" class="block text-lg font-medium">Email</label>
                <input type="email" id="email" name="email" class="mt-1 p-2 w-full bg-gray-700 text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>
            <div>
                <label for="password" class="block text-lg font-medium">Password</label>
                <input type="password" id="password" name="password" class="mt-1 p-2 w-full bg-gray-700 text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>">
            </div>
            <div>
                <label for="isAdmin" class="block text-lg font-medium">Admin Status</label>
                <input type="text" id="isAdmin" name="isAdmin" class="mt-1 p-2 w-full bg-gray-700 text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($isAdmin) ? htmlspecialchars($isAdmin) : ''; ?>">
            </div>
            <div class="flex justify-center">
                <input type="submit" value="Update" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            </div>
        </form>
    </div>
</body>
</html>
