<?php

declare(strict_types=1);

namespace PhpResponse\Response;

use PhpResponse\Database\Database;
use PhpResponse\Database\Transaction;
use PhpResponse\Database\Transaction\SqlTransaction;
use Throwable;

final class TransactionalResponse implements Response
{
    private Response $origin;
    private Transaction $transaction;

    public function __construct(Response $origin, Transaction|Database $transaction)
    {
        $this->origin = $origin;
        $this->transaction = $transaction instanceof Transaction
            ? $transaction
            : new SqlTransaction($transaction);
    }

    public function media(Media $media): Media
    {
        $this->transaction->start();
        try {
            $result = $this->origin->media($media);
            $this->transaction->commit();
            return $result;
        } catch (Throwable $ex) {
            $this->transaction->rollback();
            throw $ex;
        }
    }
}
