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
        $request = "SELECT * FROM users WHERE users.email= :email";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":email", $_email, PDO::PARAM_STR);
        $rq->execute();
        $result = $rq->fetch(PDO::FETCH_ASSOC); 
        // return $result;
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
