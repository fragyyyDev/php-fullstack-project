<?php 

function getPfpDirFromUserId($db, $userIDpost) {
    $insDataPfp = [
        ':userID' => $userIDpost,
    ];
    $sqlGetPfpDir = 'SELECT * FROM `cms_pfp` WHERE userID = :userID';
    $con = $db->prepare($sqlGetPfpDir);
    $con->execute($insDataPfp);
    $dataPfp = $con->fetch(PDO::FETCH_ASSOC); 

    if ($dataPfp) { 
        $dir = $dataPfp['userID'];
        $extension = $dataPfp['extension'];
        return $dir . '.' . $extension;
    } else {
        return null; 
    }
}


?>