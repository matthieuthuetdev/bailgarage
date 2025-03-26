<?php
class Owners
{
    private PDO $connection;
    private object $user;
    public function __construct()
    {
        $this->connection = Database::getInstance();
        $this->user = new Users();
    }
    public function create($_name, $_firstName, $_email, $_company, $_address, $_additionalAddress, $_phoneNumber, $_iban, $_bic, $_attachmentPath, $_gender)
    {
        $result = $this->user->create($_firstName, $_name, $_email);
        if ($result !== false) {
            $request = "INSERT INTO owners (userId,company,address,additionalAddress,cityId,phoneNumber,iban,bic,attachmentPath,gender) VALUE (:userId, :company, :address, :additionalAddress, :cityId,:phoneNumber ,:iban, :bic, :attachmentPath, :gender)";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":userId", $result["id"], PDO::PARAM_INT);
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
                $password = $result["password"];
                return "Bonjour $_firstName, vous trouverez ci dessou vos informations de connection a l'application Bailgarage : <br> adresse mail : $_email <br> $password";
            }
        } else {
            return "le mail existe déjà dans la base de donnée.";
        }
    }
    public function read($_id = null)
    {
        if (is_null($_id)) {
            $request = "SELECT users.id AS userId, owners.id AS ownerId, users.name, users.firstName, users.email, owners.company, owners.address, owners.additionalAddress,citys.label, owners.phoneNumber, owners.iban, owners.bic, owners.attachmentPath, owners.gender, roles.name AS roleName FROM owners INNER JOIN users ON owners.userId = users.id INNER JOIN citys ON owners.cityId = citys.id INNER JOIN roles ON users.roleId = roles.id ";
            $rq = $this->connection->prepare($request);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT users.id AS userId, owners.id AS ownerId, users.name, users.firstName, users.email, owners.company, owners.address, owners.additionalAddress,citys.label, owners.phoneNumber, owners.iban, owners.bic, owners.attachmentPath, owners.gender FROM owners INNER JOIN users ON owners.userId = users.id INNER JOIN citys ON owners.cityId = citys.id WHERE owners.id = :id";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":id", $_id, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }
    public function update($_ownerId, $_name, $_firstName, $_email, $_company, $_address, $_additionalAddress, $_phoneNumber, $_iban, $_bic, $_attachmentPath, $_gender)
    {
        $userId = $this->read($_ownerId)["userId"];
        $request = "UPDATE users SET users.name = :name, users.firstName = :firstName, users.email = :email WHERE users.id = :userId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":userId", $userId, PDO::PARAM_INT);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":firstName", $_firstName, PDO::PARAM_STR);
        $rq->execute();

        $request = "UPDATE owners SET company = :company, address = :address, additionalAddress = :additionalAddress, phoneNumber = :phoneNumber, iban = :iban, bic = :bic, attachmentPath = :attachmentPath, gender = :gender WHERE owners.id =  :ownerId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_STR);
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
    public function delete($_ownerId)
    {
        $userId =  $this->read($_ownerId)["userId"];
        $request = "DELETE FROM owners WHERE id = :id";
        $rq =  $this->connection->prepare($request);
        $rq->bindValue(":id", $_ownerId, PDO::PARAM_INT);
        $rq->execute();
        $request = "DELETE FROM users WHERE users.id = :id";
        $rq =  $this->connection->prepare($request);
        $rq->bindValue(":id", $_userid, PDO::PARAM_INT);
        $succes = $rq->execute();
    }
    public function searchOwnerByUserId($_userId)
    {
        $request = "SELECT owners.id FROM owners WHERE owners.userId = :userId";
        $rq =  $this->connection->prepare($request);
        $rq->bindValue(":userId", $_userId, PDO::PARAM_INT);
        $rq->execute();
        return $rq->fetch(PDO::FETCH_ASSOC);
    }
}
