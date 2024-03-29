<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class ProductImageRetrievalService
{
    protected const CSV_URL = 'https://b2b.magazzinicosma.it/storage/exports/prodotti.csv';

    protected const SKU_KEY = 'SKU';
    protected const IMAGES_KEY = 'immagini';

    protected $reader = null;
    protected $tmpfile = null;

    public function getImages($sku): Collection
    {
        if (!$sku) {
            return collect();
        }

        $images = $this->getImagesFromLocalDisk($sku);
        if ($images->isEmpty()) {
            $row = $this->getProductRow($sku);
            $images = $row[self::IMAGES_KEY] ?? '';
            $images = collect(explode(',', $images))
                ->map(fn ($image) => trim($image));
        }

        return $images;
    }

    protected function getProductRow($sku): array
    {
        if (!$sku) {
            return [];
        }

        $reader = $this->getReader();
        $records = (new Statement())
            ->where(fn ($row) => $row[self::SKU_KEY] === $sku)
            ->process($reader);

        return $records->first();
    }

    protected function getReader()
    {
        if (!$this->reader) {
            $this->reader = $this->createReader();
        }

        return $this->reader;
    }

    protected function createReader(): Reader
    {
        $reader = Reader::createFromPath($this->downloadCSV(), 'r');
        $reader->setHeaderOffset(0);
        $reader->setDelimiter(';');

        return $reader;
    }

    protected function downloadCSV(): string
    {
        $this->tmpfile = tmpfile();
        $filename = stream_get_meta_data($this->tmpfile)['uri'];

        $contents = file_get_contents(self::CSV_URL);
        file_put_contents($filename, $contents);

        return $filename;
    }

    protected function getImagesFromLocalDisk($sku): Collection
    {
        $path = config('filesystems.disks.gcs.root');
        $files = glob("$path/$sku*");

        $images = collect($files)
            ->map(fn ($file) => basename($file))
            ->map(fn ($file) => Storage::disk('gcs')->url($file));

        return $images;
    }
}
