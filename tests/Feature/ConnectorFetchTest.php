<?php

namespace Tests\Feature;

use App\Services\Mrr\ConnectorException;
use App\Services\Mrr\FlutterwaveConnector;
use App\Services\Mrr\PaystackConnector;
use App\Services\Mrr\StripeConnector;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConnectorFetchTest extends TestCase
{
    public function test_paystack_sums_30_day_transaction_volume_to_usd(): void
    {
        Http::fake([
            'api.paystack.co/transaction/totals*' => Http::response([
                'data' => [
                    'total_transactions' => 42,
                    'total_volume' => 500_000,
                    // ₦5,000 (500,000 kobo) -> $3.15, plus $100 (10,000 cents) in USD.
                    'total_volume_by_currency' => [
                        ['currency' => 'NGN', 'amount' => 500_000],
                        ['currency' => 'USD', 'amount' => 10_000],
                    ],
                ],
            ]),
        ]);

        $result = (new PaystackConnector)->fetch('sk_test_x');

        $this->assertSame(42, $result->activeSubscriptions);
        $this->assertSame(315 + 10_000, $result->usdCents);
        $this->assertSame('NGN', $result->displayCurrency());
        $this->assertSame(500_000, $result->displayLocalMinor());
    }

    public function test_paystack_401_throws_connector_exception(): void
    {
        Http::fake(['api.paystack.co/*' => Http::response([], 401)]);

        $this->expectException(ConnectorException::class);
        $this->expectExceptionMessage('401');
        (new PaystackConnector)->fetch('bad');
    }

    public function test_flutterwave_sums_successful_transactions(): void
    {
        Http::fake([
            'api.flutterwave.com/v3/transactions*' => Http::response([
                'data' => [
                    // amounts in major units.
                    ['status' => 'successful', 'currency' => 'NGN', 'amount' => 5_000],
                    ['status' => 'successful', 'currency' => 'NGN', 'amount' => 5_000],
                    // non-successful is ignored.
                    ['status' => 'failed', 'currency' => 'NGN', 'amount' => 9_999],
                ],
                'meta' => ['page_info' => ['total_pages' => 1]],
            ]),
        ]);

        $result = (new FlutterwaveConnector)->fetch('FLWSECK-x');

        // ₦10,000 total -> 1,000,000 kobo -> $6.30; two successful txns.
        $this->assertSame(2, $result->activeSubscriptions);
        $this->assertSame(630, $result->usdCents);
        $this->assertSame(1_000_000, $result->displayLocalMinor());
    }

    public function test_stripe_sums_net_succeeded_charges(): void
    {
        Http::fake([
            'api.stripe.com/v1/charges*' => Http::response([
                'data' => [
                    // $40 charge, fully paid.
                    ['id' => 'ch_1', 'status' => 'succeeded', 'paid' => true, 'amount' => 4_000, 'amount_refunded' => 0, 'currency' => 'usd'],
                    // $30 charge with $10 refunded -> nets $20.
                    ['id' => 'ch_2', 'status' => 'succeeded', 'paid' => true, 'amount' => 3_000, 'amount_refunded' => 1_000, 'currency' => 'usd'],
                    // failed charge ignored.
                    ['id' => 'ch_3', 'status' => 'failed', 'paid' => false, 'amount' => 9_999, 'amount_refunded' => 0, 'currency' => 'usd'],
                ],
                'has_more' => false,
            ]),
        ]);

        $result = (new StripeConnector)->fetch('sk_live_x');

        $this->assertSame(2, $result->activeSubscriptions); // two counted charges
        $this->assertSame(4_000 + 2_000, $result->usdCents); // $40 + $20 net
        $this->assertSame('USD', $result->displayCurrency());
    }

    public function test_stripe_401_throws_connector_exception(): void
    {
        Http::fake(['api.stripe.com/*' => Http::response([], 401)]);

        $this->expectException(ConnectorException::class);
        (new StripeConnector)->fetch('bad');
    }
}
