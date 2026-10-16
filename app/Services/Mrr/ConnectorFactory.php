<?php

namespace App\Services\Mrr;

use App\Services\Mrr\Contracts\ProviderConnector;

class ConnectorFactory
{
    public static function make(string $provider): ProviderConnector
    {
        return match ($provider) {
            'paystack' => new PaystackConnector,
            'flutterwave' => new FlutterwaveConnector,
            'stripe' => new StripeConnector,
            default => throw new ConnectorException("Unsupported provider [{$provider}]."),
        };
    }

    public static function supports(string $provider): bool
    {
        return in_array($provider, ['paystack', 'flutterwave', 'stripe'], true);
    }
}
