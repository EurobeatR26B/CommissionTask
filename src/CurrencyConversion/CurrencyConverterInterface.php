<?php

declare (strict_types=1);

namespace Justas\CommissionTask\CurrencyConversion;

interface CurrencyConverterInterface
{
    public function getExchangeRate(string $startCurrency, string $endCurrency);
    public function convertCurrency (float $amount, string $startCurrency, string $endCurrency);
}