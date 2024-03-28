<?php

namespace Botble\Ecommerce\Http\Resources;

use Botble\Ecommerce\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProductVariationResource extends JsonResource
{
    /**
     * @param Request $request
     */
    public function toArray($request): array
    {
        $product = Product::find($this->id);

        $details = collect([
                'sku',
                'made_in',
                'larghezza_scatola_collo_1',
                'larghezza_scatola_collo_2',
                'larghezza_scatola_collo_3',
                'larghezza_scatola_collo_4',
                'larghezza_scatola_collo_5',
                'profondita_scatola_collo_1',
                'profondita_scatola_collo_2',
                'profondita_scatola_collo_3',
                'profondita_scatola_collo_4',
                'profondita_scatola_collo_5',
                'altezza_scatola_collo_1',
                'altezza_scatola_collo_2',
                'altezza_scatola_collo_3',
                'altezza_scatola_collo_4',
                'altezza_scatola_collo_5',
                'cubatura',
                'peso_con_imballo_collo_1',
                'peso_con_imballo_collo_2',
                'peso_con_imballo_collo_3',
                'peso_con_imballo_collo_4',
                'peso_con_imballo_collo_5',
                'assemblato',
                'kit_e_istruzioni_incluse'
            ])->map(function ($value) use ($product) {
                return [
                    'key' => $value,
                    'name' => ucfirst(str_replace('_', ' ', $value)),
                    'value' => $product->{$value},
                ];
            })->filter(function ($value) {
                return !empty($value['value']);
            })->map(function ($value) {
                $um = '';
                switch (explode('_', $value['key'])[0]){
                    case 'larghezza':
                    case 'profondita':
                    case 'altezza':
                        $um = 'cm';
                        break;
                    case 'cubatura':
                        $um = 'm3';
                        break;
                    case 'peso':
                        $um = 'kg';
                        break;
                    default:
                        $um = '';
                        break;
                }

                return [
                    'name' => $value['name'],
                    'value' => $value['value'] . ' ' . $um,
                ];
            })
            ->toArray();

        $attributes = $this->variationProductAttributes->map(function ($attribute) {
                return [
                    'name' => Str::endsWith($attribute->attribute_set_title, '1') ? Str::substr($attribute->attribute_set_title, 0, -2): $attribute->attribute_set_title,
                    'value' => $attribute->title,
                ];
            })->toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'slug' => $this->slug,
            'with_storehouse_management' => $this->with_storehouse_management,
            'quantity' => $this->quantity,
            'is_out_of_stock' => $this->isOutOfStock(),
            'stock_status_label' => $this->stock_status_label,
            'stock_status_html' => $this->stock_status_html,
            'price' => $this->price_with_taxes,
            'sale_price' => $this->front_sale_price_with_taxes,
            'original_price' => $this->original_price,
            'image_with_sizes' => $this->image_with_sizes,
            'display_price' => format_price($this->price_with_taxes),
            'display_sale_price' => format_price($this->front_sale_price_with_taxes),
            'sale_percentage' => get_sale_percentage($this->price, $this->front_sale_price),
            'unavailable_attribute_ids' => $this->unavailableAttributeIds,
            'success_message' => $this->successMessage,
            'error_message' => $this->errorMessage,
            'weight' => $this->weight,
            'height' => $this->height,
            'wide' => $this->wide,
            'length' => $this->length,

            'attributes' => $attributes,

            'details' => $details,

            'dimensions' => [
                [
                    'name' => __('Length'),
                    'value' => $product->length . ' cm',
                ],
                [
                    'name' => __('Wide'),
                    'value' => $product->wide . ' cm',
                ],
                [
                    'name' => __('Height'),
                    'value' => $product->height . ' cm',
                ],
                [
                    'name' => __('Weight'),
                    'value' => $product->weight . ' kg',
                ],
            ]
        ];
    }
}
