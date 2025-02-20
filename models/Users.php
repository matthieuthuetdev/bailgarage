<?php
class Users
{
    private PDO $connection;
    public function __construct(PDO $_connection)
    {
        $this->connection = Database::getInstance();
    }
    public function signIn(string $_userName, string $_password): array
    {
        $request = "SELECT utilisateurs.id_utilisateur, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.mail_utilisateur, utilisateurs.pass_utilisateur, habilitations.libelle_niveau FROM utilisateurs INNER JOIN habilitations ON utilisateurs.id_niveau = habilitations.id_niveau WHERE mail_utilisateur= :username";
        $rq = $this->connection->prepare($request);
        $rq->bindParam(":username", $_userName, PDO::PARAM_STR);
        $rq->execute();
        $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) == 1) {
            $succes = password_verify($_password, $result[0]["pass_utilisateur"]);
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
