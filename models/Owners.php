<?php
class Users
{
    private PDO $connection;
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }
    public function create($_name, $_firstName, $_email, $role, $_company, $_address, $_additionalAddress, $_cityName, $_phoneNumber, $_iban, $_bic, $_attachementPath, $_gender,)
    {
        $request = "INSERT INTO users ('name','firstName','email','password','roleId') VALUE (:name, :firstName, :email, :password, 2)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->execute();
        $request = "SELECT id FROM users WHERE email = :email";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->execute();
        $userId = $rq->fetch(PDO::FETCH_ASSOC)["id"];






        $request = "INSERT INTO owners ('userId','company','address','additionalAddress','cityId','phoneNumber','iban','bic','attachementPath','gender') VALUE (:userId, :company, :address, :additionalAddress, :cityId, :iban, :bic, attachmentPath, :gender)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":company", $userId, PDO::PARAM_INT);
        $rq->bindValue(":company", $_company, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":cityid", mt_rand(1, 30000), PDO::PARAM_STR);
        $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":iban", $_iban, PDO::PARAM_STR);
        $rq->bindValue(":bic", $_bic, PDO::PARAM_STR);
        $rq->bindValue(":attachementPath", $_attachementPath, PDO::PARAM_STR);
        $rq->bindValue(":gender", $_gender, PDO::PARAM_STR);
        $rq->execute();
    }
}
