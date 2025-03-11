<?php
class Owners
{
    private PDO $connection;
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }
    public function create($_name, $_firstName, $_email, $_company, $_address, $_additionalAddress, $_phoneNumber, $_iban, $_bic, $_attachmentPath, $_gender)
    {
        $request = "INSERT INTO users (name,firstName,email,password,roleId) VALUE (:name, :firstName, :email, :password, 2)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $password = password_hash($_name . $_firstName . mt_rand(0, 1000), PASSWORD_ARGON2I);
        $rq->bindValue(":password", $password, PDO::PARAM_STR);
        $rq->execute();
        $request = "SELECT id FROM users WHERE email = :email";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->execute();
        $userId = $rq->fetch(PDO::FETCH_ASSOC);






        $request = "INSERT INTO owners (userId,company,address,additionalAddress,cityId,phoneNumber,iban,bic,attachmentPath,gender) VALUE (:userId, :company, :address, :additionalAddress, :cityId,:phoneNumber ,:iban, :bic, :attachmentPath, :gender)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":userId", $userId, PDO::PARAM_INT);
        $rq->bindValue(":company", $_company, PDO::PARAM_STR);
        $rq->bindValue(":address", $_address, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":cityId",1 , PDO::PARAM_INT);
        $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":iban", $_iban, PDO::PARAM_STR);
        $rq->bindValue(":bic", $_bic, PDO::PARAM_STR);
        $rq->bindValue(":attachmentPath", $_attachmentPath, PDO::PARAM_STR);
        $rq->bindValue(":gender", $_gender, PDO::PARAM_STR);
        return $rq->execute();
    }
}
