<?php
class Users
{
    private PDO $connection;
    public function __construct()
    {
        $this->connection = Database::getInstance();
    }
    public function signIn(string $_userName, string $_password): array
    {
        $request = "SELECT user.user_id, user.email, user.prenom,user.nom, user.email, user.password,  FROM user WHERE email = ";
        $rq = $this->connection->prepare($request);
        $rq->bindParam(":email", $_email, PDO::PARAM_STR);
        $rq->execute();
        $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) == 1) {
            $succes = password_verify($_password, $result[0]["password"]);
            if ($succes) {
                return $result[0];
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
}
