<?php

namespace Botble\Ecommerce\Http\Controllers;

use App\Jobs\BulkProductImportJob;
use Assets;
use BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Slug\Models\Slug;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class BulkImportController extends BaseController
{
    protected ProductImport $productImport;

    protected ProductImport|ValidateProductImport $validateProductImport;

    public function __construct(ProductImport $productImport, ValidateProductImport $validateProductImport)
    {
        $this->productImport = $productImport;
        $this->validateProductImport = $validateProductImport;
    }

    public function delete()
    {
        $productRepository = app(ProductInterface::class);
        Product::chunk(200, function ($products) use ($productRepository) {
            foreach ($products as $product) {
                $productRepository->delete($product);
            }
        });

        // $categoryRepository = app(ProductCategoryInterface::class);
        // ProductCategory::chunk(200, function ($category) use ($categoryRepository) {
        //     foreach ($category as $category) {
        //         $categoryRepository->delete($category);
        //     }
        // });

        Slug::where('reference_type', Product::class)->delete();
        // Slug::where('reference_type', ProductCategory::class)->delete();

        return redirect()->back();
    }

    public function index()
    {
        page_title()->setTitle(trans('plugins/ecommerce::bulk-import.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/ecommerce/js/bulk-import.js']);

        $template = new TemplateProductExport('xlsx');
        $headings = $template->headings();
        $data = $template->collection();
        $rules = $template->rules();

        return view('plugins/ecommerce::bulk-import.index', compact('data', 'headings', 'rules'));
    }

    public function postImport(BulkImportRequest $request, BaseHttpResponse $response)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $file = $request->file('file');
        $filePath = $file->store('bulk-import');

        dispatch(new BulkProductImportJob((string) $filePath, (string) $request->input('type')));

        // $this->validateProductImport
        //     ->setValidatorClass(new ProductRequest())
        //     ->import($file);

        // if ($this->validateProductImport->failures()->count()) {
        //     $data = [
        //         'total_failed' => $this->validateProductImport->failures()->count(),
        //         'total_error' => $this->validateProductImport->errors()->count(),
        //         'failures' => $this->validateProductImport->failures(),
        //     ];

        //     $message = trans('plugins/ecommerce::bulk-import.import_failed_description');

        //     return $response
        //         ->setError()
        //         ->setData($data)
        //         ->setMessage($message);
        // }

        // $this->productImport
        //     ->setValidatorClass(new ProductRequest())
        //     ->setImportType($request->input('type'))
        //     ->import($file);

        // $data = [
        //     'total_success' => $this->productImport->successes()->count(),
        //     'total_failed' => $this->productImport->failures()->count(),
        //     'total_error' => $this->productImport->errors()->count(),
        //     'failures' => $this->productImport->failures(),
        //     'successes' => $this->productImport->successes(),
        // ];

        // $message = trans('plugins/ecommerce::bulk-import.imported_successfully');

        // $result = trans('plugins/ecommerce::bulk-import.results', [
        //     'success' => $data['total_success'],
        //     'failed' => $data['total_failed'],
        // ]);

        // return $response->setData($data)->setMessage($message . ' ' . $result);

        $data = [
            'total_success' => 0,
            'total_failed' => 0,
            'total_error' => 0,
            'failures' => 0,
            'successes' => 0,
        ];

        return $response->setData($data)->setMessage('Product import started successfully. You will receive an email when the import is completed.');
    }

    public function downloadTemplate(Request $request)
    {
        $extension = $request->input('extension');
        $extension = $extension == 'csv' ? $extension : Excel::XLSX;
        $writeType = $extension == 'csv' ? Excel::CSV : Excel::XLSX;
        $contentType = $extension == 'csv' ? ['Content-Type' => 'text/csv'] : ['Content-Type' => 'text/xlsx'];
        $fileName = 'template_products_import.' . $extension;

        return (new TemplateProductExport($extension))->download($fileName, $writeType, $contentType);
    }
}
