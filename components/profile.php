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
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture" style="background-color:#2b2b2b;">
            </div>
            <div class="settingsTitle">
                <h2>Profile &amp; Account</h2>
                <p><?php
                $userDetails = $db -> getUser($_SESSION["user_id"]);
                echo htmlspecialchars($userDetails["email"]);
                ?></p>
            </div>
        </div>
        <div class="menu">
            <ul>
                <a href="/dashboard?tab=profile#profile">
                <li>Edit Profile</li>
                </a>
                <a href="/verify/password">
                <li>Change Password</li>
                </a>
                <a id="del" href="/pages/delete-account">
                <li>Delete Account</li>
                </a>
            </ul>
        </div>
        <div class="changeProf" id="profile">
            <h3>Profile Photo</h3>
            <p class="changeProfText">Upload JPG, PNG, or GIF up to 500KB.</p>
            <form id="profileUploadForm" class="profileUploadForm" enctype="multipart/form-data">
                <input type="file" id="profilePictureInput" name="profile_picture" accept="image/jpeg,image/png,image/gif" required>
                <button type="submit" class="uploadButton">Upload Photo</button>
            </form>
            <p id="profileUploadStatus" class="profileUploadStatus" aria-live="polite"></p>
        </div>
    </div>
</div>

<style>
<?php include "../css/profile.css"; ?>
</style>

<script>
(() => {
    const form = document.getElementById('profileUploadForm');
    const input = document.getElementById('profilePictureInput');
    const statusEl = document.getElementById('profileUploadStatus');
    const profileImages = document.querySelectorAll('.profile-picture');

    if (!form || !input || !statusEl) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!input.files || input.files.length === 0) {
            statusEl.textContent = 'Select an image first.';
            statusEl.classList.add('is-error');
            return;
        }

        const formData = new FormData(form);
        statusEl.textContent = 'Uploading...';
        statusEl.classList.remove('is-error');

        try {
            const response = await fetch('/verify/upload_profilePicture.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!data.success) {
                statusEl.textContent = data.message || 'Upload failed.';
                statusEl.classList.add('is-error');
                return;
            }

            const nextSrc = data.profile_picture + '?v=' + Date.now();
            profileImages.forEach((img) => {
                img.src = nextSrc;
            });

            statusEl.textContent = 'Profile photo updated successfully.';
            statusEl.classList.remove('is-error');
            form.reset();
        } catch (error) {
            statusEl.textContent = 'Could not upload right now. Please try again.';
            statusEl.classList.add('is-error');
        }
    });
})();
</script>