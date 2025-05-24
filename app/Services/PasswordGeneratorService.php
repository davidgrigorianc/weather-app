<?php
namespace App\Services;

use Illuminate\Support\Str;

class PasswordGeneratorService
{
    public function generate(int $length = 10): string
    {
        return Str::random($length);
    }
}
