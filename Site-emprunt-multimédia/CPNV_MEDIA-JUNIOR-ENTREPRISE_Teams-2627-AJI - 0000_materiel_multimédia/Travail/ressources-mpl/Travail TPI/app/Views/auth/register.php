<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3 text-primary">Créer un compte</h1>
                <form action="<?= route('/register'); ?>" method="POST" class="row g-3 needs-validation" novalidate>
                    <div class="col-12">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">Adresse email @eduvaud</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmation</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">S’inscrire</button>
                        <p class="mt-3 mb-0 text-center">
                            <small>Déjà inscrit ? <a href="<?= route('/login'); ?>">Se connecter</a></small>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

