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

$sqlPosts = "SELECT COUNT(*) as total FROM cms_log";

$c = $db->prepare($sqlPosts);
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

$sql_final = "SELECT * FROM cms_log 
ORDER BY time DESC 
LIMIT $offset, 10";

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
    <title>Log Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center">
    <div class="container mx-auto p-6 bg-gray-800 rounded-lg shadow-lg mt-10">
        <table class="min-w-full bg-gray-700 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-600 text-left">
                    <th class="p-3 font-bold">Log ID</th>
                    <th class="p-3 font-bold">User ID</th>
                    <th class="p-3 font-bold">Type</th>
                    <th class="p-3 font-bold">Message</th>
                    <th class="p-3 font-bold">Value</th>
                    <th class="p-3 font-bold">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $value): ?>
                    <tr class="bg-gray-800 hover:bg-gray-600">
                        <td class="p-3"><?= htmlspecialchars($value["logID"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["userID"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["type"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["message"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["value"]) ?></td>
                        <td class="p-3"><?= htmlspecialchars($value["time"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-center gap-4 mt-6">
        <a href="../admin/index.php" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">User Management</a>
        <a href="../log/index.php" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Change Log</a>
    </div>
</body>
</html>
