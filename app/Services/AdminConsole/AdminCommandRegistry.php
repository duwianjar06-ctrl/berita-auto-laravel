<?php

namespace App\Services\AdminConsole;

use InvalidArgumentException;

class AdminCommandRegistry
{
    public static function all(): array
    {
        return [
            'berita:publish' => ['label' => 'Publish Once', 'description' => 'Publish one article', 'mutating' => true, 'options' => []],
            'berita:cycle' => ['label' => 'News Cycle', 'description' => 'Run news cycle', 'mutating' => true, 'options' => []],
            'berita:publish-scheduled' => ['label' => 'Publish Scheduled', 'description' => 'Publish due scheduled articles', 'mutating' => true, 'options' => []],
            'schedule:list' => ['label' => 'Schedule List', 'description' => 'List scheduled tasks', 'mutating' => false, 'options' => []],
            'about' => ['label' => 'About', 'description' => 'Application information', 'mutating' => false, 'options' => []],
            'optimize:clear' => ['label' => 'Clear Cache', 'description' => 'Clear Laravel caches', 'mutating' => true, 'options' => []],
            'berita:debug-rejected' => ['label' => 'Debug Rejected', 'description' => 'Show rejected articles', 'mutating' => false, 'options' => ['limit' => ['type' => 'integer', 'min' => 1, 'max' => 100, 'default' => 20]]],
            'berita:reset-safety-rejected' => ['label' => 'Reset Safety Rejected', 'description' => 'Reset only overlap/entity rejections', 'mutating' => true, 'confirm' => true, 'options' => []],
            'berita:gemini-health' => ['label' => 'Gemini Health', 'description' => 'Check Gemini endpoint', 'mutating' => false, 'options' => []],
            'berita:pipeline-status' => ['label' => 'Pipeline Status', 'description' => 'Show article pipeline counts', 'mutating' => false, 'options' => []],
        ];
    }

    public static function get(string $name): array
    {
        $registry = self::all();

        if (! isset($registry[$name])) {
            throw new InvalidArgumentException('Command tidak diizinkan.');
        }

        return $registry[$name] + ['key' => $name];
    }

    public static function parse(string $input): array
    {
        $input = trim($input);

        if ($input === '') {
            throw new InvalidArgumentException('Command tidak diizinkan.');
        }

        $forbiddenCharacters = [
            ';',
            '&',
            '|',
            '<',
            '>',
            '`',
            '\\',
            "\r",
            "\n",
        ];

        foreach ($forbiddenCharacters as $character) {
            if (str_contains($input, $character)) {
                throw new InvalidArgumentException('Command tidak diizinkan.');
            }
        }

        if (str_contains($input, '$(')) {
            throw new InvalidArgumentException('Command tidak diizinkan.');
        }

        $parts = str_getcsv($input, ' ', '"', '\\');
        $name = array_shift($parts);
        $spec = self::get($name);
        $params = [];
        $args = [];

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if (! str_starts_with($part, '--')) {
                throw new InvalidArgumentException('Argument tidak diizinkan.');
            }

            [$key, $value] = array_pad(explode('=', substr($part, 2), 2), 2, null);

            if ($value === null || ! isset($spec['options'][$key])) {
                throw new InvalidArgumentException('Option --'.$key.' tidak diizinkan untuk command ini.');
            }

            $option = $spec['options'][$key];

            if ($option['type'] === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                throw new InvalidArgumentException('Nilai --'.$key.' harus integer.');
            }

            $value = $option['type'] === 'integer' ? (int) $value : $value;

            if (isset($option['min']) && ($value < $option['min'] || $value > $option['max'])) {
                throw new InvalidArgumentException('Nilai --'.$key.' harus antara '.$option['min'].' dan '.$option['max'].'.');
            }

            $params[$key] = $value;
            $args[$key] = $value;
        }

        foreach ($spec['options'] as $key => $option) {
            if (! array_key_exists($key, $params) && array_key_exists('default', $option)) {
                $params[$key] = $option['default'];
                $args[$key] = $option['default'];
            }
        }

        return [$name, $args, $params, $spec];
    }
}
