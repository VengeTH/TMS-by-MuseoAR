<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__) . "/db/db.php";

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method.",
    ]);
    exit();
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized.",
    ]);
    exit();
}

if (
    !isset($_FILES["profile_picture"]) ||
    !is_array($_FILES["profile_picture"]) ||
    (int) $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK
) {
    echo json_encode([
        "success" => false,
        "message" => "No file uploaded or there was an upload error.",
    ]);
    exit();
}

$userId = (int) $_SESSION["user_id"];
$file = $_FILES["profile_picture"];

// * Enforce maximum file size (500 KB).
$maxBytes = 500 * 1024;
if ((int) $file["size"] > $maxBytes) {
    echo json_encode([
        "success" => false,
        "message" => "Profile picture must be 500KB or smaller.",
    ]);
    exit();
}

$imageInfo = @getimagesize($file["tmp_name"]);
if ($imageInfo === false) {
    echo json_encode([
        "success" => false,
        "message" => "Uploaded file is not a valid image.",
    ]);
    exit();
}

$mime = isset($imageInfo["mime"]) ? (string) $imageInfo["mime"] : "";
$allowedMimes = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/gif" => "gif",
];

if (!array_key_exists($mime, $allowedMimes)) {
    echo json_encode([
        "success" => false,
        "message" => "Only JPG, PNG, or GIF images are allowed.",
    ]);
    exit();
}

$extension = $allowedMimes[$mime];

$uploadDir = dirname(__DIR__) . "/uploads";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fileName = "user_" . $userId . "_" . time() . "." . $extension;
$targetFilePath = $uploadDir . "/" . $fileName;

if (!move_uploaded_file($file["tmp_name"], $targetFilePath)) {
    echo json_encode([
        "success" => false,
        "message" => "Error uploading the file.",
    ]);
    exit();
}

// * Store web-accessible relative path.
$relativePath = "/uploads/" . $fileName;

$db = new db();
$conn = $db->getConnection();

$stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
if ($stmt === false) {
    echo json_encode([
        "success" => false,
        "message" => "Database error.",
    ]);
    exit();
}

$stmt->bind_param("si", $relativePath, $userId);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    echo json_encode([
        "success" => true,
        "message" => "Profile picture uploaded successfully.",
        "profile_picture" => $relativePath,
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Unable to update profile picture.",
    ]);
}

?>
