<?php
class Users
{
    private PDO $connection;
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }
    public function signIn(string $_email, string $_password): array
    {
        $request = "SELECT users.id,users.name, users.firstName, users.email, users.password, roles.name AS roleName FROM users INNER JOIN roles ON users.roleId = roles.id WHERE users.email= :email";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->execute();
        $result = $rq->fetch(PDO::FETCH_ASSOC); 
        if (!empty($result)) {
            $succes = password_verify($_password, $result["password"]);
            if ($succes) {
                return $result;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
}
