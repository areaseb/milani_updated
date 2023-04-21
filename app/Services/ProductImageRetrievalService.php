<?php

namespace App\Services;

use Illuminate\Support\Collection;
use League\Csv\Reader;
use League\Csv\Statement;

class ProductImageRetrievalService
{
    protected const CSV_URL = 'https://b2b.magazzinicosma.it/storage/exports/prodotti.csv';

    protected const SKU_KEY = 'SKU';
    protected const IMAGES_KEY = 'immagini';

    protected $reader = null;
    protected $tmpfile = null;

    public function getImages(string $sku): Collection
    {
        $row = $this->getProductRow($sku);
        $images = $row[self::IMAGES_KEY] ?? '';

        return collect(explode(',', $images))
            ->map(fn ($image) => trim($image));
    }

    protected function getProductRow(string $sku): array
    {
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
}
