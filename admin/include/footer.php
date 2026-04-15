<!-- C:\xamppnew\htdocs\rkhospital\admin\include\footer.php -->
<?php

// Usage: set $extraJS before including this file for page-specific scripts
// Example: $extraJS = '<script src="..."></script>';
?>

        <?php if (!empty($extraJS)) echo $extraJS; ?>

        <!-- jQuery -->
        <script src="<?= SITE_URL ?>/admin/assets/js/jquery-3.7.1.min.js"></script>
        <!-- Bootstrap Core JS -->
        <script src="<?= SITE_URL ?>/admin/assets/js/bootstrap.bundle.min.js"></script>
        <!-- Slimscroll JS -->
        <script src="<?= SITE_URL ?>/admin/assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
        <!-- Custom JS -->
        <script src="<?= SITE_URL ?>/admin/assets/js/script.js"></script>

        <!-- Navbar menu search with role-filtered dropdown -->
        <script>
        (function () {
            var input   = document.getElementById('menuSearch');
            var results = document.getElementById('menuSearchResults');
            var menu    = window.ADMIN_MENU || [];
            var active  = -1; // keyboard nav index

            if (!input || !results) return;

            // ── Render dropdown ───────────────────────────────────
            function render(q) {
                q = q.trim().toLowerCase();
                if (!q) { close(); return; }

                var filtered = menu.filter(function (item) {
                    return item.label.toLowerCase().indexOf(q) !== -1
                        || item.section.toLowerCase().indexOf(q) !== -1;
                });

                if (!filtered.length) {
                    results.innerHTML =
                        '<div style="padding:16px 18px;color:#94a3b8;font-size:.84rem;text-align:center;">' +
                        '<i class="fa fa-search me-2"></i>No menu items found for "<strong>' +
                        escHtml(q) + '</strong>"</div>';
                    results.style.display = 'block';
                    active = -1;
                    return;
                }

                // Group by section
                var sections = {};
                filtered.forEach(function (item) {
                    if (!sections[item.section]) sections[item.section] = [];
                    sections[item.section].push(item);
                });

                var html = '';
                Object.keys(sections).forEach(function (sec) {
                    html += '<div style="padding:6px 14px 2px;font-size:.65rem;font-weight:700;' +
                            'color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;' +
                            'border-top:1px solid #f1f5f9;">' + escHtml(sec) + '</div>';
                    sections[sec].forEach(function (item, idx) {
                        var hi = highlight(item.label, q);
                        html += '<a href="' + escHtml(item.url) + '" class="search-result-item" ' +
                                'style="display:flex;align-items:center;gap:10px;padding:9px 16px;' +
                                'color:#1e293b;text-decoration:none;font-size:.875rem;' +
                                'border-radius:0;transition:background .15s;"' +
                                'onmouseover="this.style.background=\'#f8fafc\'"' +
                                'onmouseout="this.style.background=\'\'">' +
                                '<i class="' + escHtml(item.icon) + '" style="width:18px;text-align:center;color:#64748b;font-size:.85rem;"></i>' +
                                '<span>' + hi + '</span>' +
                                '<span style="margin-left:auto;font-size:.7rem;color:#cbd5e1;">' + escHtml(sec) + '</span>' +
                                '</a>';
                    });
                });

                results.innerHTML = html;
                results.style.display = 'block';
                active = -1;
            }

            // ── Highlight matched text ────────────────────────────
            function highlight(text, q) {
                if (!q) return escHtml(text);
                var idx = text.toLowerCase().indexOf(q);
                if (idx === -1) return escHtml(text);
                return escHtml(text.slice(0, idx)) +
                       '<mark style="background:#fef08a;color:#0f172a;border-radius:2px;padding:0 1px;">' +
                       escHtml(text.slice(idx, idx + q.length)) + '</mark>' +
                       escHtml(text.slice(idx + q.length));
            }

            function escHtml(s) {
                return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;')
                                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            function close() {
                results.style.display = 'none';
                results.innerHTML = '';
                active = -1;
            }

            // ── Keyboard navigation ───────────────────────────────
            input.addEventListener('keydown', function (e) {
                var items = results.querySelectorAll('a.search-result-item');
                if (!items.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    active = Math.min(active + 1, items.length - 1);
                    items.forEach(function (el, i) { el.style.background = i === active ? '#f0f6ff' : ''; });
                    if (items[active]) items[active].focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    active = Math.max(active - 1, 0);
                    items.forEach(function (el, i) { el.style.background = i === active ? '#f0f6ff' : ''; });
                    if (items[active]) items[active].focus();
                } else if (e.key === 'Escape') {
                    close();
                    input.blur();
                } else if (e.key === 'Enter' && active >= 0 && items[active]) {
                    window.location.href = items[active].href;
                }
            });

            // ── Input handler ─────────────────────────────────────
            input.addEventListener('input', function () { render(this.value); });
            input.addEventListener('focus', function () { if (this.value.trim()) render(this.value); });

            // ── Close on outside click ────────────────────────────
            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !results.contains(e.target)) close();
            });

        })();
        </script>

    </div>
    <!-- /Main Wrapper -->

</body>
</html>
