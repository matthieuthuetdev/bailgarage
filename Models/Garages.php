<?php
class Garages
{
    private PDO $connection;
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    public function create($_ownerId, $_address, $_additionalAddress, $_cityId, $_cityName, $_postalCode, $_country, $_garageNumber, $_lotNumber, $_rentWithoutCharges, $_charges, $_surface, $_reference, $_trustee, $_caution, $_comment, $_ownerNote, $_additionalIbanId)
    {
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
        return $rq->execute();
    }

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

    public function update($_garageId, $_ownerId, $_address, $_additionalAddress, $_cityId, $_cityName, $_postalCode, $_country, $_garageNumber, $_lotNumber, $_rentWithoutCharges, $_charges, $_surface, $_reference, $_trustee, $_caution, $_additionalIbanId, $_comment, $_ownerNote)
    {
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

    public function delete($_garageId)
    {
        $request = "DELETE FROM garages WHERE id = :garageId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":garageId", $_garageId, PDO::PARAM_INT);
        return $rq->execute();
    }
}
