<?php

/**
 * Classe pour gérer les garages : opérations CRUD sur la table garages.
 */
class Garages
{
    /**
     * @var PDO Connexion PDO à la base de données.
     */
    private PDO $connection;

    /**
     * Garages constructor.
     * Initialise la connexion à la base de données via la méthode singleton Database::getInstance()
     */
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    /**
     * Crée un garage en base de données.
     *
     * @param int         $_ownerId           Identifiant du propriétaire
     * @param string      $_address           Adresse principale du garage
     * @param string|null $_additionalAddress  Adresse additionnelle (facultative)
     * @param int         $_cityId            Identifiant de la ville
     * @param string      $_cityName          Nom de la ville
     * @param string      $_postalCode        Code postal
     * @param string      $_country           Pays
     * @param int         $_garageNumber      Numéro du garage
     * @param int|null    $_lotNumber         Numéro du lot (facultatif)
     * @param string      $_rentWithoutCharges Loyer hors charges (format chaîne, ex: "100.00")
     * @param string      $_charges           Montant des charges (format chaîne)
     * @param int         $_surface           Surface du garage (unité au choix, m² par exemple)
     * @param string      $_reference         Référence interne ou externe
     * @param string      $_trustee           Nom du mandataire / gestionnaire
     * @param string      $_caution           Montant de la caution (format chaîne)
     * @param string|null $_comment           Commentaire public
     * @param string|null $_ownerNote         Note privée du propriétaire
     * @param int|null    $_additionalIbanId  Identifiant IBAN additionnel (facultatif)
     *
     * @return int|false Retourne l'identifiant du garage inséré, ou false en cas d'erreur
     */
    public function create(
        $_ownerId,
        $_address,
        $_additionalAddress,
        $_cityId,
        $_cityName,
        $_postalCode,
        $_country,
        $_garageNumber,
        $_lotNumber,
        $_rentWithoutCharges,
        $_charges,
        $_surface,
        $_reference,
        $_trustee,
        $_caution,
        $_comment,
        $_ownerNote,
        $_additionalIbanId
    ) {
        $request = "INSERT INTO garages (ownerId, address, additionalAddress, cityId, cityName, postalCode, country, garageNumber, lotNumber, rentWithoutCharges, charges, surface, reference, trustee, caution, comment, ownerNote, additionalIbanId) VALUES (:ownerId, :address, :additionalAddress, :cityId, :cityName, :postalCode, :country, :garageNumber, :lotNumber, :rentWithoutCharges, :charges, :surface, :reference, :trustee, :caution, :comment, :ownerNote, :additionalIbanId)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":address", $_address, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":cityId", $_cityId, PDO::PARAM_INT);
        $rq->bindValue(":cityName", $_cityName, PDO::PARAM_STR);
        $rq->bindValue(":postalCode", $_postalCode, PDO::PARAM_STR);
        $rq->bindValue(":country", $_country, PDO::PARAM_STR);
        $rq->bindValue(":garageNumber", $_garageNumber, PDO::PARAM_INT);
        $rq->bindValue(":lotNumber", $_lotNumber, PDO::PARAM_INT);
        $rq->bindValue(":rentWithoutCharges", $_rentWithoutCharges, PDO::PARAM_STR);
        $rq->bindValue(":charges", $_charges, PDO::PARAM_STR);
        $rq->bindValue(":surface", $_surface, PDO::PARAM_INT);
        $rq->bindValue(":reference", $_reference, PDO::PARAM_STR);
        $rq->bindValue(":trustee", $_trustee, PDO::PARAM_STR);
        $rq->bindValue(":caution", $_caution, PDO::PARAM_STR);
        $rq->bindValue(":comment", $_comment, PDO::PARAM_STR);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        $rq->bindValue(":additionalIbanId", $_additionalIbanId, PDO::PARAM_INT);

        if ($rq->execute()) {
            return $this->connection->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * Lit les garages pour un propriétaire donné, ou un garage spécifique.
     *
     * @param int      $_ownerId  Identifiant du propriétaire
     * @param int|null $_garageId Identifiant du garage (facultatif). Si null, retourne tous les garages du propriétaire.
     *
     * @return array|false Tableau associatif(s) des données du / des garage(s), ou false si erreur
     */
    public function read($_ownerId, $_garageId = null)
    {
        if (is_null($_garageId)) {
            $request = "SELECT * FROM garages WHERE ownerId = :ownerId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT * FROM garages WHERE ownerId = :ownerId AND id = :garageId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->bindValue(":garageId", $_garageId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }

    /**
     * Met à jour un garage existant.
     *
     * @param int         $_garageId          Identifiant du garage à mettre à jour
     * @param int         $_ownerId           Identifiant du propriétaire
     * @param string      $_address           Nouvelle adresse
     * @param string|null $_additionalAddress  Adresse additionnelle
     * @param int         $_cityId            Identifiant de la ville
     * @param string      $_cityName          Nom de la ville
     * @param string      $_postalCode        Code postal
     * @param string      $_country           Pays
     * @param int         $_garageNumber      Numéro du garage
     * @param int|null    $_lotNumber         Numéro du lot
     * @param string      $_rentWithoutCharges Loyer hors charges
     * @param string      $_charges           Charges
     * @param int         $_surface           Surface
     * @param string      $_reference         Référence
     * @param string      $_trustee           Mandataire
     * @param string      $_caution           Caution
     * @param int|null    $_additionalIbanId  IBAN additionnel
     * @param string|null $_comment           Commentaire public
     * @param string|null $_ownerNote         Note du propriétaire
     *
     * @return bool True si la mise à jour réussit, false sinon
     */
    public function update(
        $_garageId,
        $_ownerId,
        $_address,
        $_additionalAddress,
        $_cityId,
        $_cityName,
        $_postalCode,
        $_country,
        $_garageNumber,
        $_lotNumber,
        $_rentWithoutCharges,
        $_charges,
        $_surface,
        $_reference,
        $_trustee,
        $_caution,
        $_additionalIbanId,
        $_comment,
        $_ownerNote
    ) {
        $request = "UPDATE garages SET address = :address, additionalAddress = :additionalAddress, cityId = :cityId, cityName = :cityName, postalCode = :postalCode, country = :country, garageNumber = :garageNumber, lotNumber = :lotNumber, rentWithoutCharges = :rentWithoutCharges, charges = :charges, surface = :surface, reference = :reference, trustee = :trustee, caution = :caution, comment = :comment, ownerNote = :ownerNote, additionalIbanId = :additionalIbanId WHERE id = :garageId AND ownerId = :ownerId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":garageId", $_garageId, PDO::PARAM_INT);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":address", $_address, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":cityId", $_cityId, PDO::PARAM_INT);
        $rq->bindValue(":cityName", $_cityName, PDO::PARAM_STR);
        $rq->bindValue(":postalCode", $_postalCode, PDO::PARAM_STR);
        $rq->bindValue(":country", $_country, PDO::PARAM_STR);
        $rq->bindValue(":garageNumber", $_garageNumber, PDO::PARAM_INT);
        $rq->bindValue(":lotNumber", $_lotNumber, PDO::PARAM_INT);
        $rq->bindValue(":rentWithoutCharges", $_rentWithoutCharges, PDO::PARAM_STR);
        $rq->bindValue(":charges", $_charges, PDO::PARAM_STR);
        $rq->bindValue(":surface", $_surface, PDO::PARAM_INT);
        $rq->bindValue(":reference", $_reference, PDO::PARAM_STR);
        $rq->bindValue(":trustee", $_trustee, PDO::PARAM_STR);
        $rq->bindValue(":caution", $_caution, PDO::PARAM_STR);
        $rq->bindValue(":comment", $_comment, PDO::PARAM_STR);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        $rq->bindValue(":additionalIbanId", $_additionalIbanId, PDO::PARAM_INT);

        return $rq->execute();
    }

    /**
     * Supprime un garage de la base par son identifiant.
     *
     * @param int $_garageId Identifiant du garage à supprimer
     *
     * @return bool True si la suppression réussit, false sinon
     */
    public function delete($_garageId)
    {
        $request = "DELETE FROM garages WHERE id = :garageId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":garageId", $_garageId, PDO::PARAM_INT);
        return $rq->execute();
    }
}
