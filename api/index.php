<?php

require_once __DIR__ . '/../core/bootstrap.php';

// $rows = SimpleExcelReader::create($pathToXlsx)
//     ->fromSheet(3)
//     ->getRows();
// ```

// With multiple spreadsheets, you can too select the sheet you want to use with the `fromSheetName()` method to select by name.

// ```php
// $rows = SimpleExcelReader::create($pathToXlsx)
//     ->fromSheetName("sheet1")
//     ->getRows();
// ```

// if you want to check if a sheet exists, you can use the `sheetExists()` method.

// ```php
// $sheetExists = SimpleExcelReader::create($pathToXlsx)
//     ->sheetExists("sheet1");
