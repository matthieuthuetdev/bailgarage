<?php
class paymenthistories
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    public function create($_leasesId, $_amount, $_paymentDate, $_method)
    {
        $request = "INSERT INTO paymenthistories (leasesId, amount, paymentDate, methode) VALUES (:leasesId, :amount, :paymentDate, :methode)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":leasesId", $_leasesId, PDO::PARAM_INT);
        $rq->bindValue(":amount", $_amount);
        $rq->bindValue(":paymentDate", $_paymentDate, PDO::PARAM_STR);
        $rq->bindValue(":methode", $_method, PDO::PARAM_STR);
        return $rq->execute();
    }

    public function read($_ownerId, $_paymentId = null)
    {
        if (is_null($_paymentId)) {
            $request = "SELECT paymenthistories.*, leases.id AS leasesId FROM paymenthistories INNER JOIN leases ON paymenthistories.leasesId = leases.id WHERE leases.ownerId = :ownerId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            return $rq->fetchAll(PDO::FETCH_ASSOC);
        } elseif (is_null($_paymentId)) {
            $request = "SELECT paymenthistories.* FROM paymenthistories INNER JOIN leases ON paymenthistories.leasesId = leases.id WHERE leases.ownerId = :ownerId AND paymenthistories.leasesId = :leasesId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            return $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT paymenthistories.* FROM paymenthistories INNER JOIN leases ON paymenthistories.leasesId = leases.id WHERE leases.ownerId = :ownerId AND paymenthistories.id = :paymentId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
            $rq->execute();
            return $rq->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function update($_paymentId, $_leasesId, $_amount, $_paymentDate, $_method)
    {
        $request = "UPDATE paymenthistories SET leasesId = :leasesId, amount = :amount, paymentDate = :paymentDate, methode = :methode WHERE id = :paymentId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
        $rq->bindValue(":leasesId", $_leasesId, PDO::PARAM_INT);
        $rq->bindValue(":amount", $_amount);
        $rq->bindValue(":paymentDate", $_paymentDate, PDO::PARAM_STR);
        $rq->bindValue(":methode", $_method, PDO::PARAM_STR);
        return $rq->execute();
    }

    public function delete($_paymentId, $_ownerId)
    {
        $check = $this->connection->prepare("SELECT paymenthistories.id FROM paymenthistories INNER JOIN leases ON paymenthistories.leasesId = leases.id WHERE paymenthistories.id = :paymentId AND leases.ownerId = :ownerId");
        $check->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
        $check->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $check->execute();

        if ($check->fetch()) {
            $request = "DELETE FROM paymenthistories WHERE id = :paymentId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
            return $rq->execute();
        }

        return false;
    }
}
?>