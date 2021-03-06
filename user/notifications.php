<?php
session_start();
require("../config/database.php");

if(empty($_SESSION['loggedin']))
    header('Location: ../index.php');

$query = $pdo->prepare("SELECT notif FROM users WHERE id = :id");
$query->bindParam(':id', $_SESSION['id']);
$query->execute();
$data = $query->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['update'])){
    $checkbox = $_POST['notif'];
    if ($checkbox == NULL){
        $notif = 0;
    }else{
        $notif = 1;
    }
    $query = $pdo->prepare("UPDATE users SET notif = :notif WHERE id = :id");
    $query->bindParam(':notif', $notif);
    $query->bindParam(':id', $_SESSION['id']);
    $query->execute();
    $message = "Updated ;)";
    header("Refresh: 1; url=notifications.php");
}



?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
    <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">Hey Kitty </h2>
        <div id="account">
            <nav id="account_nav">
                <a id="EdPro" href="account.php">Edit Profile</a>
                <a id="EdPwd" href="modifyPassw.php">Edit Password</a>
                <a id="DelPho" href="deletePhotos.php" >Delete Photos</a>
                <a id="DelAcc" href="deleteAccount.php" >Delete Account</a>
                <a id="Notif" href="notifications.php" >Notifications</a>
            </nav>
            <article>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm Notif">      
                         <h2 id="subTitle">Notifications</h2>
                         <span style="color:green"><?php echo $message; ?></span>
                            <form action="" method="post">  
                            <label id="notiflabel"><input id="checkbox" type="checkbox" name="notif[]" <?php if ($data['notif'] == 1) { echo 'checked="checked"'; }?>><p id="pNot">Set up notifications on comments</p></label>
                            <div class="loginForm accountForm" style="background:none; box-shadow:none">
                                <input type="submit" id="saveBtt" style="width: 29%;font-size: 24px;" name="update" value="Update">          
                            </div>   
                        </form>
                    </div><br>
                </div>
            </article>
        
        </div>
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>