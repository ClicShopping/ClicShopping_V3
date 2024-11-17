<?php

function fixture(string $name): array
{
    $content = file_get_contents(__DIR__."/Fixtures/$name.json");

    if (! $content) {
        throw new InvalidArgumentException(
            "Cannot find fixture: [$name] at Fixtures/$name.json",
        );
    }

    return json_decode($content, true);
}
