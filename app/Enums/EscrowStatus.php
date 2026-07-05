<?php

namespace App\Enums;

enum EscrowStatus: string
{
    case WAITING_PAYMENT = 'waiting_payment';
    case PAID_IN_ESCROW = 'paid_in_escrow';
    case RELEASED_TO_GUIDE = 'released_to_guide';
    case REFUNDED = 'refunded';
}
