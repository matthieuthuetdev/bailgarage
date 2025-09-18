<?php

/**
 * Classe pour gérer les locataires (tenants) : opérations CRUD sur la table tenants.
 */
class Tenants
{
    /**
     * @var PDO Connexion PDO à la base de données.
     */
    private PDO $connection;

    /**
     * Tenants constructor.
     *
     * Initialise la connexion à la base de données via la méthode singleton Database::getInstance().
     */
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    /**
     * Crée un locataire complet en base de données.
     *
     * @param int         $_ownerId              Identifiant du propriétaire
     * @param string      $_name                 Nom du locataire
     * @param string      $_firstName            Prénom du locataire
     * @param string|null $_company              Société, si applicable
     * @param string      $_address              Adresse principale
     * @param string|null $_additionalAddress    Adresse supplémentaire (facultatif)
     * @param int         $_cityId               Identifiant de la ville
     * @param string      $_cityName             Nom de la ville
     * @param string      $_postalCode           Code postal
     * @param string      $_country              Pays
     * @param string      $_phoneNumber          Numéro de portable
     * @param string|null $_landlinePhoneNumber  Numéro de téléphone fixe (facultatif)
     * @param string      $_email                Email
     * @param int         $_rgpd                 Consentement RGPD (ex : 0 ou 1)
     * @param int         $_gender               Genre (ex : 0, 1, 2 selon conventions)
     * @param int         $_receipt              Reçu (ex : s’il faut un reçu)
     * @param string|null $_ownerNote            Note privée du propriétaire (facultatif)
     *
     * @return bool True si l’insertion réussit, false sinon
     */
    public function create(
        $_ownerId,
        $_name,
        $_firstName,
        $_company,
        $_address,
        $_additionalAddress,
        $_cityId,
        $_cityName,
        $_postalCode,
        $_country,
        $_phoneNumber,
        $_landlinePhoneNumber,
        $_email,
        $_rgpd,
        $_gender,
        $_receipt,
        $_ownerNote
    ) {
        $request = "INSERT INTO tenants (ownerId, name, firstName, company, address, additionalAddress, cityId, cityName, postalCode, country, phoneNumber, landlinePhoneNumber, email, rgpd, gender, receipt, ownerNote) VALUES (:ownerId, :name, :firstName, :company, :address, :additionalAddress, :cityId, :cityName, :postalCode, :country, :phoneNumber, :landlinePhoneNumber, :email, :rgpd, :gender, :receipt, :ownerNote)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
        $rq->bindValue(":company", $_company, PDO::PARAM_STR);
        $rq->bindValue(":address", $_address, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":cityName", $_cityName, PDO::PARAM_STR);
        $rq->bindValue(":postalCode", $_postalCode, PDO::PARAM_STR);
        $rq->bindValue(":country", $_country, PDO::PARAM_STR);
        $rq->bindValue(":cityId", $_cityId, PDO::PARAM_INT);
        $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":landlinePhoneNumber", $_landlinePhoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->bindValue(":rgpd", $_rgpd, PDO::PARAM_INT);
        $rq->bindValue(":gender", $_gender, PDO::PARAM_INT);
        $rq->bindValue(":receipt", $_receipt, PDO::PARAM_INT);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        return $rq->execute();
    }

    /**
     * Crée un locataire uniquement avec l’email pour un propriétaire donné.
     *
     * @param int    $_ownerId Identifiant du propriétaire
     * @param string $_email   Email du locataire
     *
     * @return bool True si l’insertion réussit, false sinon
     */
    public function emailCreate($_ownerId, $_email)
    {
        $request = "INSERT INTO tenants (ownerId, email) VALUES (:ownerId, :email)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        return $rq->execute();
    }

    /**
     * Lit les locataires pour un propriétaire donné, ou un locataire spécifique.
     *
     * @param int      $_ownerId  Identifiant du propriétaire
     * @param int|null $_tenantId Identifiant du locataire (facultatif). Si null, retourne tous les locataires du propriétaire.
     *
     * @return array|false Tableau associatif(s) des données du/des locataire(s), ou false en cas d'erreur
     */
    public function read($_ownerId, $_tenantId = null)
    {
        if (is_null($_tenantId)) {
            $request = "SELECT * FROM tenants WHERE ownerId = :ownerId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT * FROM tenants WHERE ownerId = :ownerId AND id = :tenantId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->bindValue(":tenantId", $_tenantId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }

    /**
     * Recherche un locataire par email.
     *
     * @param string $_email Email à chercher
     * @return array|false Tableau associatif contenant au moins l'identifiant du locataire, ou false si non trouvé
     */
    public function searchTenantByEmail($_email)
    {
        $request = "SELECT id FROM tenants WHERE email = :email";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->execute();
        $result = $rq->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Met à jour les informations d’un locataire existant.
     *
     * @param int         $_tenantId            Identifiant du locataire
     * @param int         $_ownerId             Identifiant du propriétaire
     * @param string      $_name                Nom
     * @param string      $_firstName           Prénom
     * @param string|null $_company             Société
     * @param string      $_address             Adresse
     * @param string|null $_additionalAddress   Adresse additionnelle
     * @param int         $_cityId              Identifiant de la ville
     * @param string      $_cityName            Nom de la ville
     * @param string      $_postalCode          Code postal
     * @param string      $_country             Pays
     * @param string      $_phoneNumber         Numéro de téléphone mobile
     * @param string|null $_landlinePhoneNumber Numéro fixe
     * @param string      $_email               Email
     * @param int         $_rgpd                Consentement RGPD
     * @param int         $_gender              Genre
     * @param int         $_receipt             Reçu
     * @param string|null $_ownerNote           Note privée du propriétaire
     *
     * @return bool True si la mise à jour réussit, false sinon
     */
    public function update(
        $_tenantId,
        $_ownerId,
        $_name,
        $_firstName,
        $_company,
        $_address,
        $_additionalAddress,
        $_cityId,
        $_cityName,
        $_postalCode,
        $_country,
        $_phoneNumber,
        $_landlinePhoneNumber,
        $_email,
        $_rgpd,
        $_gender,
        $_receipt,
        $_ownerNote
    ) {
        $request = "UPDATE tenants SET name = :name, firstName = :firstName, company = :company, address = :address, additionalAddress = :additionalAddress, cityId = :cityId ,cityName = :cityName, postalCode = :postalCode, country = :country, phoneNumber = :phoneNumber, landlinePhoneNumber = :landlinePhoneNumber, email = :email, rgpd = :rgpd, gender = :gender, receipt = :receipt, ownerNote = :ownerNote WHERE id = :tenantId AND ownerId = :ownerId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":tenantId", $_tenantId, PDO::PARAM_INT);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
        $rq->bindValue(":company", $_company, PDO::PARAM_STR);
        $rq->bindValue(":address", $_address, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":cityId", $_cityId, PDO::PARAM_INT);
        $rq->bindValue(":cityName", $_cityName, PDO::PARAM_STR);
        $rq->bindValue(":postalCode", $_postalCode, PDO::PARAM_STR);
        $rq->bindValue(":country", $_country, PDO::PARAM_STR);
        $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":landlinePhoneNumber", $_landlinePhoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->bindValue(":rgpd", $_rgpd, PDO::PARAM_INT);
        $rq->bindValue(":gender", $_gender, PDO::PARAM_INT);
        $rq->bindValue(":receipt", $_receipt, PDO::PARAM_INT);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        return $rq->execute();
    }

    /**
     * Supprime un locataire pour un propriétaire donné.
     *
     * @param int $_tenantId Identifiant du locataire
     * @param int $_ownerId  Identifiant du propriétaire
     *
     * @return bool True si la suppression réussit, false sinon
     */
    public function delete($_tenantId, $_ownerId)
    {
        $request = "DELETE FROM tenants WHERE id = :tenantId AND ownerId = :ownerId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":tenantId", $_tenantId, PDO::PARAM_INT);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        return $rq->execute();
    }
}
