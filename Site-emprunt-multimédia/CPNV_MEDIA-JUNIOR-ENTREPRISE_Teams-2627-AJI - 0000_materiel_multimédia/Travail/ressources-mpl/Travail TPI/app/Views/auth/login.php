<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3 text-primary">Connexion</h1>
                <form action="<?= route('/login'); ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    <p class="mt-3 mb-0 text-center">
                        <small>Pas encore de compte ? <a href="<?= route('/register'); ?>">S’inscrire</a></small>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

