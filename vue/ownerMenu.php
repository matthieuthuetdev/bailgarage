<header>
<link rel="stylesheet" href="./css/ownerMenu.css">
    <aside class="sidebar">
        <img src="./img/logo.png" alt="Logo" class="logo" />
        <nav class="nav">
            <h3>MENU</h3>
            <ul>
                <li>
                    <a href="index.php?pageController=garage&action=display">
                        <i class="fa-solid fa-warehouse"></i> GARAGE
                    </a>
                </li>
                <li>
                    <a href="index.php?pageController=tenant&action=display">
                        <i class="fa-solid fa-users"></i> LOCATAIRES
                    </a>
                </li>
                <li>
                    <a href="index.php?pageController=lease&action=display">
                        <i class="fa-solid fa-file-contract"></i> BAUX
                    </a>
                </li>
                <li>
                    <a href="index.php?pageController=payment&action=display">
                        <i class="fa-solid fa-euro-sign"></i> DEMANDES DE PAIEMENT
                    </a>
                </li>
                <li>
                    <a href="index.?pageController=paymenthistory&action=display">
                        <i class="fa-solid fa-clock-rotate-left"></i> HISTORIQUE DE PAIEMENT
                    </a>
                </li>
                <li>
                    <a href="index.php?pageController=user&action=profil">
                        <i class="fa-solid fa-user"></i> PROFIL
                    </a>
                </li>
            </ul>
        </nav>
        <div class="logout-container">
            <a href="index.php?pageController=user&action=signOut" class="logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Se déconnecter
            </a>
        </div>
    </aside>
</header>
