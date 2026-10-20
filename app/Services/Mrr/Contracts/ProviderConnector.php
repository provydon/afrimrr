<?php

namespace App\Services\Mrr\Contracts;

use App\Services\Mrr\ConnectorException;
use App\Services\Mrr\MrrResult;

interface ProviderConnector
{
    /**
     * Pull the trailing 30-day successful transaction volume from the provider
     * and return it normalized to USD (the startup's monthly revenue figure).
     *
     * @throws ConnectorException
     */
    public function fetch(string $secret): MrrResult;

    public function provider(): string;
}
