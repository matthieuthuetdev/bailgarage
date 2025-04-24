public function read($_id = null)
    {
        if (is_null($_id)) {
            $request = "SELECT users.id, users.name, users.firstName, users.email, owners.company, owners.address, owners.additionalAddress,citys.label, owners.phoneNumber, owners.iban, owners.bic, owners.attachmentPath, owners.gender, roles.name AS roleName FROM owners INNER JOIN users ON owners.userId = users.id INNER JOIN citys ON owners.cityId = citys.id INNER JOIN roles ON users.roleId = roles.id ";
            $rq = $this->connection->prepare($request);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT users.name, users.firstName, users.email, owners.company, owners.address, owners.additionalAddress,citys.label, owners.phoneNumber, owners.iban, owners.bic, owners.attachmentPath, owners.gender FROM owners INNER JOIN users ON owners.userId = users.id INNER JOIN citys ON owners.cityId = citys.id WHERE users.id = :id";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":id", $_id, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }
