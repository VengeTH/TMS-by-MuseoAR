<?php
/**
 * Dynamic XML sitemap for public crawlable pages.
 * Served as application/xml; add rewrite rule so /sitemap.xml serves this if desired.
 */
$protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$host = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost";
$base = $protocol . "://" . $host;

$pages = [
    ["loc" => "/", "priority" => "1.0", "changefreq" => "weekly"],
  ["loc" => "/user/register.php", "priority" => "0.9", "changefreq" => "monthly"],
  ["loc" => "/pages/about.php", "priority" => "0.7", "changefreq" => "monthly"],
  ["loc" => "/pages/contact.php", "priority" => "0.8", "changefreq" => "monthly"],
  ["loc" => "/termsAndConditions.html", "priority" => "0.3", "changefreq" => "yearly"],
];

$lastmod = date("Y-m-d");

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
  <url>
    <loc><?php echo htmlspecialchars($base . $p["loc"]); ?></loc>
    <lastmod><?php echo $lastmod; ?></lastmod>
    <changefreq><?php echo htmlspecialchars($p["changefreq"]); ?></changefreq>
    <priority><?php echo htmlspecialchars($p["priority"]); ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
