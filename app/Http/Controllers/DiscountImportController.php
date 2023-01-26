<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountImportRequest;
use App\Models\ImportDiscount;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class DiscountImportController extends BaseController
{
    public function index(): Factory|View|Application
    {
        return view('discount-import');
    }
    public function import(DiscountImportRequest $request): RedirectResponse
    {
        Excel::import(new ImportDiscount, $request->file('discount'));
        notify()->success('Success - Discount updated!');
        return redirect()->route('ecommerce.discount-import.index');
    }
}
