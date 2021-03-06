<?php
require("config/database.php");
date_default_timezone_set('Europe/Paris');
session_start();

if(empty($_SESSION['loggedin']))
    header('Location: /user/login.php');

if ((isset($_POST['tookAphoto']))){
    $photo = $_POST['photo'];
    $sticker = $_POST['sticker'];
    $photo = explode(',', $photo);
    $data = base64_decode($photo[1]);
    $filePath = 'public/upload/'.date("YmdHis").'.png';
    file_put_contents($filePath, $data);


    $photoCopy = imagecreatefrompng($filePath);
    $stickerCopy = imagecreatefrompng($sticker);
    $resized_mask = imagecreatetruecolor(265, 250);
    $trans_color = imagecolorallocatealpha($resized_mask, 0, 0, 0, 127);
    imagefill($resized_mask, 0, 0, $trans_color);
    imagealphablending($stickerCopy, true);
    imagesavealpha($stickerCopy, true);
    $src_x = imagesx($stickerCopy);
    $src_y = imagesy($stickerCopy);
    imagecopyresampled($resized_mask, $stickerCopy, 0, 0, 0, 0, 265, 250, $src_x, $src_y);
    imagecopy($photoCopy, $resized_mask, 0, 0, 0, 0, 265, 250);
    imagepng($photoCopy, $filePath);
    imagedestroy($photoCopy);
  

    $username = ($_SESSION['username']);
    $sql = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $sql->bindParam(":username", $username);
    $sql->execute();
    $profile = $sql->fetchAll();
    foreach ($profile as $user){
        $addPhoto = "INSERT INTO picture (id_user, img) VALUE ('".$user['id']."', '".$filePath."')";
        $pdo->query($addPhoto);
        header('location: camera.php');
    }
}

?> 
<?php ob_start(); ?>
<div class="background">
    <div id="camera">
        <div id="video_div">
            <div id="live_video">
                <img src="public/stickers/poop.png" id="overlay">
            </div>
            <video id="video"></video>
        </div>
        <div id="sticker_div">
            <img src="public/stickers/poop.png" class="stickerImg active">
            <img src="public/stickers/peach.png" class="stickerImg">
            <img src="public/stickers/watermelon.png" class="stickerImg">
            <img src="public/stickers/pig.png" class="stickerImg">
            <img src="public/stickers/callme.png" class="stickerImg">
        </div>

        <form method="POST" action="" onsubmit=takePhoto();>
            <input id="photo" name="photo" type="hidden" value="">
            <input id="sticker" name="sticker" type="hidden" value="">
            <input id="snap" style="display:none;" type="submit"  name="tookAphoto" value="" >
        </form>

        <canvas style="display:none" id="canvas" width=640 height=480></canvas>
        <div id='camera_gallery'>
        <?php
            $stmt = $pdo->prepare("SELECT img, id_img FROM picture WHERE id_user = :id_user ORDER BY date DESC");
            $stmt->bindParam(":id_user", $_SESSION['id']);
    		$stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($res as $photos){
                echo "<img src='".$photos['img']."' id='photo' id='".$photos['id_img']."'>";
            }
        ?>
        </div>
        <div><p id="text-camera">No camera ? No problem !<a style="color: #EFB4E4;" href="upload.php"> <b>Click here :)</b></a></p></div>
        <canvas style="display:none" id="canvasCopy" width="640" height="480"></canvas>
    </div>
</div><br/><br/>

<script src="/public/js/camera.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("template.php"); ?>