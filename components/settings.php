<?php require_once dirname(__DIR__) . "/db/db.php";?>
<div class="container">
    <div class="upperContainer">
        <div class="cover">
            <img src="/img/defaultCover.png" class="cover" width=100%>
        </div>
        <div class="profile">
            <div class="profilePic">
                <?php $profilePicture = !empty($user["profile_picture"])
                    ? htmlspecialchars($user["profile_picture"])
                    : "/img/defaultPFP.png"; ?>
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture" style="background-color:#627885;">
            </div>
            <div class="settingsTitle">
                <h2>Settings</h2>
                <p><?php
                $userDetails = $db -> getUser($_SESSION["user_id"]);
                echo htmlspecialchars($userDetails["email"]);
                ?></p>
            </div>
        </div>
        <div class="menu">
            <ul>
                <a href="#">
                <li>Edit Profile</li>
                </a>
                <a href="#">
                <li>Change Password</li>
                </a>
                <a id="del" href="#">
                <li>Delete Account</li>
                </a>
            </ul>
        </div>
        <div class="changeProf">
            <h3>Profile Photo</h3>
        </div>
    </div>
</div>

<style>
<?php include "../css/settings.css"; ?>
</style>