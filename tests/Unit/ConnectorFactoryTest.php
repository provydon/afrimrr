<?php

namespace Tests\Unit;

use App\Services\Mrr\ConnectorException;
use App\Services\Mrr\ConnectorFactory;
use App\Services\Mrr\FlutterwaveConnector;
use App\Services\Mrr\PaystackConnector;
use App\Services\Mrr\StripeConnector;
use PHPUnit\Framework\TestCase;

class ConnectorFactoryTest extends TestCase
{
    public function test_it_builds_each_supported_connector(): void
    {
        $this->assertInstanceOf(PaystackConnector::class, ConnectorFactory::make('paystack'));
        $this->assertInstanceOf(FlutterwaveConnector::class, ConnectorFactory::make('flutterwave'));
        $this->assertInstanceOf(StripeConnector::class, ConnectorFactory::make('stripe'));
    }

    public function test_connector_reports_its_provider(): void
    {
        $this->assertSame('paystack', ConnectorFactory::make('paystack')->provider());
        $this->assertSame('flutterwave', ConnectorFactory::make('flutterwave')->provider());
        $this->assertSame('stripe', ConnectorFactory::make('stripe')->provider());
    }

    public function test_unsupported_provider_throws(): void
    {
        $this->expectException(ConnectorException::class);
        ConnectorFactory::make('monnify');
    }

    public function test_supports_reflects_available_providers(): void
    {
        $this->assertTrue(ConnectorFactory::supports('paystack'));
        $this->assertTrue(ConnectorFactory::supports('flutterwave'));
        $this->assertTrue(ConnectorFactory::supports('stripe'));
        $this->assertFalse(ConnectorFactory::supports('monnify'));
    }
}
