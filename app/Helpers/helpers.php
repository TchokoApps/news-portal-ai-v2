<?php

if (! function_exists('format_tags')) {
    /**
     * Convert iterable tags into comma-separated names.
     */
    function format_tags(iterable $tags): string
    {
        return collect($tags)
            ->map(function (mixed $tag): ?string {
                if (is_string($tag)) {
                    return trim($tag);
                }

                return data_get($tag, 'name');
            })
            ->filter(fn (?string $tag): bool => ! empty($tag))
            ->implode(',');
    }
}
