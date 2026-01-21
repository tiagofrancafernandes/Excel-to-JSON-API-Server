<?php

namespace App\Excel;

use Spatie\SimpleExcel\SimpleExcelReader;

class Reader
{
    public static function experimentalMode(): bool
    {
        return request_any_get('experimental_mode') === 'TRUE';
    }

    public static function response(array $options = []): void
    {
        try {
            $experimentalMode = static::experimentalMode();
            $delimiter = null;

            $allowedTypes = [
                'csv',
                'xlsx',
                'ods',
            ];

            if ($experimentalMode) {
                $allowedTypes[] = 'xls';
                $allowedTypes[] = 'tsv';
            }

            $type = request_any_get('type', 'csv');
            $type = in_array($type, $allowedTypes) ? $type : null;

            if (!$type) {
                response_as_json(['error' => 'Invalid Type', ...static::helpMessage()], 422);

                exit((int) 422);
            }

            if ($type === 'csv') {
                $delimiter = filter_var($options['delimiter'] ?? null, FILTER_DEFAULT, FILTER_NULL_ON_FAILURE) ?: null;
                $delimiter ??= filter_var(request_any_get('delimiter', null), FILTER_DEFAULT, FILTER_NULL_ON_FAILURE) ?: null;
            }

            $delimiter = match ($delimiter) {
                '', null, 'null', 'NULL' => null,
                'TAB', '\t' => "\t",
                'SPACE', ' ' => ' ',
                'COMMA', ',' => ',',
                'SEMICOLON', ';' => ';',
                'PIPE', '|' => '|',
                'DOUBLE-PIPE', '||' => '||',
                'DOUBLE-SEMICOLON', ';;' => ';;',
                'DOUBLE-COMMA', ',,' => ',,',
                default => null,
            };

            if ($type === 'tsv') {
                $type = 'csv';
                $delimiter = "\t";
            }

            $source = request_any_get('source') ?: '';
            $source = filter_var($source, FILTER_VALIDATE_URL) ?: filter_var(urldecode($source), FILTER_VALIDATE_URL);

            if (!$source) {
                response_as_json(['error' => 'Invalid URL', ...static::helpMessage()], 422);

                exit((int) 422);
            }

            $sourceMD5 = md5($source);
            $sourceLocalPath = temp_path('excel-db-' . $sourceMD5 . '.' . $type);

            $toDeleteFile = !is_to_cache() && is_file($sourceLocalPath);

            if ($toDeleteFile) {
                unlink($sourceLocalPath);
            }

            if (!is_file($sourceLocalPath)) {
                file_put_contents($sourceLocalPath, file_get_contents($source));
            }

            if (!is_file($sourceLocalPath) || !filesize($sourceLocalPath)) {
                response_as_json(['error' => 'Invalid file', ...static::helpMessage()], 404);

                exit((int) 404);
            }

            $headersToSnakeCase = filter_var(request_any_get('headersToSnakeCase', false), FILTER_VALIDATE_BOOL);
            $filterBy = request_any_get('filterBy');
            $filterValue = request_any_get('filterValue');
            $filterOperator = request_any_get('filterOperator', 'search');

            $filters = request_any_get('filters');
            $filters = array_filter(is_array($filters) ? $filters : [], fn($filter) => is_array($filter));

            if (filled($filterBy) && (filled($filterValue) || filled($filterOperator))) {
                $filters[] = [
                    'key' => $filterBy,
                    'value' => $filterValue,
                    'operator' => $filterOperator,
                ];
            }

            $fromSheetName = $type != 'csv' ? request_any_get('fromSheetName') : null;
            $fromSheet = $type != 'csv' ? filter_var(request_any_get('fromSheet'), FILTER_VALIDATE_INT) : null;
            $rows = $type === 'csv' && $delimiter
                ? SimpleExcelReader::create($sourceLocalPath, $type)->useDelimiter($delimiter)
                : SimpleExcelReader::create($sourceLocalPath, $type);

            if ($fromSheetName) {
                $rows = $rows->fromSheetName($fromSheetName);
            }

            if (!$fromSheetName && $fromSheet) {
                $rows = $rows->fromSheet($fromSheet);
            }

            if ($headersToSnakeCase) {
                $rows = $rows->headersToSnakeCase();
            }

            $rows = $rows->getRows();

            if ($filters) {
                $rows = $rows->filter(function (array $rowProperties) use ($filters) {
                    foreach ($filters as $_filter) {
                        if (!is_array($_filter) || !$_filter) {
                            continue;
                        }

                        $_filterKey = $_filter['key'] ?? $_filter['by'] ?? null;
                        $filterValue = $_filter['value'] ?? null;
                        $filterOperator = $_filter['operator'] ?? null;

                        if (is_null($_filterKey)) {
                            continue;
                        }

                        if (is_null($filterOperator) || $filterOperator === '') {
                            $filterOperator = 'equal';
                        }

                        $result = match ($filterOperator) {
                            '=', 'equal' => ($rowProperties[$_filterKey] ?? null) == $filterValue,
                            '!=', 'notequal', 'notEqual', 'ne' => ($rowProperties[$_filterKey] ?? null) != $filterValue,
                            '>', 'gt' => ($rowProperties[$_filterKey] ?? null) > $filterValue,
                            '>=', 'ge' => ($rowProperties[$_filterKey] ?? null) >= $filterValue,
                            '<', 'lt' => ($rowProperties[$_filterKey] ?? null) < $filterValue,
                            '<=', 'le' => ($rowProperties[$_filterKey] ?? null) <= $filterValue,
                            'filled', 'notEmpty' => filled($rowProperties[$_filterKey] ?? null),
                            'contains', 'like' => str_contains(strval($rowProperties[$_filterKey] ?? null), strval($filterValue)),
                            '*', 'search', 'ilike' => str_contains(
                                strtolower(strval($rowProperties[$_filterKey] ?? null)),
                                strtolower(strval($filterValue))
                            ),
                            default => ($rowProperties[$_filterKey] ?? null) == $filterValue,
                        };

                        if (!$result) {
                            return false;
                        }
                    }

                    return true;
                });
            }

            $rows = $rows ? $rows?->values()?->toArray() : [];

            response_as_json([
                'data' => $rows,
                'count' => count((array) $rows),
                'filters' => $filters,
            ]);

            die();
        } catch (\Throwable $th) {
            throw $th;

            app_abort(500);

            exit((int) 500);
        }
    }

    public static function helpMessage(): array
    {
        $repoUrl = 'https://github.com/tiagofrancafernandes/Excel-to-JSON-API-Server';

        return [
            'help' => [
                'doc' => "For help, read the doc in {$repoUrl}?tab=readme-ov-file#readme",
                'repoUrl' => $repoUrl,
            ]
        ];
    }
}
