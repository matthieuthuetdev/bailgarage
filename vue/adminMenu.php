    <link rel="stylesheet" href="./css/adminMenu.css">
    <header>
        <aside class="sidebar">
            <img src="./img/logo.png" alt="Logo" class="logo" />
            <nav class="nav">
                <h3>MENU</h3>
                <ul>
                    <li>
                        <a href="index.php?pageController=owner&action=display"><i class="fa-solid fa-user"
                                style="color: #ffffff;"></i></i>MON COMPTE</a>
                    </li>
                    <li>
                        <a href="index.php?pageController=emailtemplate&action=display"><i class="fa-solid fa-key"
                                style="color: #ffffff;"></i>PROPRIÉTAIRES</a>
                    </li>
                    <li>
                        <a href="index.php?pageController=user&action=profil"><i class="fa-solid fa-envelope"
                                style="color: #ffffff;"></i>TEMPLATES D'EMAIL</a>
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