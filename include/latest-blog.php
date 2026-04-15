<?php
// ── Fetch Latest Blogs ──────────────────────────────
$latestBlogs = [];
$lbRes = $conn->query("
    SELECT 
        b.id, b.title, b.slug, b.excerpt, b.image,
        b.published_at, b.views, b.reading_time,
        bc.name AS category_name,
        ba.name AS author_name
    FROM blogs b
    LEFT JOIN categories bc ON b.category_id = bc.id
    LEFT JOIN doctors ba ON b.id = ba.id
    WHERE b.is_published = 1
    ORDER BY b.published_at DESC
    LIMIT 3
");
if ($lbRes) {
    while ($lb = $lbRes->fetch_assoc()) {
        $latestBlogs[] = $lb;
    }
}
?>

<?php if (!empty($latestBlogs)): ?>
<section class="article-section section">
    <div class="container">

        <div class="section-header section-header-one text-center wow fadeInUp" data-wow-duration="1s">
            <div class="title">Recent Blogs</div>
            <h2 class="section-title">Stay Updated With Our <span class="text-danger">Latest Blogs</span></h2>
        </div>
        <div class="row g-4">

            <?php foreach ($latestBlogs as $index => $lb): ?>
            <?php
                $duration = ($index === 0) ? '1s' : ($index === 1 ? '1.5s' : '2s');
                $day      = !empty($lb['published_at']) ? date('d', strtotime($lb['published_at'])) : '';
                $month    = !empty($lb['published_at']) ? date('M', strtotime($lb['published_at'])) : '';
            ?>

            <div class="col-lg-4 col-md-6">
                <div class="blog-card wow fadeInUp" data-wow-duration="<?= $duration ?>" style="
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                    background: #fff;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                "
                onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 12px 30px rgba(0,0,0,0.15)'"
                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'"
                >

                    <!-- Card Image (Top Full Width) -->
                    <div class="blog-card-img" style="position: relative; overflow: hidden;">
                        <a href="blog-details.php?slug=<?= urlencode($lb['slug']) ?>">
                            <?php if (!empty($lb['image'])): ?>
                                <img src="<?= htmlspecialchars($lb['image']) ?>"
                                     class="img-fluid"
                                     alt="<?= htmlspecialchars($lb['title']) ?>"
                                     onerror="this.src='assets/img/blog/default-blog.jpg'"
                                     style="width: 100%; height: 220px; object-fit: cover; display: block;">
                            <?php else: ?>
                                <img src="assets/img/blog/default-blog.jpg"
                                     class="img-fluid"
                                     alt="<?= htmlspecialchars($lb['title']) ?>"
                                     style="width: 100%; height: 220px; object-fit: cover; display: block;">
                            <?php endif; ?>
                        </a>

                        <!-- Date Badge (Top Left on Image) -->
                        <?php if ($day && $month): ?>
                        <div style="
                            position: absolute;
                            top: 12px;
                            left: 12px;
                            background: #fff;
                            border-radius: 8px;
                            padding: 6px 10px;
                            text-align: center;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                            line-height: 1.2;
                        ">
                            <span style="font-size: 18px; font-weight: 700; color: #222; display: block;"><?= $day ?></span>
                            <span style="font-size: 12px; color: #666;"><?= $month ?></span>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Card Content (Bottom) -->
                    <div class="blog-card-body" style="padding: 20px; display: flex; flex-direction: column; flex: 1;">

                        <!-- Category Badge -->
                        <?php if (!empty($lb['category_name'])): ?>
                            <span class="badge badge-cyan mb-2" style="width: fit-content;">
                                <?= htmlspecialchars($lb['category_name']) ?>
                            </span>
                        <?php endif; ?>

                        <!-- Title -->
                        <h3 class="custom-title mb-2" style="font-size: 17px; font-weight: 600; line-height: 1.4;">
                            <a href="blog-details.php?slug=<?= urlencode($lb['slug']) ?>"
                               style="color: #222; text-decoration: none;">
                                <?= htmlspecialchars(mb_strimwidth($lb['title'], 0, 60, '...')) ?>
                            </a>
                        </h3>

                        <!-- Excerpt -->
                        <?php if (!empty($lb['excerpt'])): ?>
                            <p style="font-size: 14px; color: #666; line-height: 1.6; flex: 1;">
                                <?= htmlspecialchars(mb_strimwidth($lb['excerpt'], 0, 100, '....')) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Read More -->
                        <a href="blog-details.php?slug=<?= urlencode($lb['slug']) ?>" class="link mt-2"
                           style="font-weight: 600; font-size: 14px;">
                            Read More <i class="isax isax-arrow-right-3 ms-1"></i>
                        </a>

                    </div>
                </div>
            </div>

            <?php endforeach; ?>

        </div>

        <div class="text-center load-item wow fadeInUp mt-5" data-wow-duration="1s">
            <a href="blogs.php" class="btn btn-dark d-inline-flex align-items-center">
                View All Blogs
                <i class="isax isax-arrow-right-3 ms-2"></i>
            </a>
        </div>

    </div>
</section>
<?php endif; ?>