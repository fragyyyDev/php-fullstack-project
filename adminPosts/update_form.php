<?php
include "../db/db.php";
session_start();

if (isset($_SESSION['admin']) && isset($_SESSION['LOGGED'])) {
    if ($_SESSION['admin'] == 1 && $_SESSION['LOGGED'] == true) {
        // User is admin and logged in
    } else {
        header("Location: ../client/register.php");
        exit();
    }
} else {
    header("Location: ../client/register.php");
    exit();
}

// Fetch post details if ID is valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['id'] = $id;
    
    $sql = "SELECT * FROM cms_posts WHERE postID = :id";
    $ins = [':id' => $id];
    $con = $db->prepare($sql);
    $con->execute($ins);
    $data = $con->fetchAll(PDO::FETCH_ASSOC);

    if (count($data) == 1) {
        $postID = $data[0]['postID'];
        $authorID = $data[0]['authorID'];
        $title = $data[0]['title'];
        $text = $data[0]['text'];
        $time = $data[0]['time'];
        $image = $data[0]['image'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-primary { background-color: #1e40af; } /* Blue */
        .bg-secondary { background-color: #374151; } /* Medium Grey */
        .bg-tertiary { background-color: #111827; } /* Black */
        .text-primary { color: #60a5fa; } /* Light Blue */
        .text-secondary { color: #e5e7eb; } /* Lighter Grey */
        .border-primary { border-color: #1e40af; } /* Blue */
    </style>
</head>
<body class="bg-tertiary text-secondary flex items-center justify-center min-h-screen">
    <div class="w-full max-w-lg bg-secondary p-6 rounded-lg shadow-lg">
        <h1 class="text-primary text-2xl font-bold mb-4">Update Post</h1>
        <form action="update_action.php" method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="postID">Post ID (Do not change)</label>
                <input type="text" name="postID" id="postID" readonly value="<?php echo isset($postID) ? htmlspecialchars($postID) : ''; ?>" class="block w-full p-2 border border-primary rounded bg-tertiary text-secondary">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="authorID">Author ID (Do not change)</label>
                <input type="text" name="authorID" id="authorID" readonly value="<?php echo isset($authorID) ? htmlspecialchars($authorID) : ''; ?>" class="block w-full p-2 border border-primary rounded bg-tertiary text-secondary">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" id="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" class="block w-full p-2 border border-primary rounded bg-tertiary text-secondary">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="text">Text</label>
                <textarea name="text" id="text" class="block w-full p-2 border border-primary rounded bg-tertiary text-secondary"><?php echo isset($text) ? htmlspecialchars($text) : ''; ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="currentImage">Current Image</label>
                <?php if (isset($image) && !empty($image)): ?>
                    <img src="../server/<?php echo htmlspecialchars($image); ?>" alt="Current Image" class="block max-w-xs mb-2">
                    <input type="hidden" name="image" value="<?php echo htmlspecialchars($image); ?>">
                <?php else: ?>
                    <p class="text-gray-400">No image currently set.</p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="newImage">New Image (Optional)</label>
                <input type="file" name="image" id="newImage" class="block w-full p-2 border border-primary rounded bg-tertiary text-secondary">
            </div>

            <div>
                <input type="submit" value="Update Post" class="bg-primary text-white p-2 rounded hover:bg-blue-700 cursor-pointer">
            </div>
        </form>
    </div>
</body>
</html>
