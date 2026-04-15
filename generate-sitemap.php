<?php
require_once 'include/config.php';

// Define the base URL (make sure it doesn't have a trailing slash)
$base_url = defined('SITE_URL') ? rtrim(SITE_URL, '/') : "http://localhost/rkhospital";
$current_date = date('Y-m-d');

// Initialize the XML string
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// ─── 1. STATIC PAGES ──────────────────────────────────────────
// List all your static pages here WITHOUT the .php extension
$static_pages = [
    '',                     // Homepage (Root)
    '/about-us',
    '/contact-us',
    '/doctors',
    '/orthopedic-services',
    '/gynecology-services',
    '/hospital-services',
    '/blogs',
    '/privacy-policy',
    '/terms-conditions',
    '/cancellation-policy'
    
];

foreach ($static_pages as $page) {
    $priority = ($page === '') ? '1.0' : '0.8';
    
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . htmlspecialchars($base_url . $page) . "</loc>\n";
    $xml .= "    <lastmod>" . $current_date . "</lastmod>\n";
    $xml .= "    <changefreq>weekly</changefreq>\n";
    $xml .= "    <priority>" . $priority . "</priority>\n";
    $xml .= "  </url>\n";
}

// ─── 2. DYNAMIC PAGES: DOCTORS ───────────────────────────────
$doc_query = "SELECT slug FROM doctors WHERE slug IS NOT NULL AND slug != ''";
$doc_res = $conn->query($doc_query);

if ($doc_res && $doc_res->num_rows > 0) {
    while ($row = $doc_res->fetch_assoc()) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($base_url . '/doctors/' . $row['slug']) . "</loc>\n";
        $xml .= "    <lastmod>" . $current_date . "</lastmod>\n";
        $xml .= "    <changefreq>monthly</changefreq>\n";
        $xml .= "    <priority>0.9</priority>\n";
        $xml .= "  </url>\n";
    }
}

// ─── 3. DYNAMIC PAGES: BLOGS ─────────────────────────────────
// Check if the blogs table exists and fetch active blogs
$blog_query = "SELECT slug, published_at FROM blogs WHERE is_published = 1";
$blog_res = $conn->query($blog_query);

if ($blog_res && $blog_res->num_rows > 0) {
    while ($row = $blog_res->fetch_assoc()) {
        // Use the blog's actual publish date for the lastmod if available
        $blog_date = !empty($row['published_at']) ? date('Y-m-d', strtotime($row['published_at'])) : $current_date;
        
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($base_url . '/blog/' . $row['slug']) . "</loc>\n";
        $xml .= "    <lastmod>" . $blog_date . "</lastmod>\n";
        $xml .= "    <changefreq>monthly</changefreq>\n";
        $xml .= "    <priority>0.7</priority>\n";
        $xml .= "  </url>\n";
    }
}

// Close the XML structure
$xml .= '</urlset>';

// ─── GENERATE THE FILE ────────────────────────────────────────
$file_path = __DIR__ . '/sitemap.xml';

// Write the XML string to sitemap.xml
if (file_put_contents($file_path, $xml)) {
    echo "<div style='font-family: sans-serif; padding: 30px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 8px; max-width: 600px; margin: 50px auto; text-align: center;'>";
    echo "<h2 style='margin-top:0;'>✅ Success!</h2>";
    echo "<p>Your <strong>sitemap.xml</strong> file has been generated successfully.</p>";
    echo "<p>Total URLs added: " . (count($static_pages) + ($doc_res ? $doc_res->num_rows : 0) + ($blog_res ? $blog_res->num_rows : 0)) . "</p>";
    echo "<a href='sitemap.xml' target='_blank' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background: #16a34a; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold;'>View Sitemap</a>";
    echo "</div>";
} else {
    echo "<div style='font-family: sans-serif; padding: 30px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; max-width: 600px; margin: 50px auto; text-align: center;'>";
    echo "<h2 style='margin-top:0;'>❌ Error</h2>";
    echo "<p>Could not write to <code>sitemap.xml</code>. Please check your folder permissions to ensure PHP has write access to the root directory.</p>";
    echo "</div>";
}

$conn->close();
?>