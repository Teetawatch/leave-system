<?php

namespace App\Enums;

enum UserRole: string
{
    case EMPLOYEE = 'employee';
    case DEPARTMENT_HEAD = 'department_head';
    case DEPUTY_DIRECTOR = 'deputy_director'; 
    case DIRECTOR = 'director';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::EMPLOYEE => 'ข้าราชการ',
            self::DEPARTMENT_HEAD => 'หัวหน้าแผนก',
            self::DEPUTY_DIRECTOR => 'รองผู้อำนวยการโรงเรียนพลาธิการ ฯ',
            self::DIRECTOR => 'ผู้อำนวยการโรงเรียนพลาธิการ ฯ',
            self::ADMIN => 'ผู้ดูแลระบบ',
        };
    }
}
