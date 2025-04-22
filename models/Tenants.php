<?php
class Tenants
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    public function create($_ownerId, $_name, $_firstName, $_company, $_address, $_additionalAddress, $_cityId, $_cityName, $_postalCode, $_phoneNumber, $_landlinePhoneNumber, $_email, $_rgpd, $_attachmentPath, $_gender, $_receipt, $_ownerNote)
    {
        $request = "INSERT INTO tenants (ownerId, name, firstName, company, address, additionalAddress, cityId, cityName, postalCode, phoneNumber, landlinePhoneNumber, email, rgpd, attachmentPath, gender, receipt, ownerNote) VALUES (:ownerId, :name, :firstName, :company, :address, :additionalAddress, :cityId, :cityName, :postalCode, :phoneNumber, :landlinePhoneNumber, :email, :rgpd, :attachmentPath, :gender, :receipt, :ownerNote)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
        $rq->bindValue(":company", $_company, PDO::PARAM_STR);
        $rq->bindValue(":address", $_address, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":cityName", $_cityName, PDO::PARAM_STR);
        $rq->bindValue(":postalCode", $_postalCode, PDO::PARAM_STR);
        $rq->bindValue(":cityId", $_cityId, PDO::PARAM_INT);
        $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":landlinePhoneNumber", $_landlinePhoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->bindValue(":rgpd", $_rgpd, PDO::PARAM_INT);
        $rq->bindValue(":attachmentPath", $_attachmentPath, PDO::PARAM_STR);
        $rq->bindValue(":gender", $_gender, PDO::PARAM_INT);
        $rq->bindValue(":receipt", $_receipt, PDO::PARAM_INT);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        return $rq->execute();
    }

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

    public function update($_tenantId, $_ownerId, $_name, $_firstName, $_company, $_address, $_additionalAddress, $_cityId, $_cityName , $_postalCode, $_phoneNumber, $_landlinePhoneNumber, $_email, $_rgpd, $_attachmentPath, $_gender, $_receipt, $_ownerNote)
    {
        $request = "UPDATE tenants SET name = :name, firstName = :firstName, company = :company, address = :address, additionalAddress = :additionalAddress, cityId = :cityId ,cityName = :cityName, postalCode = :postalCode, phoneNumber = :phoneNumber, landlinePhoneNumber = :landlinePhoneNumber, email = :email, rgpd = :rgpd, attachmentPath = :attachmentPath, gender = :gender, receipt = :receipt, ownerNote = :ownerNote WHERE id = :tenantId AND ownerId = :ownerId";
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
        $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":landlinePhoneNumber", $_landlinePhoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->bindValue(":rgpd", $_rgpd, PDO::PARAM_INT);
        $rq->bindValue(":attachmentPath", $_attachmentPath, PDO::PARAM_STR);
        $rq->bindValue(":gender", $_gender, PDO::PARAM_INT);
        $rq->bindValue(":receipt", $_receipt, PDO::PARAM_INT);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        return $rq->execute();
    }

    public function delete($_tenantId, $_ownerId)
    {
        $request = "DELETE FROM tenants WHERE id = :tenantId AND ownerId = :ownerId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":tenantId", $_tenantId, PDO::PARAM_INT);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        return $rq->execute();
    }
}
