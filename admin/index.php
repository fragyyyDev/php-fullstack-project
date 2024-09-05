<?php
session_start();
include "../db/db.php";

//--------------------------------------------------------

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
        // Admin is logged in
    } else {
        header("Location: ../client/register.php");
    }
} else {
    header("Location: ../client/register.php");
}

$sqlUsers = "SELECT COUNT(*) as total FROM cms_users";

$c = $db->prepare($sqlUsers);
$c->execute();
$count = $c->fetch(PDO::FETCH_ASSOC)['total'];

$totalPages = ceil($count / 10);

echo '<div class="flex justify-center p-4">';
for ($i = 1; $i <= $totalPages; $i++) {
    echo "<a class='p-2 text-blue-500 hover:text-blue-700' href=\"index.php?p=" . $i . "\">" . $i . "</a>";
}
echo '</div>';

$page = isset($_GET["p"]) && !empty($_GET["p"]) && $_GET["p"] > 0 ? (int)$_GET["p"] : 1;
$offset = ($page - 1) * 10;

$sql_final = "SELECT * FROM cms_users LIMIT $offset, 10";

$con = $db->prepare($sql_final);
$con->execute();
$data = $con->fetchAll(PDO::FETCH_ASSOC);

//---------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center">
    <div class="container mx-auto p-6 bg-gray-800 rounded-lg shadow-lg mt-10">
        <table class="min-w-full bg-gray-700 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-600 text-left">
                    <th class="p-3 font-bold">ID</th>
                    <th class="p-3 font-bold">Email</th>
                    <th class="p-3 font-bold">Password</th>
                    <th class="p-3 font-bold">Time</th>
                    <th class="p-3 font-bold">Admin</th>
                    <th colspan='3' class="p-3 font-bold">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $value): ?>
                    <tr class="bg-gray-800 hover:bg-gray-600">
                        <td class="p-3"><?= htmlspecialchars($value["ID"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["email"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["pass"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["time"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["admin"]) ?></td>
                        <td class="p-3"><a href="single.php?id=<?= htmlspecialchars($value["ID"]) ?>" class="text-cyan-400 hover:text-cyan-600">Detail</a></td>
                        <td class="p-3"><a href="delete.php?id=<?= htmlspecialchars($value["ID"]) ?>&page=<?= htmlspecialchars($page) ?>" class="text-red-400 hover:text-red-600">Delete</a></td>
                        <td class="p-3"><a href="update_form.php?id=<?= htmlspecialchars($value["ID"]) ?>&page=<?= htmlspecialchars($page) ?>" class="text-green-400 hover:text-green-600">Update</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="flex justify-around mt-6">
            <a href="../adminPosts/index.php" class='bg-gray-600 hover:bg-gray-500 text-white py-2 px-4 rounded'>Post Management</a>
            <a href="../log/index.php" class='bg-gray-600 hover:bg-gray-500 text-white py-2 px-4 rounded'>Change Log</a>
        </div>
    </div>
</body>
</html>
