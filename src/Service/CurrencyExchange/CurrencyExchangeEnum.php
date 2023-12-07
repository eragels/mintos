<?php

namespace App\Service\CurrencyExchange;

enum CurrencyExchangeEnum: int
{
    case MaxRetries         = 3;
    case InitialRetryDelay  = 500;
    case RetryMultiplier    = 2;
}
