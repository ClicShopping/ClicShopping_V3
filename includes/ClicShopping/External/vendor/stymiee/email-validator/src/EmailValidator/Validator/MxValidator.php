<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;

class MxValidator extends AValidator
{
    public function validate(EmailAddress $email): bool
    {
        $valid = true;
        if ($this->policy->validateMxRecord()) {
            $domain = sprintf('%s.', $email->getDomain());
            $valid = checkdnsrr(idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46), 'MX');
        }
        return $valid;
    }
}
