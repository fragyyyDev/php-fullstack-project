<?php
    session_start();
    include "../db/db.php";

//--------------------------------------------------------

$sqlUsers = "SELECT COUNT(*) as total FROM cms_users";

$c = $db->prepare($sqlUsers);
$c->execute();
$count = $c->fetch(PDO::FETCH_ASSOC)['total'];
var_dump($count);

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

$sql_final = "SELECT * FROM cms_users LIMIT $offset, 10";

echo $sql_final;
$con = $db->prepare($sql_final);
$con->execute();
$data = $con->fetchAll(PDO::FETCH_ASSOC);

var_dump($data);

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
        echo "<td class='p-2 font-bold'>ID</td>";
        echo "<td class='p-2 font-bold'>email</td>";
        echo "<td class='p-2 font-bold'>password</td>";
        echo "<td class='p-2 font-bold'>time</td>";
        echo "<td class='p-2 font-bold'>admin</td>";
        echo "<td colspan='3' class='p-2 font-bold'>action</td>";
        echo "</tr>"
        ?>
        <?php 
        foreach($data as $value){
            echo "<tr>";
            echo "<td class='p-2 bg-gray-200'>" . $value["ID"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["email"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["pass"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["time"]. "</td>";
            echo "<td class='p-2 bg-gray-200'>" . $value["admin"]. '</td>';
            echo "<td class='p-2 bg-cyan-400'>". "<a  href=\"single.php?id=".$value["ID"]."\">detail</a></td>";
            echo "<td class='p-2 bg-red-400'>". "<a href=\"delete.php?id=".$value["ID"]."&page=".$page."\">delete</a></td>";
            echo "<td class='p-2 bg-green-400'>". "<a  href=\"update_form.php?id=".$value["ID"]."&page=".$page."\">update</a></td>";
            echo  "</tr>";
        }
        ?>
    </table>
</body>
</html>