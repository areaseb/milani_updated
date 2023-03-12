<?php

namespace Botble\Ecommerce\Imports;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Maatwebsite\Excel\Validators\Failure;

class ValidateProductImport extends ProductImport
{
    /**
     * @param array $row
     *
     * @return ProductVariation|null
     */
    public function model(array $row): ?ProductVariation
    {
        $importType = $this->getImportType();

        $name = $this->request->input('name');

        if (in_array($importType, ['products', 'all']) && $row['import_type'] == 'product') {
            // Storing a product is always successful
            return $this->storeProduct();

        } else if (in_array($importType, ['variations', 'all']) && $row['import_type'] == 'variation') {

            // If we are storing a variant we need to be sure
            // that the parent product exists
            if (!$this->getProductData($name)) {
                return $this->fails();
            }

            return null;
        }

        // We cannot understand what we are importing
        // so we fail
        $this->fails();
    }

    /**
     * @return null
     */
    public function storeProduct(): ?Product
    {
        $product = collect($this->request->all());
        $collect = collect([
            'name' => $product['name'],
            'import_type' => 'product',
            'model' => $product,
        ]);

        $this->onSuccess($collect);

        return null;
    }

    protected function getProductData($name)
    {
        $collection = $this->successes()
            ->where('import_type', 'product')
            ->where('name', $name)
            ->first();

        if (!($collection['model'] ?? false)) {
            return Product::where('name', $name)->first();
        }

        return $collection['model'];
    }

    protected function fails()
    {
        if (method_exists($this, 'onFailure')) {
            $failures[] = new Failure(
                $this->rowCurrent,
                'Product Name',
                [__('Product name ":name" does not exists', ['name' => $this->request->input('name')])],
                []
            );

            $this->onFailure(...$failures);

            return null;
        }
    }
}
