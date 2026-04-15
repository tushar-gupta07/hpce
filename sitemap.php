<?php
// Set the header so browsers and search engines treat this as an XML file
header("Content-Type: application/xml; charset=utf-8");

require_once 'include/config.php';

// Define the base URL
$base_url = defined('SITE_URL') ? rtrim(SITE_URL, '/') : "http://localhost/rkhospital";
$current_date = date('Y-m-d');

// Output the standard XML sitemap headers
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// ─── 1. STATIC PAGES ──────────────────────────────────────────
$static_pages = [
    '',                     // Homepage (Root)
    '/about-us',
    '/contact-us',
    '/doctors',
    '/orthopedic-services',
    '/gynecology-services',
    '/hospital-services',
    '/blog-grid'
];

foreach ($static_pages as $page) {
    $priority = ($page === '') ? '1.0' : '0.8';
    
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($base_url . $page) . "</loc>\n";
    echo "    <lastmod>" . $current_date . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>" . $priority . "</priority>\n";
    echo "  </url>\n";
}

// ─── 2. DYNAMIC PAGES: DOCTORS ───────────────────────────────
$doc_query = "SELECT slug FROM doctors WHERE slug IS NOT NULL AND slug != ''";
$doc_res = $conn->query($doc_query);

if ($doc_res && $doc_res->num_rows > 0) {
    while ($row = $doc_res->fetch_assoc()) {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($base_url . '/doctors/' . $row['slug']) . "</loc>\n";
        echo "    <lastmod>" . $current_date . "</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.9</priority>\n";
        echo "  </url>\n";
    }
}

// ─── 3. DYNAMIC PAGES: BLOGS ─────────────────────────────────
// Fetch published blogs
$blog_query = "SELECT slug, published_at FROM blogs WHERE is_published = 1";
$blog_res = $conn->query($blog_query);

if ($blog_res && $blog_res->num_rows > 0) {
    while ($row = $blog_res->fetch_assoc()) {
        $blog_date = !empty($row['published_at']) ? date('Y-m-d', strtotime($row['published_at'])) : $current_date;
        
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($base_url . '/blog/' . $row['slug']) . "</loc>\n";
        echo "    <lastmod>" . $blog_date . "</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
}

// Close the XML structure
echo '</urlset>';

// Close database connection
$conn->close();
?>