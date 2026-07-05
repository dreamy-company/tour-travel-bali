<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case QRIS = 'qris';
    case CREDIT_CARD = 'credit_card';
    case VIRTUAL_ACCOUNT = 'virtual_account';
}
