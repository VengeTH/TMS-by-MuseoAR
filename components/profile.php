<?php
require_once dirname(__DIR__) . "/db/db.php";

if (!isset($db) || !($db instanceof db)) {
    $db = new db();
}

$userDetails = $db->getUser($_SESSION["user_id"]);
if (!is_array($userDetails)) {
    $userDetails = [];
}
$profilePicture = !empty($userDetails["profile_picture"])
    ? htmlspecialchars($userDetails["profile_picture"])
    : "/img/defaultPFP.png";
$email = htmlspecialchars($userDetails["email"] ?? "demo@theheedful.me");
$firstName = trim((string) ($userDetails["first_name"] ?? ""));
$lastName = trim((string) ($userDetails["last_name"] ?? ""));
$displayName = trim($firstName . " " . $lastName);
if ($displayName === "") {
    $displayName = "Profile Owner";
}
?>
<div class="container">
    <div class="upperContainer">
        <div class="cover">
            <img src="/img/defaultCover.png" class="cover" width="100%" alt="Profile cover">
        </div>
        <div class="profile">
            <div class="profilePic">
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture" style="background-color:#2b2b2b;">
            </div>
            <div class="settingsTitle">
                <h2>Profile &amp; Account</h2>
                <p><?php echo $displayName; ?> · <?php echo $email; ?></p>
            </div>
            <div class="profile-summary-grid">
                <div class="profile-summary-card">
                    <span class="profile-summary-label">Tasks tracked</span>
                    <strong>24</strong>
                    <small>12 active this week</small>
                </div>
                <div class="profile-summary-card">
                    <span class="profile-summary-label">AI plans</span>
                    <strong>8</strong>
                    <small>3 generated this month</small>
                </div>
                <div class="profile-summary-card">
                    <span class="profile-summary-label">Focus score</span>
                    <strong>87%</strong>
                    <small>Based on current demo values</small>
                </div>
                <div class="profile-summary-card">
                    <span class="profile-summary-label">Workspace</span>
                    <strong>Heedful</strong>
                    <small>Dark-first system profile</small>
                </div>
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
                <a href="/dashboard?tab=weeklyPlanner">
                <li>Open Weekly Planner</li>
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

            <div class="profile-details-grid">
                <div class="profile-details-card">
                    <h4>Account Details</h4>
                    <dl>
                        <div>
                            <dt>Email</dt>
                            <dd><?php echo $email; ?></dd>
                        </div>
                        <div>
                            <dt>Display name</dt>
                            <dd><?php echo htmlspecialchars($displayName); ?></dd>
                        </div>
                        <div>
                            <dt>Role</dt>
                            <dd>Primary workspace member</dd>
                        </div>
                        <div>
                            <dt>Timezone</dt>
                            <dd>Asia/Manila</dd>
                        </div>
                    </dl>
                </div>

                <div class="profile-details-card">
                    <h4>Workspace Snapshot</h4>
                    <ul class="profile-points">
                        <li>Profile photo and avatar previews sync across the dashboard header.</li>
                        <li>Demo values keep the system visually complete before your data is filled in.</li>
                        <li>AI planning, reminders, and task deadlines all share the same dark-first theme.</li>
                        <li>Account tools stay close to the profile surface for quick updates.</li>
                    </ul>
                </div>
            </div>
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