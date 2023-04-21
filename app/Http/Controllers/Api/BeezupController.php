<?php

namespace App\Http\Controllers\Api;

use BaseHelper;
use App\Http\Controllers\Controller;
use Botble\Ecommerce\Exports\CsvProductExport;
use Maatwebsite\Excel\Excel;

class BeezupController extends Controller
{
    public function catalog()
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        return (new CsvProductExport())->download('export_products.csv', Excel::CSV, ['Content-Type' => 'text/csv']);
    }
}
