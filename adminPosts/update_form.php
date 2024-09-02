
<?php

include "../db/db.php";
session_start();

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {

    } else {
        header("Location: ../client/register.php");
    }
} else {
    header("Location: ../client/register.php");
}


//-----------------------------------------------------------------------------------------

if(isset($_GET) && is_numeric($_GET["id"])){
    $id = $_GET['id'];
    $_SESSION["id"] = $id;
    $sql = "SELECT * FROM cms_posts WHERE postID = :id";
    $ins = [
        ':id'=>$id
    ];
    $con = $db->prepare($sql);
    $con->execute($ins);
    $data = $con->fetchAll(PDO::FETCH_ASSOC);
    if(count($data) == 1 && isset($data[0]["title"]) && isset($data[0]["text"]) && isset($data[0]["postID"])){
        $postID = $data[0]["postID"];
        $authorID = $data[0]["authorID"];
        $title = $data[0]["title"];
        $text = $data[0]["text"];
        $time = $data[0]["time"];
        $image = $data[0]["image"];
    }
}

//-----------------------------------------------------------------------------------------


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Post</title>
</head>
<body>
    <form action="update_action.php" method="post" enctype="multipart/form-data" class="formular">
        <p>Post ID (Do not change)</p>
        <input type="text" name="postID" readonly value="<?php echo isset($postID) ? htmlspecialchars($postID) : ''; ?>"> <br>

        <p>Author ID (Do not change)</p>
        <input type="text" name="authorID" readonly value="<?php echo isset($authorID) ? htmlspecialchars($authorID) : ''; ?>"> <br>

        <p>Title</p>
        <input type="text" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"> <br>

        <p>Text</p>
        <textarea name="text"><?php echo isset($text) ? htmlspecialchars($text) : ''; ?></textarea><br>

        <p>Current Image</p>
        <?php if (isset($image) && !empty($image)): ?>
            <img src="../server/<?php echo htmlspecialchars($image); ?>" alt="Current Image" style="max-width: 200px;"><br>
            <input type="hidden" name="image" value="<?php echo htmlspecialchars($image); ?>">
        <?php else: ?>
            <p>No image currently set.</p>
        <?php endif; ?>
        
        <p>New Image (Optional)</p>
        <input type="file" name="image"><br>

        <input type="submit" value="Update Post" class="submit">
    </form>
</body>
</html>