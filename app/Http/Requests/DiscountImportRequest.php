<?php

namespace App\Http\Requests;

use Botble\Support\Http\Requests\Request;
use JetBrains\PhpStorm\ArrayShape;

class DiscountImportRequest extends Request
{
    #[ArrayShape(['discount' => "file"])] public function rules(): array
    {
        return [
            'discount' => 'required|file',
        ];
    }
}
