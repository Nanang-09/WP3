<?php

namespace App\Support;

class MathCaptcha
{
    public static function generate(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        session(['math_captcha_answer' => $a + $b]);

        return [
            'question' => "{$a} + {$b}",
        ];
    }

    public static function validate(mixed $answer): bool
    {
        $expected = session('math_captcha_answer');
        session()->forget('math_captcha_answer');

        if ($expected === null || $answer === null || $answer === '') {
            return false;
        }

        return (int) $answer === (int) $expected;
    }
}
