<?php
class Attachments
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    public function create(int $_ownerId, string $_originalFileName, string $_filename)
    {
        $request = "INSERT INTO attachment (ownerId, originalFileName, filename) VALUES (:ownerId, :originalFileName, :filename)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":originalFileName", $_originalFileName, PDO::PARAM_STR);
        $rq->bindValue(":filename", $_filename, PDO::PARAM_STR);

        if ($rq->execute()) {
            return $this->connection->lastInsertId();
        } else {
            return false;
        }
    }

    public function delete(int $_attachmentId)
    {
        $request = "DELETE FROM attachment WHERE id = :attachmentId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":attachmentId", $_attachmentId, PDO::PARAM_INT);
        return $rq->execute();
    }
}
