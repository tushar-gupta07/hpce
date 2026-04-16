<?php
$activePage = isset($activePage) ? $activePage : '';
$adminBase  = defined('SITE_URL') ? SITE_URL . '/admin/' : '/hpce/admin/';

/**
 * Exact match — used on submenu <li> items.
 * $also: additional page keys that should also make this item active
 *        (e.g. edit page → highlight the "All X" list item)
 */
function sbActive(string $key, string $active, array $also = []): string {
    return ($active === $key || in_array($active, $also, true))
        ? ' class="active"' : '';
}

/**
 * Prefix match — used on parent <li> to keep it open when any child is active.
 */
function sbParent(string $prefix, string $active): string {
    return ' class="submenu"';
}

function sbOpen(string $prefix, string $active): string {
    return str_starts_with($active, $prefix) ? 'block' : 'none';
}
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>

                <li class="menu-title"><span>Main</span></li>

                <li<?= sbActive('dashboard', $activePage) ?>>
                    <a href="<?= $adminBase ?>">
                        <i class="fe fe-home"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title"><span>Hospital</span></li>

                <?php if (canAccess('doctors')): ?>
                <li<?= sbParent('doctors', $activePage) ?>>
                    <a href="#">
                        <i class="fe fe-user-plus"></i> <span>Doctors</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display:<?= sbOpen('doctors', $activePage) ?>;">
                        <li<?= sbActive('doctors-index', $activePage, ['doctors-edit']) ?>>
                            <a href="<?= $adminBase ?>doctors/">All Doctors</a>
                        </li>
                        <?php if (!hasRole('viewer')): ?>
                        <li<?= sbActive('doctors-add', $activePage) ?>>
                            <a href="<?= $adminBase ?>doctors/add">Add Doctor</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (canAccess('users')): ?>
                <li<?= sbParent('users', $activePage) ?>>
                    <a href="#">
                        <i class="fe fe-users"></i> <span>Users</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display:<?= sbOpen('users', $activePage) ?>;">
                        <li<?= sbActive('users-index', $activePage, ['users-edit']) ?>>
                            <a href="<?= $adminBase ?>users/">All Users</a>
                        </li>
                        <?php if (hasRole(['superadmin', 'admin'])): ?>
                        <li<?= sbActive('users-add', $activePage) ?>>
                            <a href="<?= $adminBase ?>users/add">Add User</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="menu-title"><span>Content</span></li>

                <?php if (canAccess('blogs')): ?>
                <li<?= sbParent('blogs', $activePage) ?>>
                    <a href="#">
                        <i class="fe fe-edit"></i> <span>Blogs</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display:<?= sbOpen('blogs', $activePage) ?>;">
                        <li<?= sbActive('blogs-index', $activePage, ['blogs-edit']) ?>>
                            <a href="<?= $adminBase ?>blog/">All Blogs</a>
                        </li>
                        <?php if (!hasRole('viewer')): ?>
                        <li<?= sbActive('blogs-add', $activePage) ?>>
                            <a href="<?= $adminBase ?>blog/add">Add Blog</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (canAccess('services')): ?>
                <li<?= sbParent('services', $activePage) ?>>
                    <a href="#">
                        <i class="fe fe-layout"></i> <span>Services</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display:<?= sbOpen('services', $activePage) ?>;">
                        <li<?= sbActive('services-index', $activePage, ['services-edit']) ?>>
                            <a href="<?= $adminBase ?>services/">All Services</a>
                        </li>
                        <?php if (!hasRole('viewer')): ?>
                        <li<?= sbActive('services-add', $activePage) ?>>
                            <a href="<?= $adminBase ?>services/add">Add Service</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
