<?php

namespace App\Helpers;

class Env
{
    protected array $rawEnv = [];
    protected array $env = [];

    public function __construct(
        ?string $baseDir = null,
        string $envFile = '.env',
        array $initialData = [],
    ) {
        $this->env = static::defaultEnvValues();
        $this->loadSystemEnv();
        $this->loadFromFile($baseDir, $envFile);
        $this->prepareEnv();
        $this->prepareInitialData($initialData);
    }

    protected static function defaultEnvValues(): array
    {
        return [
            'APP_ENV' => 'production',
            'APP_DEBUG' => false,
            'APP_KEY' => null,
            'APP_URL' => null,
        ];
    }

    protected function loadFromFile(?string $baseDir = null, string $envFile = '.env'): void
    {
        $baseDir = $baseDir ?: (defined('BASE_PATH') ? constant('BASE_PATH') : null);

        $filePath = rtrim("{$baseDir}", '/') . '/' . $envFile;

        if (!is_file($filePath)) {
            return;
        }

        $fileContents = explode(PHP_EOL, trim(file_get_contents($filePath)));

        foreach ($fileContents as $line) {
            $line = static::validateLine($line);

            if (!$line) {
                continue;
            }

            $key = $line['key'] ?? null;
            $value = static::mutateValue($line['value'] ?? null);

            if (!$key) {
                continue;
            }

            $this->rawEnv[$key] = $value;
        }
    }

    protected function loadSystemEnv(): void
    {
        foreach (getenv() as $key => $value) {
            $this->rawEnv[$key] = $value;
        }
    }

    protected function prepareInitialData(array $initialData): void
    {
        foreach ($initialData as $key => $value) {
            if (!$key || !is_string($key) || !trim($key)) {
                continue;
            }

            $this->rawEnv[$key] = $value;
        }
    }

    public static function validateLine(?string $line): ?array
    {
        if (
            !$line
            || !trim($line)
            || str_starts_with($line, ' ')
            || is_numeric((trim($line)[0] ?? 1) ?? null)
            || !str_contains($line, '=')
        ) {
            return null;
        }

        $checkAny = function (?string $str, callable $callable, array|string ...$values) {
            $values = is_array($values[0] ?? null) ? array_filter(
                $values[0],
                fn($item) => $item && is_string($item)
            ) : $values;

            if (!$values) {
                return false;
            }

            foreach ($values as $toCheck) {
                if (call_user_func($callable, "{$str}", $toCheck)) {
                    return true;
                }
            }

            return false;
        };

        if ($checkAny($line, 'str_starts_with', ['#', '=', '"', "'"])) {
            return null;
        }

        if (!$checkAny($line, 'str_contains', ['='])) {
            return null;
        }

        $line = trim($line);
        $line = explode('=', $line, 2);
        $key = $line[0] ?? null;

        if (!$key || !trim($key) || str_contains($key, ' ')) {
            return null;
        }

        $value = $line[1] ?? null;

        if (
            $checkAny("{$value}", 'str_starts_with', ['"', "'"])
            && $checkAny("{$value}", 'str_ends_with', ['"', "'"])
        ) {
            $toTrim = str_starts_with("{$value}", '"') && str_ends_with("{$value}", '"') ? '"' : "'";
            $value = trim($value, $toTrim);
        }

        return [
            'key' => $key,
            'value' => $value,
        ];
    }

    public function getRawEnv(): array
    {
        return $this->rawEnv ?? [];
    }

    public function getEnv(?string $key = null, mixed $default = null): mixed
    {
        $env = $this->env ?? [];

        if (is_null($key)) {
            return $env;
        }

        return $env[$key] ?? $default;
    }

    protected function prepareEnv(): void
    {
        foreach ($this->getRawEnv() as $key => $value) {
            // TODO: validate type of data
            $value = static::mutateValue($value);

            $this->env[$key] = $value;
            $_ENV[$key] = $value;
        }
    }

    public static function mutateValue(mixed $value): mixed
    {
        return match ($value) {
            'False', 'false', 'FALSE', false => false,
            'True', 'true', 'TRUE', true => true,
            '', 'null', 'NULL', null => null,
            default => $value,
        };
    }

    public static function init(
        ?string $baseDir = null,
        string $envFile = '.env',
        array $initialData = [],
    ): static {
        return new static($baseDir, $envFile, $initialData);
    }

    public static function get(?string $key = null, mixed $default = null): mixed
    {
        return static::init()->getEnv($key, $default);
    }
}
