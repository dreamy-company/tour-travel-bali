<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case BANNED = 'banned';
    case PENDING_VERIFICATION = 'pending_verification';
}
