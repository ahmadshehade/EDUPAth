<?php

namespace App\Enums;

enum UserRoles: string
{
    case Admin = 'admin';
    case Instructor = 'instructor';
    case Student = 'student';
}