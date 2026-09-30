<?php
use App\Core\Session;

$session = new Session();
$user = $session->get('user');
$roleLabels = config('auth.role_labels', []);
$flashSuccess = $session->flash('success');
$flashError = $session->flash('error');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(config('name', 'Application')); ?></title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css'); ?>">
</head>

<body>
    <header class="topbar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a class="brand" href="<?= route('/'); ?>">CPNV Gestion du matériel</a>
            <?php if ($user): ?>
                <form action="<?= route('/logout'); ?>" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-sm btn-light">Déconnexion</button>
                </form>
            <?php endif; ?>
        </div>
    </header>
    <div class="container-fluid">
        <div class="row">
            <?php if ($user): ?>
                <aside class="col-12 col-md-3 col-lg-2 sidebar">
                    <div class="sidebar-user">
                        <div class="avatar">
                            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div>
                            <p class="mb-0"><?= htmlspecialchars($user['name']); ?></p>
                            <small>
                                <?= htmlspecialchars($user['email']); ?><br>
                                <?php foreach ($user['roles'] as $role): ?>
                                    <span class="badge bg-secondary"><?= $roleLabels[$role] ?? ucfirst($role); ?></span>
                                <?php endforeach; ?>
                            </small>
                        </div>
                    </div>
                    <?php
                    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                    $isActive = function ($path) use ($currentPath) {
                        $routePath = route($path);
                        // Exact match for home, prefix match for others
                        if ($path === '/') {
                            return $currentPath === $routePath ? 'active' : '';
                        }
                        return strpos($currentPath, $routePath) === 0 ? 'active' : '';
                    };
                    ?>
                    <nav class="nav flex-column">
                        <a href="<?= route('/'); ?>" class="nav-link <?= $isActive('/'); ?>">Tableau de bord</a>
                        <a href="<?= route('/materials'); ?>" class="nav-link <?= $isActive('/materials'); ?>">Catalogue du
                            matériel</a>
                        <a href="<?= route('/cart'); ?>" class="nav-link <?= $isActive('/cart'); ?>">Mon panier</a>
                        <?php if (array_intersect($user['roles'], ['admin', 'responsable'])): ?>
                            <a href="<?= route('/admin/tools'); ?>" class="nav-link <?= $isActive('/admin/tools'); ?>">Gestion
                                du matériel</a>
                            <a href="<?= route('/admin/reservations'); ?>"
                                class="nav-link <?= $isActive('/admin/reservations'); ?>">Gestion des réservations</a>
                            <a href="<?= route('/admin/users'); ?>" class="nav-link <?= $isActive('/admin/users'); ?>">Gestion
                                des utilisateurs</a>
                            <a href="<?= route('/admin/incidents'); ?>"
                                class="nav-link <?= $isActive('/admin/incidents'); ?>">Incidents</a>
                            <?php if (in_array('admin', $user['roles'], true)): ?>
                                <a href="<?= route('/admin/trash'); ?>"
                                    class="nav-link <?= $isActive('/admin/trash'); ?>">Corbeille</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </nav>
                </aside>
                <main class="col-12 col-md-9 col-lg-10 main-content">
                <?php else: ?>
                    <main class="col-12">
                    <?php endif; ?>
                    <div class="container py-4">
                        <?php if ($flashSuccess): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($flashSuccess); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($flashError): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($flashError); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?= $content ?? ''; ?>
                    </div>
                </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js'); ?>"></script>
</body>

</html>