<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING_CONFIRMATION = 'pending_confirmation';
    case REJECTED = 'rejected';
    case WAITING_PAYMENT = 'waiting_payment';
    case CONFIRMED = 'confirmed';
    case HEADING_TO_LOCATION = 'heading_to_location';
    case ONGOING = 'ongoing';
    case COMPLETED = 'completed';
    case DISPUTED = 'disputed';
}
