<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

abstract class AProviderValidator extends AValidator
{
    /**
     * Gets public lists of disposable email address domains and merges them together into one array. If a custom
     * list is provided it is merged into the new list.
     *
     * @param bool $checkLocalOnly
     * @param array $list
     * @return array
     */
    public function getList(bool $checkLocalOnly = false, array $list = []): array
    {
        $providers = [];
        if (!$checkLocalOnly) {
            foreach (static::$providers as $provider) {
                if (filter_var($provider['url'], FILTER_VALIDATE_URL)) {
                    $content = @file_get_contents($provider['url']);
                    if ($content) {
                        $providers[] = $this->getExternalList($content, $provider['format']);
                    }
                }
            }
        }
        return array_filter(array_unique(array_merge($list, ...$providers)));
    }

    /**
     * Parses a list of disposable email address domains based on their format.
     *
     * @param string $content
     * @param string $type
     * @return array
     */
    protected function getExternalList(string $content, string $type): array
    {
        switch ($type) {
            case 'json':
                $providers = json_decode($content, true);
                break;
            case 'txt':
            default:
                $providers = explode("\n", str_replace("\r\n", "\n", $content));
                break;
        }
        return $providers;
    }
}
