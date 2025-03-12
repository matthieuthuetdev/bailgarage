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
        $password = $_name . $_firstName . mt_rand(0, 1000);

        $rq->bindValue(":password", password_hash($password, PASSWORD_ARGON2I), PDO::PARAM_STR);
        $rq->execute();
        $request = "SELECT id FROM users WHERE email = :email";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        if ($rq->execute()) {
            $userId = $rq->fetch(PDO::FETCH_ASSOC);
            $request = "INSERT INTO owners (userId,company,address,additionalAddress,cityId,phoneNumber,iban,bic,attachmentPath,gender) VALUE (:userId, :company, :address, :additionalAddress, :cityId,:phoneNumber ,:iban, :bic, :attachmentPath, :gender)";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":userId", $userId, PDO::PARAM_INT);
            $rq->bindValue(":company", $_company, PDO::PARAM_STR);
            $rq->bindValue(":address", $_address, PDO::PARAM_STR);
            $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
            $rq->bindValue(":cityId", 1, PDO::PARAM_INT);
            $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
            $rq->bindValue(":iban", $_iban, PDO::PARAM_STR);
            $rq->bindValue(":bic", $_bic, PDO::PARAM_STR);
            $rq->bindValue(":attachmentPath", $_attachmentPath, PDO::PARAM_STR);
            $rq->bindValue(":gender", $_gender, PDO::PARAM_STR);
            if ($rq->execute()) {
                return "Bonjour $_firstName, vous trouverez ci dessou vos informations de connection a l'application Bailgarage : <br> adresse mail : $_email <br> $password";
            }
        } else {
            return "le mail existe déjà dans la base de donnée.";
        }
    }
    public function read($_id = null)
    {
        if (is_null($_id)) {
            $request = "SELECT users.name, users.firstName, users.email, owners.company, owners.address, owners.additionalAddress,citys.label, owners.phoneNumber, owners.iban, owners.bic, owners.attachmentPath, owners.gender FROM owners INNER JOIN users ON owners.userId = users.id INNER JOIN citys ON owners.cityId = citys.id ";
            $rq = $this->connection->prepare($request);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT users.name, users.firstName, users.email, owners.company, owners.address, owners.additionalAddress,citys.label, owners.phoneNumber, owners.iban, owners.bic, owners.attachmentPath, owners.gender FROM owners INNER JOIN users ON owners.userId = users.id INNER JOIN citys ON owners.cityId = citys.id WHERE owners.id = :id";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":id", $_id, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }
    public function update($_name, $_firstName, $_email, $_company, $_address, $_additionalAddress, $_phoneNumber, $_iban, $_bic, $_attachmentPath, $_gender)
    {
        $request = "UPDATE users SET name = :name, firstName = :firstName WHERE email = :email";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
        $rq->execute();

        $request = "UPDATE owners SET company = :company, address = :address, additionalAddress = :additionalAddress, phoneNumber = :phoneNumber, iban = :iban, bic = :bic, attachmentPath = :attachmentPath, gender = :gender WHERE userId = (SELECT id FROM users WHERE email = :email)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->bindValue(":company", $_company, PDO::PARAM_STR);
        $rq->bindValue(":address", $_address, PDO::PARAM_STR);
        $rq->bindValue(":additionalAddress", $_additionalAddress, PDO::PARAM_STR);
        $rq->bindValue(":phoneNumber", $_phoneNumber, PDO::PARAM_STR);
        $rq->bindValue(":iban", $_iban, PDO::PARAM_STR);
        $rq->bindValue(":bic", $_bic, PDO::PARAM_STR);
        $rq->bindValue(":attachmentPath", $_attachmentPath, PDO::PARAM_STR);
        $rq->bindValue(":gender", $_gender, PDO::PARAM_STR);

        return $rq->execute();
    }
}
