<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;
use EmailValidator\Policy;

abstract class AValidator implements IValidator
{
    /**
     * @var Policy
     */
    protected $policy;

    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }

    abstract public function validate(EmailAddress $email): bool;
}
