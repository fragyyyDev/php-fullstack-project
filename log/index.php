<?php
    session_start();
    include "../db/db.php";

//--------------------------------------------------------

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {

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

for($i = 1; $i <= $totalPages; $i++) {
    echo "<a class='p-2 text-black' href=\"index.php?p=".$i."\">".$i." |  " ."</a>";
}

if(isset($_GET["p"]) && !empty($_GET["p"]) && $_GET["p"] > 0) {
    $page = (int)$_GET["p"];
} else {
    $page = 1;
}

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
    <title>Document</title>
    <style>
        table,td,th {
            border: 1px solid black;
            
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <table>
        <?php
        echo "<tr>";
        echo "<td class='p-2 font-bold'>logID</td>";
        echo "<td class='p-2 font-bold'>userID</td>";
        echo "<td class='p-2 font-bold'>type</td>";
        echo "<td class='p-2 font-bold'>message</td>";
        echo "<td class='p-2 font-bold'>value</td>";
        echo "<td class='p-2 font-bold'>time</td>";
        echo "</tr>"
        ?>
        <?php 
        foreach($data as $value){
            echo "<tr>";
            echo "<td class='p-2 bg-gray-200'>" . $value["logID"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["userID"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["type"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["message"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["value"]. '</td>';
            echo "<td class='p-2 bg-gray-200'>" . $value["time"]. '</td>';
            echo  "</tr>";
        }
        ?>
    </table>
</body>
</html>