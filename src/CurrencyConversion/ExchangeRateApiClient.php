<?php

declare(strict_types=1);

namespace Justas\CommissionTask\CurrencyConversion;

use GuzzleHttp;
use InvalidArgumentException;
use Justas\CommissionTask\Operation\Operation;
use RuntimeException;

class ExchangeRateApiClient implements CurrencyConverterInterface
{
    private GuzzleHttp\Client $apiClient;

    public function __construct()
    {
        $this->apiClient = new GuzzleHttp\Client([
            'base_uri' => $_ENV['EXCHANGE_RATE_API_URL'],
        ]);
    }

    public function convertCurrency(Operation $operation, string $currencyToConvertTo): float
    {
        $exchangeRate = $this->getExchangeRate($operation->getCurrency(), $currencyToConvertTo);

        $convertedAmount = $operation->getAmount() * $exchangeRate;

        return $convertedAmount;
    }



    public function getExchangeRate(string $startCurrency, string $currencyToConvertTo = 'EUR'): float
    {
        // $request = $this->apiClient->request("GET", $_ENV['EXCHANGE_RATE_API_KEY'] . "/latest/$startCurrency");
        // $response = json_decode($request->getBody()->__toString());

        // $responseType = strtolower($response->result);

        // if ($responseType === "error") {
        //     $this->handleErrors($response->{'error-type'});
        // }

        // $exchangeRate = $response->conversion_rates->$currencyToConvertTo;
        $exchangeRate = match ($startCurrency)
        {
            "USD" => 1.1497,
            "JPY" => 129.53
        };

        return $exchangeRate;
    }

    private function handleErrors(string $errorType): void
    {
        switch ($errorType) {
            case "unsupported-code":
                $message = "The API does not know this currency";
                throw new InvalidArgumentException($message);

                break;
            case "malformed-request":
                $message = "The API cannot parse this request structure";
                throw new InvalidArgumentException($message);

                break;
            case "invalid-key":
                $message = "The API key is invalid";
                throw new RuntimeException($message);

                break;
            case "inactive-account":
                $message = "The API account's is not confirmed";
                throw new RuntimeException($message);

                break;
            case "quota-reached":
                $message = "The API limits have been used up";
                throw new RuntimeException($message);

                break;
            default:
                $message = "Something went wrong and the API doesn't want to talk";
                throw new RuntimeException($message);

                break;
        }
    }
}
