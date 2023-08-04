<?php

namespace App\Jobs;

use App\Mail\BulkProductImportJobErrorEmail;
use App\Mail\BulkProductImportJobSuccessEmail;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class BulkProductImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const SUCCESS = 'success';
    protected const ERROR = 'error';

    protected $filePath = null;

    protected $importType = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath, $importType)
    {
        $this->filePath = $filePath;
        $this->importType = $importType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $productImport = app(ProductImport::class);
        // $validateProductImport = app(ValidateProductImport::class);

        // $validateProductImport
        //     ->setValidatorClass(new ProductRequest())
        //     ->import($this->filePath);

        // if ($validateProductImport->failures()->count()) {
        //     $data = [
        //         'total_failed' => $validateProductImport->failures()->count(),
        //         'total_error' => $validateProductImport->errors()->count(),
        //         'failures' => $validateProductImport->failures(),
        //     ];

        //     $message = trans('plugins/ecommerce::bulk-import.import_failed_description');

        //     return $this->sendEmail($data, $message, self::ERROR);
        // }

        $productImport
            ->setValidatorClass(new ProductRequest())
            ->setImportType($this->importType)
            ->import($this->filePath);

        $data = [
            'total_success' => $productImport->successes()->count(),
            'total_failed' => $productImport->failures()->count(),
            'total_error' => $productImport->errors()->count(),
            'failures' => $productImport->failures(),
            'successes' => $productImport->successes(),
        ];

        $message = trans('plugins/ecommerce::bulk-import.imported_successfully');

        $result = trans('plugins/ecommerce::bulk-import.results', [
            'success' => $data['total_success'],
            'failed' => $data['total_failed'],
        ]);

        return $this->sendEmail($data, $message . ' ' . $result, self::SUCCESS);
    }

    protected function sendEmail($data, $message, $status)
    {
        $class = $status === self::SUCCESS ? BulkProductImportJobSuccessEmail::class : BulkProductImportJobErrorEmail::class;

        Mail::to(config('bulk-import.email'))
            ->send(new $class($data, $message));
    }
}
