<?php
class additionalibans
{
    private PDO $connection;
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }
    public function create($_ownerId, $_name, $_iban, $_bic)
    {
        $request = "INSERT INTO additionalibans (ownerId, name, iban, bic ) VALUES (:ownerId, :name,:iban, :bic)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":iban", $_iban, PDO::PARAM_STR);
        $rq->bindValue(":bic", $_bic, PDO::PARAM_STR);
        return $rq->execute();
    }
    public function read($_ownerId, $_additionalIbanId = null)
    {
        if (is_null($_additionalIbanId)) {
            $request = "SELECT * FROM additionalibans WHERE ownerId = :ownerId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT * FROM additionalibans WHERE ownerId = :ownerId AND id = :additionalIbanId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->bindValue(":additionalIbanId", $_additionalIbanId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }
    public function update($_additionalIbanId, $_ownerId, $_name, $_iban, $_bic)
    {
        $request = "UPDATE additionalibans SET name = :name, iban = :iban, bic = :bic WHERE id = :additionalIbanId AND ownerId = :ownerId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":additionalIbanId", $_additionalIbanId, PDO::PARAM_INT);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":iban", $_iban, PDO::PARAM_STR);
        $rq->bindValue(":bic", $_bic, PDO::PARAM_STR);
        return $rq->execute();
    }
    public function delete($_additionalIbanId)
    {
        $request = "DELETE FROM additionalibans WHERE id = :additionalIbanId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":additionalIbanId", $_additionalIbanId, PDO::PARAM_INT);
        return $rq->execute();
    }
}
