<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;

class BannedListValidator extends AValidator
{
    public function validate(EmailAddress $email): bool
    {
        if ($this->policy->checkBannedListedEmail()) {
            $domain = $email->getDomain();
            foreach ($this->policy->getBannedList() as $bannedDomain) {
                if (fnmatch($bannedDomain, $domain ?? '')) {
                    return false;
                }
            }
        }
        return true;
    }
}
