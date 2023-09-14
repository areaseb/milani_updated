<?php

namespace Botble\Ecommerce\Imports;

use App\Services\ProductImageRetrievalService;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Enums\StockStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductLabelInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Services\Products\StoreProductService;
use Botble\Ecommerce\Services\Products\UpdateDefaultProductService;
use Botble\Ecommerce\Services\StoreProductTagService;
use Botble\Media\Facades\RvMediaFacade;
use Botble\Media\Models\MediaFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Mimey\MimeTypes;
use SlugHelper;

class ProductImport implements
    ToModel,
    WithHeadingRow,
    WithMapping,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError,
    WithChunkReading
{
    use Importable;
    use SkipsFailures;
    use SkipsErrors;
    use ImportTrait;

    /**
     * @var ProductInterface
     */
    protected ProductInterface $productRepository;

    /**
     * @var ProductCategoryInterface
     */
    protected ProductCategoryInterface $productCategoryRepository;

    /**
     * @var ProductTagInterface
     */
    protected ProductTagInterface $productTagRepository;

    /**
     * @var ProductLabelInterface
     */
    protected ProductLabelInterface $productLabelRepository;

    /**
     * @var TaxInterface
     */
    protected TaxInterface $taxRepository;

    /**
     * @var ProductCollectionInterface
     */
    protected ProductCollectionInterface $productCollectionRepository;

    /**
     * @var ProductAttributeInterface
     */
    protected ProductAttributeInterface $productAttributeRepository;

    /**
     * @var ProductVariationInterface
     */
    protected ProductVariationInterface $productVariationRepository;

    /**
     * @var BrandInterface
     */
    protected BrandInterface $brandRepository;

    /**
     * @var StoreProductTagService
     */
    protected StoreProductTagService $storeProductTagService;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var mixed
     */
    protected mixed $validatorClass;

    /**
     * @var Collection
     */
    protected Collection $brands;

    /**
     * @var Collection
     */
    protected Collection $categories;

    /**
     * @var Collection
     */
    protected Collection $tags;

    /**
     * @var Collection
     */
    protected Collection $taxes;

    /**
     * @var Collection
     */
    protected Collection $stores;

    /**
     * @var Collection
     */
    protected Collection $labels;

    /**
     * @var Collection
     */
    protected Collection $productCollections;

    /**
     * @var Collection
     */
    protected Collection $productLabels;

    /**
     * @var string
     */
    protected string $importType = 'all';

    /**
     * @var Collection
     */
    protected Collection $productAttributeSets;

    /**
     * @var int
     */
    protected int $rowCurrent = 1; // include header

    /**
     * @var ProductAttributeSetInterface
     */
    protected ProductAttributeSetInterface $productAttributeSetRepository;

    /**
     * @var ProductImageRetrievalService
     */
    protected ProductImageRetrievalService $productImageRetrievalService;

    /**
     * @param ProductInterface $productRepository
     * @param ProductCategoryInterface $productCategoryRepository
     * @param ProductTagInterface $productTagRepository
     * @param ProductLabelInterface $productLabelRepository
     * @param TaxInterface $taxRepository
     * @param ProductCollectionInterface $productCollectionRepository
     * @param ProductAttributeSetInterface $productAttributeSetRepository
     * @param ProductAttributeInterface $productAttributeRepository
     * @param ProductVariationInterface $productVariationRepository
     * @param BrandInterface $brandRepository
     * @param StoreProductTagService $storeProductTagService
     * @param Request $request
     */
    public function __construct(
        ProductInterface             $productRepository,
        ProductCategoryInterface     $productCategoryRepository,
        ProductTagInterface          $productTagRepository,
        ProductLabelInterface        $productLabelRepository,
        TaxInterface                 $taxRepository,
        ProductCollectionInterface   $productCollectionRepository,
        ProductAttributeSetInterface $productAttributeSetRepository,
        ProductAttributeInterface    $productAttributeRepository,
        ProductVariationInterface    $productVariationRepository,
        BrandInterface               $brandRepository,
        StoreProductTagService       $storeProductTagService,
        Request                      $request
    ) {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productTagRepository = $productTagRepository;
        $this->productLabelRepository = $productLabelRepository;
        $this->taxRepository = $taxRepository;
        $this->productCollectionRepository = $productCollectionRepository;
        $this->storeProductTagService = $storeProductTagService;
        $this->brandRepository = $brandRepository;
        $this->productAttributeSetRepository = $productAttributeSetRepository;

        $this->request = $request;
        $this->categories = collect();
        $this->brands = collect();
        $this->taxes = collect();
        $this->labels = collect();
        $this->productCollections = collect();
        $this->productLabels = collect();

        $this->productAttributeRepository = $productAttributeRepository;
        $this->productVariationRepository = $productVariationRepository;

        if (is_plugin_active('marketplace')) {
            $this->stores = collect();
        }

        $this->productImageRetrievalService = app(ProductImageRetrievalService::class);
    }

    /**
     * @param string $importType
     * @return self
     */
    public function setImportType(string $importType): ProductImport
    {
        $this->importType = $importType;

        return $this;
    }

    /**
     * @return string
     */
    public function getImportType(): string
    {
        return $this->importType;
    }

    /**
     * @param array $row
     *
     * @return Product|ProductVariation
     * @throws Exception
     */
    public function model(array $row)
    {
        $importType = $this->getImportType();

        $name = $this->request->input('name');
        $slug = $this->request->input('slug');

        if (in_array($importType, ['products', 'all']) && $row['import_type'] == 'product') {
            return $this->storeProduct();

        } else if (in_array($importType, ['variations', 'all']) && $row['import_type'] == 'variation') {
            $product = $this->getProduct($name, $slug);

            return $this->storeVariant($product);
        }

        if ($importType == 'all') {
            $productExist = Product::where('sku', $row['sku'])->firstOrFail();
            return (new UpdateDefaultProductService())->execute($productExist, $row);
        }

        return $this->storeProduct();
    }

    /**
     * @param string $name
     * @param string|null $slug
     * @return \Eloquent|Builder|Model|object|null
     */
    protected function getProduct(string $name, ?string $slug)
    {
        if ($slug) {
            $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Product::class), Product::class);

            if ($slug) {
                return $this->productRepository->getFirstBy([
                    'id'           => $slug->reference_id,
                    'is_variation' => 0,
                ]);
            }
        }

        return $this->productRepository
            ->getModel()
            ->where(function ($query) use ($name) {
                $query
                    ->where('name', $name)
                    ->orWhere('id', $name);
            })
            ->where('is_variation', 0)
            ->first();
    }

    /**
     * @return Product|null
     * @throws Exception
     */
    public function storeProduct(): ?Product
    {
        $product = $this->productRepository->getModel();

        if ($description = $this->request->input('description')) {
            $this->request->merge(['description' => BaseHelper::clean($description)]);
        }

        if ($content = $this->request->input('content')) {
            $this->request->merge(['content' => BaseHelper::clean($content)]);
        }

        $product = (new StoreProductService($this->productRepository))->execute($this->request, $product);

        $tagsInput = (array) $this->request->input('tags', []);
        if ($tagsInput) {
            $tags = [];
            foreach ($tagsInput as $tag) {
                $tags[] = ['value' => $tag];
            }
            $this->request->merge(['tag' => json_encode($tags)]);
            $this->storeProductTagService->execute($this->request, $product);
        }

        $attributeSets = $this->request->input('attribute_sets', []);

        if ($attributeSets) {
            $product->productAttributeSets()->sync($attributeSets);
        }

        $collect = collect([
            'name'           => $product->name,
            'slug'           => $this->request->input('slug'),
            'import_type'    => 'product',
            'attribute_sets' => $attributeSets,
            'model'          => $product,
        ]);

        $this->onSuccess($collect);

        return $product;
    }

    protected function prepareProductImages($images, $sku)
    {
        // Retrive old product images
        $oldProductImages = MediaFile::whereIn('note', $images)->get();
        $oldProdictImagesUrl = $oldProductImages->map(fn ($image) => $image->note);

        // Let's download new images
        $downloadedImagesUrls = $images
            ->filter(fn ($url) => !$oldProdictImagesUrl->contains($url))
            ->map(fn ($url) => $this->downloadImageFromURL($url, $sku))
            ->filter();

        // Let's filter out images that need to be deleted
        $oldImagesToKeepUrls = $oldProductImages->filter(function ($image) use ($images) {
                $keep = $images->contains($image->note);
                if (!$keep) {
                    $image->delete();
                }

                return $keep;
            })
            ->map(fn ($image) => $image->url);

        return collect($oldImagesToKeepUrls->toArray())->merge($downloadedImagesUrls)->toArray();
    }

    /**
     * @param string|null $url
     * @return string|null
     */
    protected function downloadImageFromURL(?string $url, string $name): ?string
    {
        if (empty($url)) {
            return null;
        }

        try {
            $contents = file_get_contents($url);
        } catch (Exception $exception) {
            return null;
        }

        if (empty($contents)) {
            return null;
        }

        $path = '/tmp';

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755);
        }

        $path = $path . '/' . basename($url);

        file_put_contents($path, $contents);

        $mimeType = (new MimeTypes())->getMimeType(File::extension($path));

        $fileUpload = new UploadedFile($path, $name . '.' . File::extension($path), $mimeType, null, true);

        $result = RvMediaFacade::handleUpload($fileUpload, 0, 'products', false, $url);

        File::delete($path);

        if (!$result['error']) {
            $url = $result['data']->url;
        }

        return $url;
    }

    /**
     * @param $product
     * @return ProductVariation|null
     */
    public function storeVariant($product): ?ProductVariation
    {
        if (!$product) {
            if (method_exists($this, 'onFailure')) {
                $failures[] = new Failure(
                    $this->rowCurrent,
                    'Product Name',
                    [__('Product name ":name" does not exists', ['name' => $this->request->input('name')])],
                    []
                );
                $this->onFailure(...$failures);
            }

            return null;
        }

        $addedAttributes = $this->request->input('product_attributes', []);

        $result = $this->productVariationRepository->getVariationByAttributesOrCreate($product->id, $addedAttributes);

        if (!$result['created']) {
            if (method_exists($this, 'onFailure')) {
                $failures[] = new Failure(
                    $this->rowCurrent,
                    'variation',
                    [trans('plugins/ecommerce::products.form.variation_existed') . ' ' . trans('plugins/ecommerce::products.form.product_id') . ': ' . $product->id],
                    []
                );
                $this->onFailure(...$failures);
            }

            return null;
        }

        $variation = $result['variation'];

        $version = array_merge($variation->toArray(), $this->request->toArray());
        $version['variation_default_id'] = Arr::get($version, 'is_variation_default') ? $version['id'] : null;
        $version['attribute_sets'] = $addedAttributes;

        if ($version['description']) {
            $version['description'] = BaseHelper::clean($version['description']);
        }

        if ($version['content']) {
            $version['content'] = BaseHelper::clean($version['content']);
        }

        $productRelatedToVariation = $this->productRepository->getModel();
        $productRelatedToVariation->fill($version);

        $productRelatedToVariation->name = $product->name;
        $productRelatedToVariation->status = $product->status;
        $productRelatedToVariation->brand_id = $product->brand_id;
        $productRelatedToVariation->is_variation = 1;

        $productRelatedToVariation->sku = Arr::get($version, 'sku');
        if (!$productRelatedToVariation->sku && Arr::get($version, 'auto_generate_sku')) {
            $productRelatedToVariation->sku = $product->sku;
            foreach ($version['attribute_sets'] as $setId => $attributeId) {
                $attributeSet = $this->productAttributeSets->firstWhere('id', $setId);
                if ($attributeSet) {
                    $attribute = $attributeSet->attributes->firstWhere('id', $attributeId);
                    if ($attribute) {
                        $productRelatedToVariation->sku .= '-' . Str::upper($attribute->slug);
                    }
                }
            }
        }

        $productRelatedToVariation->price = Arr::get($version, 'price', $product->price);
        $productRelatedToVariation->sale_price = Arr::get($version, 'sale_price', $product->sale_price);

        if (Arr::get($version, 'description')) {
            $productRelatedToVariation->description = BaseHelper::clean($version['description']);
        }

        if (Arr::get($version, 'content')) {
            $productRelatedToVariation->content = BaseHelper::clean($version['content']);
        }

        $productRelatedToVariation->length = Arr::get($version, 'length', $product->length);
        $productRelatedToVariation->wide = Arr::get($version, 'wide', $product->wide);
        $productRelatedToVariation->height = Arr::get($version, 'height', $product->height);
        $productRelatedToVariation->weight = Arr::get($version, 'weight', $product->weight);

        $productRelatedToVariation->with_storehouse_management = Arr::get(
            $version,
            'with_storehouse_management',
            $product->with_storehouse_management
        );
        $productRelatedToVariation->stock_status = Arr::get(
            $version,
            'stock_status',
            StockStatusEnum::IN_STOCK
        );
        $productRelatedToVariation->quantity = Arr::get($version, 'quantity', $product->quantity);
        $productRelatedToVariation->allow_checkout_when_out_of_stock = Arr::get(
            $version,
            'allow_checkout_when_out_of_stock',
            $product->allow_checkout_when_out_of_stock
        );

        $productRelatedToVariation->sale_type = (int)Arr::get($version, 'sale_type', $product->sale_type);

        if ($productRelatedToVariation->sale_type == 0) {
            $productRelatedToVariation->start_date = null;
            $productRelatedToVariation->end_date = null;
        } else {
            $productRelatedToVariation->start_date = Carbon::parse(Arr::get($version, 'start_date', $product->start_date))->toDateTimeString();
            $productRelatedToVariation->end_date = Carbon::parse(Arr::get($version, 'end_date', $product->end_date))->toDateTimeString();
        }

        $productRelatedToVariation->images = json_encode(Arr::get($version, 'images', []) ?: []);

        $productRelatedToVariation->status = Arr::get($version, 'status', $product->status);

        $productRelatedToVariation->product_type = $product->product_type;

        $productRelatedToVariation = $this->productRepository->createOrUpdate($productRelatedToVariation);

        event(new CreatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $this->request, $productRelatedToVariation));

        $variation->product_id = $productRelatedToVariation->id;

        $variation->is_default = Arr::get($version, 'variation_default_id', 0) == $variation->id;

        $this->productVariationRepository->createOrUpdate($variation);

        if ($version['attribute_sets']) {
            $variation->productAttributes()->sync($version['attribute_sets']);
        }

        return $variation;
    }

    /**
     * Change value before insert to model
     *
     * @param array $row
     */
    public function map($row): array
    {
        ++$this->rowCurrent;
        $row = $this->mapLocalization($row);
        $row = $this->setCategoriesToRow($row);
        $row = $this->setBrandToRow($row);
        $row = $this->setTaxToRow($row);
        $row = $this->setProductCollectionsToRow($row);
        $row = $this->setProductLabelsToRow($row);
        $row = $this->setProductImagesToRow($row);
        $row = $this->setProductDescriptions($row);

        if (is_plugin_active('marketplace')) {
            $row = $this->setStoreToRow($row);
        }

        $this->request->merge($row);

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function setProductImagesToRow(array $row): array
    {
        $codiceCosma = $row['codice_cosma'];

        $row['images'] = $this->prepareProductImages(
            $this->productImageRetrievalService->getImages($codiceCosma),
            $row['sku']
        );

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function setTaxToRow(array $row): array
    {
        $row['tax_id'] = 0;

        if (!empty($row['tax'])) {
            $row['tax'] = trim($row['tax']);

            $tax = $this->taxes->firstWhere('keyword', $row['tax']);
            if ($tax) {
                $taxId = $tax['tax_id'];
            } else {
                if (is_numeric($row['tax'])) {
                    $tax = $this->taxRepository->findById($row['tax']);
                } else {
                    $tax = $this->taxRepository->getFirstBy(['title' => $row['tax']]);
                }

                $taxId = $tax ? $tax->id : 0;
                $this->taxes->push([
                    'keyword' => $row['tax'],
                    'tax_id'  => $taxId,
                ]);
            }

            $row['tax_id'] = $taxId;
        }

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function setStoreToRow(array $row): array
    {
        $row['store_id'] = 0;

        if (!empty($row['vendor'])) {
            $row['vendor'] = trim($row['vendor']);

            $store = $this->stores->firstWhere('keyword', $row['vendor']);
            if ($store) {
                $storeId = $store['store_id'];
            } else {
                $storeRepository = app(StoreInterface::class);

                if (is_numeric($row['vendor'])) {
                    $store = $storeRepository->findById($row['vendor']);
                } else {
                    $store = $storeRepository->getFirstBy(['name' => $row['vendor']]);
                }

                $storeId = $store ? $store->id : 0;
                $this->stores->push([
                    'keyword'  => $row['vendor'],
                    'store_id' => $storeId,
                ]);
            }

            $row['store_id'] = $storeId;
        }

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function setBrandToRow(array $row): array
    {
        $row['brand_id'] = 0;

        if (!empty($row['brand'])) {
            $row['brand'] = trim($row['brand']);

            $brand = $this->brands->firstWhere('keyword', $row['brand']);
            if ($brand) {
                $brandId = $brand['brand_id'];
            } else {
                if (is_numeric($row['brand'])) {
                    $brand = $this->brandRepository->findById($row['brand']);
                } else {
                    $brand = $this->brandRepository->getFirstBy(['name' => $row['brand']]);
                }

                $brandId = $brand ? $brand->id : 0;
                $this->brands->push([
                    'keyword'  => $row['brand'],
                    'brand_id' => $brandId,
                ]);
            }

            $row['brand_id'] = $brandId;
        }

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function setCategoriesToRow(array $row): array
    {
        if ($row['categories']) {
            $categories = $row['categories'];
            $categoryIds = [];
            foreach ($categories as $value) {
                $value = trim($value);

                $category = $this->categories->firstWhere('keyword', $value);
                if ($category) {
                    $categoryId = $category['category_id'];
                } else {
                    if (is_numeric($value)) {
                        $category = $this->productCategoryRepository->findById($value);
                    } else {
                        $category = $this->productCategoryRepository->getFirstBy(['name' => $value]);
                    }

                    if (!$category) {
                        $category = $this->productCategoryRepository->create([
                            'name' => $value,
                        ]);
                    }
                    $categoryId = $category ? $category->id : 0;

                    $this->categories->push([
                        'keyword'     => $value,
                        'category_id' => $categoryId,
                    ]);
                }
                $categoryIds[] = $categoryId;
            }

            $row['categories'] = array_filter($categoryIds);
        }

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function setProductCollectionsToRow(array $row): array
    {
        if ($row['product_collections']) {
            $productCollections = $row['product_collections'];
            $collectionIds = [];
            foreach ($productCollections as $value) {
                $value = trim($value);

                $collection = $this->productCollections->firstWhere('keyword', $value);
                if ($collection) {
                    $collectionId = $collection['collection_id'];
                } else {
                    if (is_numeric($value)) {
                        $collection = $this->productCollectionRepository->findById($value);
                    } else {
                        $collection = $this->productCollectionRepository->getFirstBy(['name' => $value]);
                    }

                    $collectionId = $collection ? $collection->id : 0;
                    $this->productCollections->push([
                        'keyword'       => $value,
                        'collection_id' => $collectionId,
                    ]);
                }
                $collectionIds[] = $collectionId;
            }

            $row['product_collections'] = array_filter($collectionIds);
        }

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function setProductLabelsToRow(array $row): array
    {
        if ($row['product_labels']) {
            $productLabels = $row['product_labels'];
            $productLabelIds = [];
            foreach ($productLabels as $value) {
                $value = trim($value);

                $productLabel = $this->productLabels->firstWhere('keyword', $value);
                if ($productLabel) {
                    $productLabelId = $productLabel['product_label_id'];
                } else {
                    if (is_numeric($value)) {
                        $productLabel = $this->productLabelRepository->findById($value);
                    } else {
                        $productLabel = $this->productLabelRepository->getFirstBy(['name' => $value]);
                    }

                    $productLabelId = $productLabel ? $productLabel->id : 0;
                    $this->productLabels->push([
                        'keyword'          => $value,
                        'product_label_id' => $productLabelId,
                    ]);
                }
                $productLabelIds[] = $productLabelId;
            }

            $row['product_labels'] = array_filter($productLabelIds);
        }

        return $row;
    }

    public function setProductDescriptions($row)
    {
        $row['content'] = $row['description'];
        $row['description'] = $row['short_description'];

        return $row;
    }

    /**
     * @param array $row
     * @return array
     */
    public function mapLocalization(array $row): array
    {

        $row['stock_status'] = (string)Arr::get($row, 'stock_status');
        if (!in_array($row['stock_status'], StockStatusEnum::values())) {
            $row['stock_status'] = StockStatusEnum::IN_STOCK;
        }

        $row['status'] = Arr::get($row, 'status');
        if (!in_array($row['status'], BaseStatusEnum::values())) {
            $row['status'] = BaseStatusEnum::PENDING;
        }

        $row['product_type'] = Arr::get($row, 'product_type');
        if (!in_array($row['product_type'], ProductTypeEnum::values())) {
            $row['product_type'] = ProductTypeEnum::PHYSICAL;
        }

        $row['import_type'] = Arr::get($row, 'import_type');
        if ($row['import_type'] != 'variation') {
            $row['import_type'] = 'product';
        }

        $row['name'] = Arr::get($row, 'product_name');
        $row['is_slug_editable'] = true;

        $this->setValues($row, [
            ['key' => 'slug', 'type' => 'string', 'default' => 'name'],
            ['key' => 'sku', 'type' => 'string'],
            ['key' => 'price', 'type' => 'number'],
            ['key' => 'weight', 'type' => 'number'],
            ['key' => 'length', 'type' => 'number'],
            ['key' => 'wide', 'type' => 'number'],
            ['key' => 'height', 'type' => 'number'],
            ['key' => 'is_featured', 'type' => 'bool'],
            ['key' => 'product_labels'],
            ['key' => 'labels'],
            ['key' => 'images'],
            ['key' => 'categories'],
            ['key' => 'product_collections'],
            ['key' => 'product_attributes'],
            ['key' => 'is_variation_default', 'type' => 'bool'],
            ['key' => 'auto_generate_sku', 'type' => 'bool'],
            ['key' => 'with_storehouse_management', 'type' => 'bool'],
            ['key' => 'allow_checkout_when_out_of_stock', 'type' => 'bool'],
            ['key' => 'quantity', 'type' => 'number'],
            ['key' => 'sale_price', 'type' => 'number'],
            ['key' => 'start_date', 'type' => 'datetime', 'from' => 'start_date_sale_price'],
            ['key' => 'end_date', 'type' => 'datetime', 'from' => 'end_date_sale_price'],
            ['key' => 'tags'],
        ]);

        $row['product_labels'] = $row['labels'];

        if ($row['import_type'] == 'product' && !$row['sku'] && $row['auto_generate_sku']) {
            $row['sku'] = Str::upper(Str::random(7));
        }

        $row['sale_type'] = 0;
        if ($row['start_date'] || $row['end_date']) {
            $row['sale_type'] = 1;
        }

        if (!$row['with_storehouse_management']) {
            $row['quantity'] = null;
            $row['allow_checkout_when_out_of_stock'] = false;
        }

        $attributeSets = [];
        foreach (Arr::get($row, 'product_attributes') as $key => $value) {
            $valueExploded = explode(':', $value);
            $key = trim(Arr::get($valueExploded, 0));
            $value = trim(Arr::get($valueExploded, 1, ''));

            $attributeSets[$key] = $value;
        }

        $row['attribute_sets'] = [];
        $row['product_attributes'] = [];

        if ($row['import_type'] == 'variation') {
            foreach ($attributeSets as $title => $value) {
                if (!$title || !$value) {
                    continue;
                }

                $attributeSet = $this->productAttributeSetRepository
                    ->firstOrCreate(['title' => $title], [
                        'slug' => Str::slug($title),
                        'status' => 'published',
                        'order' => $this->productAttributeSetRepository->getModel()->max('order') + 1,
                        'display_layout' => 'text',
                        'is_searchable' => 1,
                        'is_comparable' => 1,
                        'is_use_in_product_listing' => 1,
                        'use_image_from_product_variation' => 0,
                    ]);

                $attribute = $attributeSet->attributes()
                    ->firstOrCreate(['title' => $value], [
                        'slug' => Str::slug($value),
                        'color' => null,
                        'status' => 'published',
                        'order' => $attributeSet->attributes()->max('order') + 1,
                        'image' => null,
                        'is_default' => 0,
                    ]);

                $row['product_attributes'][$attributeSet->id] = $attribute->id;
            }
        }

        if ($row['import_type'] == 'product') {
            foreach ($attributeSets as $title => $value) {
                $attributeSet = $this->productAttributeSetRepository
                    ->firstOrCreate(['title' => $title], [
                        'slug' => Str::slug($title),
                        'status' => 'published',
                        'order' => $this->productAttributeSetRepository->getModel()->max('order') + 1,
                        'display_layout' => 'text',
                        'is_searchable' => 1,
                        'is_comparable' => 1,
                        'is_use_in_product_listing' => 1,
                        'use_image_from_product_variation' => 0,
                    ]);

                $row['attribute_sets'][] = $attributeSet->id;
            }
        }

        // Let's fix the sku_set attribute
        if ($row['sku_set'] ?? false) {
            // If there is written "tempesta", then the quantity is 99
            if (Str::contains($row['sku_set'], 'tempesta')) {
                $row['quantity'] = 99;
            }
        }

        return $row;
    }

    /**
     * @param array $row
     * @param array $attributes
     * @return $this
     */
    protected function setValues(array &$row, array $attributes = []): ProductImport
    {
        foreach ($attributes as $attribute) {
            $this->setValue(
                $row,
                Arr::get($attribute, 'key'),
                Arr::get($attribute, 'type', 'array'),
                Arr::get($attribute, 'default'),
                Arr::get($attribute, 'from')
            );
        }

        return $this;
    }

    /**
     * @param array $row
     * @param string $key
     * @param string $type
     * @param $default
     * @param $from
     * @return $this
     */
    protected function setValue(array &$row, string $key, string $type = 'array', $default = null, $from = null): ProductImport
    {
        $value = Arr::get($row, $from ?: $key, $default);

        switch ($type) {
            case 'array':
                $value = $value ? explode(',', $value) : [];
                break;
            case 'bool':
                if (Str::lower($value) == 'false' || $value == '0' || Str::lower($value) == 'no') {
                    $value = false;
                }
                $value = (bool)$value;
                break;
            case 'datetime':
                if ($value) {
                    if (in_array(gettype($value), ['integer', 'double'])) {
                        $value = $this->transformDate($value);
                    } else {
                        $value = $this->getDate($value);
                    }
                }
                break;
        }

        Arr::set($row, $key, $value);

        return $this;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return method_exists($this->getValidatorClass(), 'rules') ? $this->getValidatorClass()->rules() : [];
    }

    /**
     * @return mixed
     */
    public function getValidatorClass()
    {
        return $this->validatorClass;
    }

    /**
     * @param mixed $validatorClass
     * @return self
     */
    public function setValidatorClass($validatorClass): self
    {
        $this->validatorClass = $validatorClass;

        return $this;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
