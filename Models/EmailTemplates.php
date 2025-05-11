<?php
class EmailTemplate {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance();
    }

    public function read($_name = null) {
        if (is_null($_name)) {
            $request = "SELECT * FROM emailtemplate";
            $rq = $this->connection->prepare($request);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT * FROM emailtemplate WHERE name = :name";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":name", $_name, PDO::PARAM_STR);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);     
        }
        return $result;
    }

    public function update($_name, $_subject, $_content) {
        $request = "UPDATE emailtemplate SET subject = :subject, content = :content WHERE name = :name";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":name", $_name, PDO::PARAM_STR);
        $rq->bindValue(":subject", $_subject, PDO::PARAM_STR);
        $rq->bindValue(":content", $_content, PDO::PARAM_STR);
        return $rq->execute();
    }
}
?>