<?php

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;
use EmailValidator\Policy;
use PHPUnit\Framework\TestCase;

class MxValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $policy = [
            'checkMxRecords' => false
        ];
        $validator = new MxValidator(new Policy($policy));
        self::assertEquals(true, $validator->validate(new EmailAddress('user@example.com')));
    }
}
