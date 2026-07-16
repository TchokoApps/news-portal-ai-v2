<?php

use App\Models\Language;

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

if (! function_exists('set_language')) {
    function set_language(string $languageCode): void
    {
        session()->put('language', $languageCode);
    }
}

if (! function_exists('default_language')) {
    function default_language(): ?string
    {
        return Language::query()
            ->where('status', true)
            ->where('default', true)
            ->orderBy('id')
            ->value('lang')
            ?? Language::query()
                ->where('status', true)
                ->orderByDesc('default')
                ->orderBy('name')
                ->value('lang');
    }
}

if (! function_exists('current_language')) {
    function current_language(): string
    {
        $sessionCode = session('language');

        if (filled($sessionCode)) {
            $activeExists = Language::query()
                ->where('lang', $sessionCode)
                ->where('status', true)
                ->exists();

            if ($activeExists) {
                return $sessionCode;
            }
        }

        $resolvedCode = default_language() ?? config('app.fallback_locale', 'en');

        set_language($resolvedCode);

        return $resolvedCode;
    }
}
