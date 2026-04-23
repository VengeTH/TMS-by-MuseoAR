<?php
require_once dirname(__DIR__) . "/helpers/sessionHandler.php";
require_once dirname(__DIR__) . "/db/db.php";

$db = new db();
$user = $db->getUserById($_SESSION["user_id"]);

if (!$user) {
    header("Location: /");
    exit();
}

if (empty($_SESSION["delete_account_csrf"])) {
    $_SESSION["delete_account_csrf"] = bin2hex(random_bytes(16));
}

$errorMessage = $_SESSION["delete_account_error"] ?? "";
unset($_SESSION["delete_account_error"]);

$hasPassword = !empty($user["password"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - OrgaNiss</title>
    <link rel="stylesheet" href="/css/deleteAcc.css">
    <link rel="icon" href="/img/logo.png" type="image/x-icon">
</head>
<body>
    <main class="delete-shell">
        <header class="delete-header">
            <a href="/dashboard?tab=profile" class="delete-back">Back to Profile</a>
            <div class="delete-brand">
                <img src="/img/logo.png" alt="OrgaNiss logo">
                <span>The Heedful System</span>
            </div>
        </header>

        <section class="delete-hero">
            <p class="delete-eyebrow">Account Security</p>
            <h1>Delete Account</h1>
            <p>This action permanently removes your account and task history. This cannot be undone.</p>
        </section>

        <?php if ($errorMessage !== ""): ?>
            <div class="delete-alert"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <section class="delete-panel">
            <h2>Before You Continue</h2>
            <ul>
                <li>All tasks and planning data will be permanently deleted.</li>
                <li>Your profile details and account access will be removed immediately.</li>
                <li>This operation cannot be reversed by support.</li>
            </ul>

            <form class="delete-form" method="POST" action="/user/delete.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION["delete_account_csrf"]); ?>">

                <label for="confirmText">Type DELETE to confirm</label>
                <input id="confirmText" name="confirm_text" type="text" autocomplete="off" required>

                <?php if ($hasPassword): ?>
                    <label for="password">Current password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                <?php else: ?>
                    <p class="delete-helper">No password is required because this account uses a social sign-in method.</p>
                <?php endif; ?>

                <button type="submit" class="delete-button">Permanently Delete Account</button>
            </form>
        </section>
    </main>
</body>
</html>

