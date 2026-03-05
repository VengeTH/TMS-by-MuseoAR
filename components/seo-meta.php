<?php
/**
 * Reusable SEO and social meta tags for public pages.
 * Expects: $seo_title (string), $seo_description (string)
 * Optional: $seo_canonical (string, URL path e.g. "/"), $seo_image (string, path e.g. "/img/logo.png"),
 *           $seo_noindex (bool), $seo_type (string, "website" or "article")
 */
$seo_title = isset($seo_title) ? trim((string) $seo_title) : "OrgaNiss - Task Management System";
$seo_description = isset($seo_description) ? trim((string) $seo_description) : "OrgaNiss helps you organize and manage tasks with an intuitive dashboard, reminders, and AI-powered planning.";
$seo_canonical = isset($seo_canonical) ? trim((string) $seo_canonical) : "";
$seo_image = isset($seo_image) ? trim((string) $seo_image) : "/img/logo.png";
$seo_noindex = isset($seo_noindex) && $seo_noindex;
$seo_type = isset($seo_type) && $seo_type === "article" ? "article" : "website";

$base_url = "";
if (isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] !== "") {
    $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
    $base_url = $protocol . "://" . $_SERVER["HTTP_HOST"];
}
$canonical_url = $base_url !== "" && $seo_canonical !== "" ? $base_url . $seo_canonical : "";
$image_url = $base_url !== "" ? $base_url . $seo_image : $seo_image;
?>
<meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
<?php if ($seo_noindex): ?>
<meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<?php if ($canonical_url !== ""): ?>
<link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
<?php endif; ?>
<meta property="og:type" content="<?php echo htmlspecialchars($seo_type); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($seo_description); ?>">
<?php if ($canonical_url !== ""): ?>
<meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
<?php endif; ?>
<meta property="og:image" content="<?php echo htmlspecialchars($image_url); ?>">
<meta property="og:site_name" content="OrgaNiss">
<meta property="og:locale" content="en_US">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo htmlspecialchars($seo_title); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($seo_description); ?>">
<?php if ($image_url !== ""): ?>
<meta name="twitter:image" content="<?php echo htmlspecialchars($image_url); ?>">
<?php endif; ?>
