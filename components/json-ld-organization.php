<?php
/**
 * JSON-LD structured data for Organization and WebApplication (schema.org).
 * Helps search engines understand the product and can enable rich results.
 */
$base_url = "";
if (isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] !== "") {
    $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
    $base_url = $protocol . "://" . $_SERVER["HTTP_HOST"];
}
if ($base_url === "") {
    return;
}
$logo_url = $base_url . "/img/logo.png";
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "OrgaNiss",
      "url": "<?php echo htmlspecialchars($base_url); ?>",
      "logo": "<?php echo htmlspecialchars($logo_url); ?>",
      "description": "OrgaNiss is a task management system that helps you organize tasks, set reminders, and plan with AI-powered features."
    },
    {
      "@type": "WebApplication",
      "name": "OrgaNiss",
      "url": "<?php echo htmlspecialchars($base_url); ?>",
      "applicationCategory": "ProductivityApplication",
      "description": "Organize and manage your tasks with an intuitive dashboard, reminders, and AI weekly planning."
    }
  ]
}
</script>
