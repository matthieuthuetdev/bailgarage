<?php

class Leases
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance();
    }

    public function create(
        $_tenantId,
        $_garageId,
        $_ownerId,
        $_madeThe,
        $_madeIn,
        $_startDate,
        $_duration,
        $_rentAmount,
        $_rentAmountInLetter,
        $_chargesAmount,
        $_chargesAmountInLetter,
        $_totalAmountMonthly,
        $_totalAmountMonthlyInLetter,
        $_prorata,
        $_prorataInLetter,
        $_caution,
        $_cautionInLetter,
        $_numberOfKey,
        $_numberOfBeep,
        $_status,
        $_ownerNote,
        $_reference
    ) {
        $request = "INSERT INTO leases (tenantId, garageId, ownerId, madeThe, madeIn, startDate, duration, rentAmount, rentAmountInLetter, chargesAmount, chargesAmountInLetter, totalAmountMonthly, totalAmountMonthlyInLetter, prorata, prorataInLetter, caution, cautionInLetter, numberOfKey, numberOfBeep, status, ownerNote, reference) VALUES (:tenantId, :garageId, :ownerId, :madeThe, :madeIn, :startDate, :duration, :rentAmount, :rentAmountInLetter, :chargesAmount, :chargesAmountInLetter, :totalAmountMonthly, :totalAmountMonthlyInLetter, :prorata, :prorataInLetter, :caution, :cautionInLetter, :numberOfKey, :numberOfBeep, :status, :ownerNote, :reference)";

        $rq = $this->connection->prepare($request);

        $rq->bindValue(":tenantId", $_tenantId, PDO::PARAM_INT);
        $rq->bindValue(":garageId", $_garageId, PDO::PARAM_INT);
        $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
        $rq->bindValue(":madeThe", $_madeThe, PDO::PARAM_STR);
        $rq->bindValue(":madeIn", $_madeIn, PDO::PARAM_STR);
        $rq->bindValue(":startDate", $_startDate, PDO::PARAM_STR);
        $rq->bindValue(":duration", $_duration, PDO::PARAM_INT);
        $rq->bindValue(":rentAmount", $_rentAmount, PDO::PARAM_STR);
        $rq->bindValue(":rentAmountInLetter", $_rentAmountInLetter, PDO::PARAM_STR);
        $rq->bindValue(":chargesAmount", $_chargesAmount, PDO::PARAM_STR);
        $rq->bindValue(":chargesAmountInLetter", $_chargesAmountInLetter, PDO::PARAM_STR);
        $rq->bindValue(":totalAmountMonthly", $_totalAmountMonthly, PDO::PARAM_STR);
        $rq->bindValue(":totalAmountMonthlyInLetter", $_totalAmountMonthlyInLetter, PDO::PARAM_STR);
        $rq->bindValue(":prorata", $_prorata, PDO::PARAM_STR);
        $rq->bindValue(":prorataInLetter", $_prorataInLetter, PDO::PARAM_STR);
        $rq->bindValue(":caution", $_caution, PDO::PARAM_STR);
        $rq->bindValue(":cautionInLetter", $_cautionInLetter, PDO::PARAM_STR);
        $rq->bindValue(":numberOfKey", $_numberOfKey, PDO::PARAM_INT);
        $rq->bindValue(":numberOfBeep", $_numberOfBeep, PDO::PARAM_INT);
        $rq->bindValue(":status", $_status, PDO::PARAM_INT);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        $rq->bindValue(":reference", $_reference, PDO::PARAM_STR);

        return $rq->execute();
    }

    public function read($_ownerId, $_leaseId = null)
    {
        if (is_null($_leaseId)) {
            $request = "SELECT * FROM leases WHERE leases.ownerId = :ownerId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $request = "SELECT leases.* FROM leases WHERE leases.ownerId = :ownerId AND leases.id = :leaseId";
            $rq = $this->connection->prepare($request);
            $rq->bindValue(":ownerId", $_ownerId, PDO::PARAM_INT);
            $rq->bindValue(":leaseId", $_leaseId, PDO::PARAM_INT);
            $rq->execute();
            $result = $rq->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }

    public function update(
        $_leaseId,
        $_tenantId,
        $_garageId,
        $_madeThe,
        $_madeIn,
        $_startDate,
        $_duration,
        $_rentAmount,
        $_rentAmountInLetter,
        $_chargesAmount,
        $_chargesAmountInLetter,
        $_totalAmountMonthly,
        $_totalAmountMonthlyInLetter,
        $_prorata,
        $_prorataInLetter,
        $_caution,
        $_cautionInLetter,
        $_numberOfKey,
        $_numberOfBeep,
        $_status,
        $_ownerNote,
        $_reference
    ) {
        $request = "UPDATE leases SET tenantId = :tenantId, garageId = :garageId, madeThe = :madeThe, madeIn = :madeIn, startDate = :startDate, duration = :duration, rentAmount = :rentAmount, rentAmountInLetter = :rentAmountInLetter, chargesAmount = :chargesAmount, chargesAmountInLetter = :chargesAmountInLetter, totalAmountMonthly = :totalAmountMonthly, totalAmountMonthlyInLetter = :totalAmountMonthlyInLetter, prorata = :prorata, prorataInLetter = :prorataInLetter, caution = :caution, cautionInLetter = :cautionInLetter, numberOfKey = :numberOfKey, numberOfBeep = :numberOfBeep, status = :status, ownerNote = :ownerNote, reference = :reference WHERE id = :leaseId";

        $rq = $this->connection->prepare($request);

        $rq->bindValue(":leaseId", $_leaseId, PDO::PARAM_INT);
        $rq->bindValue(":tenantId", $_tenantId, PDO::PARAM_INT);
        $rq->bindValue(":garageId", $_garageId, PDO::PARAM_INT);
        $rq->bindValue(":madeThe", $_madeThe, PDO::PARAM_STR);
        $rq->bindValue(":madeIn", $_madeIn, PDO::PARAM_STR);
        $rq->bindValue(":startDate", $_startDate, PDO::PARAM_STR);
        $rq->bindValue(":duration", $_duration, PDO::PARAM_INT);
        $rq->bindValue(":rentAmount", $_rentAmount, PDO::PARAM_STR);
        $rq->bindValue(":rentAmountInLetter", $_rentAmountInLetter, PDO::PARAM_STR);
        $rq->bindValue(":chargesAmount", $_chargesAmount, PDO::PARAM_STR);
        $rq->bindValue(":chargesAmountInLetter", $_chargesAmountInLetter, PDO::PARAM_STR);
        $rq->bindValue(":totalAmountMonthly", $_totalAmountMonthly, PDO::PARAM_STR);
        $rq->bindValue(":totalAmountMonthlyInLetter", $_totalAmountMonthlyInLetter, PDO::PARAM_STR);
        $rq->bindValue(":prorata", $_prorata, PDO::PARAM_STR);
        $rq->bindValue(":prorataInLetter", $_prorataInLetter, PDO::PARAM_STR);
        $rq->bindValue(":caution", $_caution, PDO::PARAM_STR);
        $rq->bindValue(":cautionInLetter", $_cautionInLetter, PDO::PARAM_STR);
        $rq->bindValue(":numberOfKey", $_numberOfKey, PDO::PARAM_INT);
        $rq->bindValue(":numberOfBeep", $_numberOfBeep, PDO::PARAM_INT);
        $rq->bindValue(":status", $_status, PDO::PARAM_INT);
        $rq->bindValue(":ownerNote", $_ownerNote, PDO::PARAM_STR);
        $rq->bindValue(":reference", $_reference, PDO::PARAM_STR);

        return $rq->execute();
    }

    public function delete($_leaseId)
    {
        $request = "DELETE FROM leases WHERE id = :leaseId";
        $rq = $this->connection->prepare($request);
        $rq->bindValue(":leaseId", $_leaseId, PDO::PARAM_INT);
        return $rq->execute();
    }
}
