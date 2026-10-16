<?php

namespace App\Services\Mrr\Contracts;

use App\Services\Mrr\MrrResult;

interface ProviderConnector
{
    /**
     * Pull active subscriptions from the provider and return normalized MRR.
     *
     * @throws \App\Services\Mrr\ConnectorException
     */
    public function fetch(string $secret): MrrResult;

    public function provider(): string;
}
