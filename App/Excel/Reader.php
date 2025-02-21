<?php

namespace App\Excel;

use Spatie\SimpleExcel\SimpleExcelReader;

class Reader
{
    public static function response(array $options = []): void
    {
        try {
            $type = request_any_get('type', 'csv');
            $type = in_array($type, [
                'csv',
                'xlsx',
                'ods',
            ]) ? $type : null;

            if (!$type) {
                response_as_json(['error' => 'Invalid Type', ...static::helpMessage()], 422);

                exit((int) 422);
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

            $fromSheetName = $type != 'csv' ? request_any_get('fromSheetName') : null;
            $fromSheet = $type != 'csv' ? filter_var(request_any_get('fromSheet'), FILTER_VALIDATE_INT) : null;
            $rows = SimpleExcelReader::create($sourceLocalPath, $type);

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

            if ($filterBy) {
                $rows = $rows->filter(fn (array $rowProperties) => match ($filterOperator) {
                    '=', 'equal' => ($rowProperties[$filterBy] ?? null) == $filterValue,
                    '!=', 'notequal', 'notEqual' => ($rowProperties[$filterBy] ?? null) != $filterValue,
                    '>', 'gt' => ($rowProperties[$filterBy] ?? null) > $filterValue,
                    '>=', 'ge' => ($rowProperties[$filterBy] ?? null) >= $filterValue,
                    '<', 'lt' => ($rowProperties[$filterBy] ?? null) < $filterValue,
                    '<=', 'le' => ($rowProperties[$filterBy] ?? null) <= $filterValue,
                    'contains', 'like' => str_contains(strval($rowProperties[$filterBy] ?? null), strval($filterValue)),
                    '*', 'search', 'ilike' => str_contains(
                        strtolower(strval($rowProperties[$filterBy] ?? null)),
                        strtolower(strval($filterValue))
                    ),
                    default => ($rowProperties[$filterBy] ?? null) == $filterValue,
                });
            }

            $rows = $rows ? $rows?->values()?->toArray() : [];

            response_as_json([
                'data' => $rows,
                'count' => count((array) $rows),
                'filters' => [
                    'filterBy' => $filterBy,
                    'filterValue' => $filterValue,
                    'filterOperator' => $filterOperator,
                ],
            ]);

            die();
        } catch (\Throwable $th) {
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
