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
    public function test_paystack_normalizes_active_subscriptions_to_monthly_usd(): void
    {
        Http::fake([
            'api.paystack.co/subscription*' => Http::response([
                'data' => [
                    // ₦5,000/mo (500,000 kobo) -> $3.15.
                    ['status' => 'active', 'plan' => ['interval' => 'monthly', 'currency' => 'NGN', 'amount' => 500_000]],
                    // ₦120,000/yr -> ₦10,000/mo (1,000,000 kobo) -> $6.30.
                    ['status' => 'active', 'plan' => ['interval' => 'annually', 'currency' => 'NGN', 'amount' => 12_000_000]],
                    // Inactive is ignored.
                    ['status' => 'cancelled', 'plan' => ['interval' => 'monthly', 'currency' => 'NGN', 'amount' => 999_999]],
                ],
                'meta' => ['pageCount' => 1],
            ]),
        ]);

        $result = (new PaystackConnector)->fetch('sk_test_x');

        $this->assertSame(2, $result->activeSubscriptions);
        $this->assertSame(315 + 630, $result->usdCents);
        $this->assertSame('NGN', $result->displayCurrency());
        $this->assertSame(1_500_000, $result->displayLocalMinor());
    }

    public function test_paystack_401_throws_connector_exception(): void
    {
        Http::fake(['api.paystack.co/*' => Http::response([], 401)]);

        $this->expectException(ConnectorException::class);
        $this->expectExceptionMessage('401');
        (new PaystackConnector)->fetch('bad');
    }

    public function test_flutterwave_joins_subscriptions_to_payment_plans(): void
    {
        Http::fake([
            'api.flutterwave.com/v3/payment-plans*' => Http::response([
                'data' => [
                    // amounts in major units.
                    ['id' => 77, 'interval' => 'monthly', 'currency' => 'NGN', 'amount' => 5_000],
                ],
                'meta' => ['page_info' => ['total_pages' => 1]],
            ]),
            'api.flutterwave.com/v3/subscriptions*' => Http::response([
                'data' => [
                    ['status' => 'active', 'plan' => 77],
                    ['status' => 'cancelled', 'plan' => 77],
                ],
                'meta' => ['page_info' => ['total_pages' => 1]],
            ]),
        ]);

        $result = (new FlutterwaveConnector)->fetch('FLWSECK-x');

        // ₦5,000 major -> 500,000 kobo -> $3.15; one active sub.
        $this->assertSame(1, $result->activeSubscriptions);
        $this->assertSame(315, $result->usdCents);
        $this->assertSame(500_000, $result->displayLocalMinor());
    }

    public function test_stripe_normalizes_quantity_interval_and_counts_sub_once(): void
    {
        Http::fake([
            'api.stripe.com/v1/subscriptions*' => Http::response([
                'data' => [
                    [
                        'id' => 'sub_1',
                        'items' => ['data' => [
                            // $20/mo x2 = $40/mo.
                            ['quantity' => 2, 'price' => ['unit_amount' => 2_000, 'currency' => 'usd', 'recurring' => ['interval' => 'month', 'interval_count' => 1]]],
                            // $120/yr = $10/mo, same subscription (second line item).
                            ['quantity' => 1, 'price' => ['unit_amount' => 12_000, 'currency' => 'usd', 'recurring' => ['interval' => 'year', 'interval_count' => 1]]],
                        ]],
                    ],
                ],
                'has_more' => false,
            ]),
        ]);

        $result = (new StripeConnector)->fetch('sk_live_x');

        $this->assertSame(1, $result->activeSubscriptions); // one sub, two line items
        $this->assertSame(4_000 + 1_000, $result->usdCents); // $40 + $10
        $this->assertSame('USD', $result->displayCurrency());
    }
}
