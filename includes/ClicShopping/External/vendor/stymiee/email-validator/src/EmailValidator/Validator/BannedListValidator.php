<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;

class BannedListValidator extends AValidator
{
    public function validate(EmailAddress $email): bool
    {
        $valid = true;
        if ($this->policy->checkBannedListedEmail()) {
            $domain = $email->getDomain();
            $valid = !in_array($domain, $this->policy->getBannedList(), true);
        }
        return $valid;
    }
}
