<?php

namespace Botble\Ecommerce\Tables;

use App\Services\OrderExporterService;
use BaseHelper;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Table\Abstracts\TableAbstract;
use EcommerceHelper;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OrderHelper;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;
use Request;

class OrderTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, OrderInterface $orderRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $orderRepository;

        if (! Auth::user()->hasPermission('orders.edit')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return BaseHelper::clean($item->status->toHtml());
            })
/*            ->editColumn('payment_status', function ($item) {
                return $item->payment->status->label() ? BaseHelper::clean(
                    $item->payment->status->toHtml()
                ) : '&mdash;';
            })*/
            ->editColumn('payment_method', function ($item) {
                return BaseHelper::clean($item->payment->payment_channel->label() ?: '&mdash;');
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount);
            })
            ->editColumn('products', function ($item) {
                return $item->products->count();
            })
            ->editColumn('total_volume', function ($item) {
            	$cubatura = 0;
            	foreach($item->products as $product){
            		$cubatura += $product->product->cubatura;
            	}
                return $cubatura;
            })
            ->editColumn('total_weight', function ($item) {
                $weight = 0;
            	foreach($item->products as $product){
            		$weight += $product->product->weight;
            	}
                return $weight;
            })
            ->editColumn('address', function ($item) {
                return $item->address->address.'<br>'.$item->address->zip_code.' '.$item->address->city.'<br>Tel. '.$item->address->phone;
            })
/*            ->editColumn('shipping_amount', function ($item) {
                return format_price($item->shipping_amount);
            })*/
            ->editColumn('user_id', function ($item) {
                return BaseHelper::clean($item->user->name ?: $item->address->name);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('carrier', function ($item) {
                return match($item->carrier) {
                    '1' => 'Non definito',
                    '2' => 'SDA',
                    '3' => 'BRT',
                    default => 'Non definito',
                };
            });

/*        if (EcommerceHelper::isTaxEnabled()) {
            $data = $data->editColumn('tax_amount', function ($item) {
                return format_price($item->tax_amount);
            });
        }*/

        $data = $data
            ->addColumn('operations', function ($item) {
                return $this->getOperations('orders.edit', 'orders.destroy', $item);
            })
            ->filter(function ($query) {
                $keyword = $this->request->input('search.value');
                if ($keyword) {
                    return $query
                        ->whereHas('address', function ($subQuery) use ($keyword) {
                            return $subQuery->where('name', 'LIKE', '%' . $keyword . '%');
                        })
                        ->orWhereHas('user', function ($subQuery) use ($keyword) {
                            return $subQuery->where('name', 'LIKE', '%' . $keyword . '%');
                        })
                        ->orWhere('code', 'LIKE', '%' . $keyword . '%');
                }

                return $query;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->with(['user', 'payment'])
            ->select([
                'id',
                'source',
                'status',
                'user_id',
                'created_at',
                'amount',
                'tax_amount',
                'shipping_amount',
                'payment_id',
                'carrier',
            ])
            ->where('is_finished', 1);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-start',
            ],
            'source' => [
                'title' => trans('core/base::tables.source'),
                'class' => 'text-start',
            ],            
            'products' => [
                'title' => 'Prodotti',		//trans('core/base::tables.products'),
                'class' => 'text-center',
            ],
            'amount' => [
                'title' => trans('plugins/ecommerce::payment.amount'),
                'class' => 'text-center',
            ],
        ];

/*        if (EcommerceHelper::isTaxEnabled()) {
            $columns['tax_amount'] = [
                'title' => trans('plugins/ecommerce::order.tax_amount'),
                'class' => 'text-center',
            ];
        }*/

        $columns += [
/*            'shipping_amount' => [
                'title' => trans('plugins/ecommerce::order.shipping_amount'),
                'class' => 'text-center',
            ],*/
            'payment_method' => [
                'name' => 'payment_id',
                'title' => trans('plugins/ecommerce::order.payment_method'),
                'class' => 'text-start',
            ],  
            'carrier' => [
                'title' => trans('core/base::tables.carrier'),
                'class' => 'text-center',
            ],          
            'user_id' => [
                'title' => trans('plugins/ecommerce::order.customer_label'),
                'class' => 'text-start',
            ],       
            'address' => [
                'title' => trans('plugins/ecommerce::order.address'),
                'class' => 'text-start',
            ],    
            'total_weight' => [
                'title' => 'Peso tot.',		//trans('plugins/ecommerce::order.weight'),
                'class' => 'text-center',
            ],    
            'total_volume' => [
                'title' => 'Volume tot.',		//trans('plugins/ecommerce::order.volume'),
                'class' => 'text-center',
            ],
/*            'payment_status' => [
                'name' => 'payment_id',
                'title' => trans('plugins/ecommerce::order.payment_status_label'),
                'class' => 'text-center',
            ],*/
            'status' => [
                'title' => trans('core/base::tables.status'),
                'class' => 'text-center',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-start',
            ],
        ];

        return $columns;
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('orders.create'), 'orders.create');
        return $this->addImportButton(route('orders.import'), 'orders.create', $buttons);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('orders.deletes'), 'orders.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => OrderStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', OrderStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
            'carrier' => [
                'title' => trans('core/base::tables.carrier'),
                'type' => 'select',
                'choices' => [
                    1 => 'Non definito (usa i corrieri dei prodotti)',
                    2 => 'SDA',
                    3 => 'BRT',
                ],
                'validate' => 'required|in:' . implode(',', [1, 2, 3]),
            ],
        ];
    }

    public function renderTable($data = [], $mergeData = []): View|Factory|Response
    {
        if ($this->query()->count() === 0 &&
            ! $this->request()->wantsJson() &&
            $this->request()->input('filter_table_id') !== $this->getOption('id') && ! $this->request()->ajax()
        ) {
            return view('plugins/ecommerce::orders.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }

    public function saveBulkChangeItem(Model $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'status' && $inputValue == OrderStatusEnum::CANCELED) {
            if (! $item->canBeCanceledByAdmin()) {
                return $item;
            }

            OrderHelper::cancelOrder($item);

            app(OrderHistoryInterface::class)->createOrUpdate([
                'action' => 'cancel_order',
                'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
                'order_id' => $item->id,
                'user_id' => Auth::id(),
            ]);

            return $item;
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }

    protected function addImportButton(string $url, ?string $permission = null, array $buttons = []): array
    {
        $result = [];
        if (! $permission || Auth::user()->hasPermission($permission)) {
            $queryString = http_build_query(Request::query());

            if ($queryString) {
                $url .= '?' . $queryString;
            }

            $result['import'] = [
                'link' => $url,
                'text' => view('core/table::partials.import')->render(),
            ];
        }

        return [
            ...$result,
            ...$buttons,
        ];
    }

    public function saveBulkChanges(array $ids, string $inputKey, ?string $inputValue): bool
    {
        $result = parent::saveBulkChanges($ids, $inputKey, $inputValue);

        if ($inputKey == 'carrier') {
            $exporter = app(OrderExporterService::class);

            $orders = collect($ids)->map(function ($id) {
                return Order::find($id);
            });

            $exporter->forceUpdateBatch($orders, false);
        }

        return true;
    }
}
