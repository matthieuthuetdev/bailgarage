<form action="../includes/connexion_process.php" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <input type="hidden" name="user_role" value="admin">
            <input type="submit" name="login-submit" value="Se connecter">
        </form>
0