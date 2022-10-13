<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;

interface IValidator
{
    public function validate(EmailAddress $email): bool;
}
