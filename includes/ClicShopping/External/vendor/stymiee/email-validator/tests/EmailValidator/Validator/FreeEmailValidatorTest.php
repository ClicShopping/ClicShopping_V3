<?php

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;
use EmailValidator\Policy;
use PHPUnit\Framework\TestCase;

class FreeEmailValidatorTest extends TestCase
{
    public function testValidateExplicit(): void
    {
        $policy = [
            'checkFreeEmail' => false
        ];
        $validator = new FreeEmailValidator(new Policy($policy));
        self::assertEquals(true, $validator->validate(new EmailAddress('user@example.com')));
    }

    public function testValidateDefault(): void
    {
        $validator = new FreeEmailValidator(new Policy());
        self::assertEquals(true, $validator->validate(new EmailAddress('user@example.com')));
    }
}
