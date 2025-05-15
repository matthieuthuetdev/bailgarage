<ul>
    <li>
        <a href="index.php?pageController=garage&action=display">Garage</a>
    </li>
    <li>
        <a href="index.php?pageController=tenant&action=display">Locataire</a>
    </li>
    <li>
        <a href="index.php?pageController=lease&action=display">Baux</a>
    </li>
    <li>
        <a href="index.php?pageController=payment&action=display">Demande de paiment</a>
    </li>
    <li>
        <a href="index.php?pageController=paymenthistory&action=display">Historique des paiments</a>
    </li>
    <li>
        <a href="index.php?pageController=user&action=profil">Profil</a>
    </li>
    <li>
        <?php echo $_SESSION["role"] == "owner"?"<a href='index.php?pageController=user&action=signOut'>Se déconnecter</a>" : "<a href='index.php?pageController=owner&action=stophelp'>Sortire du mode aide</a>"; ?>
    </li>
</ul>