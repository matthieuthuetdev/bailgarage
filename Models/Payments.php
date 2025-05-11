<?php
class Payments
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    public function create($_leaseId, $_monthPayment, $_status, $_amount, $_methodPayment, $_ownerNote)
    {
        $request = "INSERT INTO payments (leaseId, monthPayment, status, amount, methodPayment, ownerNote) VALUES (:leaseId, :monthPayment, :status, :amount, :methodPayment, :ownerNote)";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":leaseId", $_leaseId, PDO::PARAM_INT);
        $rq->bindValue(":monthPayment", $_monthPayment, PDO::PARAM_STR);
        $rq->bindValue(":status", $_status, PDO::PARAM_STR);
        $rq->bindValue(":amount", $_amount);
        $rq->bindValue(":methodPayment", $_methodPayment, PDO::PARAM_STR);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        return $rq->execute();
    }

    public function read($_ownerId, $_paymentId = null)
    {
        if (is_null($_paymentId)) {
            $request = "SELECT payments.*, leases.id AS leaseId, garageId, leases.tenantId  FROM payments INNER JOIN leases ON payments.leaseId = leases.id WHERE leases.ownerId = :ownerId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            return $rq->fetchAll(PDO::FETCH_ASSOC);
        } elseif ( is_null($_paymentId)) {
            $request = "SELECT payments.* FROM payments INNER JOIN leases ON payments.leaseId = leases.id WHERE leases.ownerId = :ownerId AND payments.leaseId = :leaseId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            return $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT payments.* FROM payments INNER JOIN leases ON payments.leaseId = leases.id WHERE leases.ownerId = :ownerId AND payments.id = :paymentId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
            $rq->execute();
            return $rq->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function update($_paymentId, $_leaseId, $_monthPayment, $_status, $_amount, $_methodPayment, $_ownerNote)
    {
        $request = "UPDATE payments SET leaseId = :leaseId, monthPayment = :monthPayment, status = :status, amount = :amount, methodPayment = :methodPayment, ownerNote = :ownerNote WHERE id = :paymentId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
        $rq->bindValue(":leaseId", $_leaseId, PDO::PARAM_INT);
        $rq->bindValue(":monthPayment", $_monthPayment, PDO::PARAM_STR);
        $rq->bindValue(":status", $_status, PDO::PARAM_STR);
        $rq->bindValue(":amount", $_amount);
        $rq->bindValue(":methodPayment", $_methodPayment, PDO::PARAM_STR);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        return $rq->execute();
    }

    public function delete($_paymentId, $_ownerId)
    {
        $check = $this->connection->prepare("SELECT payments.id FROM payments INNER JOIN leases ON payments.leaseId = leases.id WHERE payments.id = :paymentId AND leases.ownerId = :ownerId");
        $check->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
        $check->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $check->execute();

        if ($check->fetch()) {
            $request = "DELETE FROM payments WHERE id = :paymentId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":paymentId", $_paymentId, PDO::PARAM_INT);
            return $rq->execute();
        }

        return false;
    }
}
