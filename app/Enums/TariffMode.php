<?php

namespace App\Enums;

enum TariffMode: string
{
    case PACKAGE = 'package';
    case HOURLY = 'hourly';
    case DAILY = 'daily';
}
