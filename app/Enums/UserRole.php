<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case GUIDE = 'guide';
    case ADMIN = 'admin';
}
