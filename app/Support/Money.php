<?php

namespace App\Support;

class Money
{
    private const SYMBOLS = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£',
        'NGN' => '₦', 'GHS' => 'GH₵', 'KES' => 'KSh', 'ZAR' => 'R',
        'EGP' => 'E£', 'XOF' => 'CFA', 'XAF' => 'CFA', 'RWF' => 'FRw',
        'UGX' => 'USh', 'TZS' => 'TSh', 'ZMW' => 'ZK', 'MAD' => 'DH', 'ETB' => 'Br',
    ];

    public static function symbol(string $currency): string
    {
        return self::SYMBOLS[strtoupper($currency)] ?? strtoupper($currency).' ';
    }

    /**
     * Format a minor-unit amount (cents/kobo) with its currency symbol.
     */
    public static function format(int $minor, string $currency): string
    {
        $major = $minor / 100;
        $decimals = $major < 100 && fmod($major, 1.0) !== 0.0 ? 2 : 0;

        return self::symbol($currency).number_format($major, $decimals);
    }

    public static function usd(int $cents): string
    {
        return self::format($cents, 'USD');
    }

    /**
     * Compact form for cards/badges, e.g. $12.5K, $3.2M.
     */
    public static function compactUsd(int $cents): string
    {
        $dollars = $cents / 100;

        if ($dollars >= 1_000_000) {
            return '$'.rtrim(rtrim(number_format($dollars / 1_000_000, 1), '0'), '.').'M';
        }

        if ($dollars >= 1_000) {
            return '$'.rtrim(rtrim(number_format($dollars / 1_000, 1), '0'), '.').'K';
        }

        return '$'.number_format($dollars, 0);
    }
}
