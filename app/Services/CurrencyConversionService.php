<?php

namespace App\Services;

class CurrencyConversionService
{
    /**
     * Exchange rates (base currency: USD).
     * These can be updated from settings or API in the future.
     */
    private array $exchangeRates = [
        'USD' => 1.0,      // Base currency
        'TZS' => 2600.0,   // 1 USD = 2600 TZS (approximate)
        'USDT' => 1.0,     // 1 USD = 1 USDT (stablecoin pegged to USD)
    ];

    /**
     * Convert amount from one currency to another.
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Convert to USD first (base currency)
        $amountInUsd = $this->toUsd($amount, $fromCurrency);

        // Convert from USD to target currency
        return $this->fromUsd($amountInUsd, $toCurrency);
    }

    /**
     * Convert amount to USD.
     */
    public function toUsd(float $amount, string $fromCurrency): float
    {
        if ($fromCurrency === 'USD') {
            return $amount;
        }

        $rate = $this->exchangeRates[$fromCurrency] ?? 1.0;

        return $amount / $rate;
    }

    /**
     * Convert amount from USD.
     */
    public function fromUsd(float $usdAmount, string $toCurrency): float
    {
        if ($toCurrency === 'USD') {
            return $usdAmount;
        }

        $rate = $this->exchangeRates[$toCurrency] ?? 1.0;

        return $usdAmount * $rate;
    }

    /**
     * Get exchange rate between two currencies.
     */
    public function getRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $fromRate = $this->exchangeRates[$fromCurrency] ?? 1.0;
        $toRate = $this->exchangeRates[$toCurrency] ?? 1.0;

        return $toRate / $fromRate;
    }

    /**
     * Get currency for payment method.
     */
    public function getCurrencyForPaymentMethod(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'zenopay_mobile' => 'TZS',
            'paypal' => 'USD',
            'trc20' => 'USDT',
            default => 'USD',
        };
    }
}
