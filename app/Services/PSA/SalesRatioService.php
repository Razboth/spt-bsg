<?php
namespace App\Services\PSA;

use App\Http\Controllers\Controller;
use App\Models\PSA\SalesRatio;
use App\Models\PSA\SettingPositionProduct;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesRatioService extends Controller
{
    private AgentService $agentService;
    private SettingPositionProductService $settingPositionProductService;

    public function __construct()
    {
        $this->agentService                  = new AgentService();
        $this->settingPositionProductService = new SettingPositionProductService();
    }

    public function getSalesRatios($columns = []): Builder
    {
        $query = SalesRatio::query();

        if (count($columns) > 0) {
            $query->select($columns);
        }

        return $query;
    }

    public function getSalesRatioById(string $id): SalesRatio
    {
        return SalesRatio::find($id);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request, $settingId): JsonResponse | null
    {
        if (! $request->ajax()) {
            return null;
        }

        $date          = $request->get('filter_1') ?? date('d/m/Y');
        $formattedDate = resetDate($date);

        // Get base query for agents and customers
        $settingProduct = SettingPositionProduct::find($settingId);
        $arrPositions   = explode(",", $settingProduct->positions);
        $query          = $this->agentService->getAgent(isSalesOnly: true)
            ->with(['unit'])
            ->whereIn('position_id', $arrPositions)
            ->select(['agents.*', 'customers.name as customer_name', 'customers.id as customer_id'])
            ->join('customers', 'agents.id', '=', 'customers.agent_id')
            ->when(Auth::user()->role->name == 'Manager', function ($query) {
                $query->where('leader_id', Auth::user()->model_id);
            })
            ->when(Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', Auth::user()->model->unit_id);
            })
            ->when(Auth::user()->role->name == 'Sales', function ($query) {
                $query->where('agents.id', Auth::user()->model->id);
            });

        // Filter function
        $filter      = $request->get('search')['value'];
        $queryFilter = function ($query) use ($filter) {
            if (isset($filter)) {
                $query->where('agents.name', 'like', '%' . $filter . '%');
            }
        };

        // Base columns
        $arrColumn = $this->getBaseColumns($date);

        // Get position product setting
        $setting = $this->settingPositionProductService->getSettingPositionProductById($settingId);

        // Get all customer IDs that will be displayed
        $customerIds = $query->clone()->pluck('customer_id')->toArray();

        // Collect all product IDs from settings
        $productIds = $setting->details->pluck('product_id')->toArray();

        // Preload all sales ratio data in two efficient queries
        $previousRatios = $this->preloadPreviousSalesRatios($customerIds, $productIds, $formattedDate);
        $currentRatios  = $this->preloadCurrentSalesRatios($customerIds, $productIds, $formattedDate);

        // Add product columns
        foreach ($setting->details as $detail) {
            $productId = $detail->product_id;

            // Add columns for each product
            $arrColumn[] = $this->createBalanceColumn($productId, $previousRatios);
            $arrColumn[] = $this->createCoreColumn($productId, $currentRatios);
            $arrColumn[] = $this->createDeltaColumn($productId, $previousRatios, $currentRatios);
        }

        // Calculate the total balance for all products
        $arrColumn[] = ['name' => 'balance_total', 'type' => 'custom', 'value' => function ($row) use ($productIds, $previousRatios) {
            $total = 0;
            foreach ($productIds as $productId) {
                $value = $previousRatios[$row->customer_id][$productId] ?? 0;
                $total += floatval($value);
            }
            return setCurrency($total);
        }];

        // Calculate the total core for all products
        $arrColumn[] = ['name' => 'core_total', 'type' => 'custom', 'value' => function ($row) use ($productIds, $currentRatios) {
            $total = 0;
            foreach ($productIds as $productId) {
                $value = $currentRatios[$row->customer_id][$productId] ?? 0;
                $total += floatval($value);
            }
            return setCurrency($total);
        }];

        // Calculate the total delta for all products
        $arrColumn[] = ['name' => 'delta_total', 'type' => 'custom', 'value' => function ($row) use ($productIds, $previousRatios, $currentRatios) {
            $total = 0;
            foreach ($productIds as $productId) {
                $balance = $previousRatios[$row->customer_id][$productId] ?? 0;
                $nominal = $currentRatios[$row->customer_id][$productId] ?? 0;

                $delta = (floatval($balance) > 0 && floatval($nominal) > 0) ? floatval($nominal) - floatval($balance) : 0;
                $total += $delta;
            }
            return setCurrency($total);
        }];

        return generateDatatable($query, $queryFilter, $arrColumn, editable: false, deleteable: false);
    }

    public function dataDailyAverage(Request $request, $settingId): JsonResponse | null
    {
        if (! $request->ajax()) {
            return null;
        }

        $date          = $request->get('filter_1') ?? date('d/m/Y');
        $formattedDate = resetDate($date);

        // Get base query for agents and customers
        $settingProduct = SettingPositionProduct::find($settingId);
        $arrPositions   = explode(",", $settingProduct->positions);
        $query          = $this->agentService->getAgent(isSalesOnly: true)
            ->with(['unit'])
            ->whereIn('position_id', $arrPositions)
            ->select(['agents.*', 'customers.name as customer_name', 'customers.id as customer_id'])
            ->join('customers', 'agents.id', '=', 'customers.agent_id')
            ->when(Auth::user()->role->name == 'Manager', function ($query) {
                $query->where('leader_id', Auth::user()->model_id);
            })
            ->when(Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', Auth::user()->model->unit_id);
            })
            ->when(Auth::user()->role->name == 'Sales', function ($query) {
                $query->where('agents.id', Auth::user()->model->id);
            });

        // Filter function
        $filter      = $request->get('search')['value'];
        $queryFilter = function ($query) use ($filter) {
            if (isset($filter)) {
                $query->where('agents.name', 'like', '%' . $filter . '%');
            }
        };

        // Base columns
        $arrColumn = $this->getBaseColumns($date);

        // Get position product setting
        $setting = $this->settingPositionProductService->getSettingPositionProductById($settingId);

        // Get all customer IDs that will be displayed
        $customerIds = $query->clone()->pluck('customer_id')->toArray();

        // Collect all product IDs from settings
        $productIds = $setting->details->pluck('product_id')->toArray();

        // Preload all sales ratio data in two efficient queries
        $previousRatios = $this->preloadPreviousSalesRatios($customerIds, $productIds, $formattedDate);
        $currentRatios  = $this->preloadCurrentSalesRatios($customerIds, $productIds, $formattedDate);

        // Add product columns
        foreach ($setting->details as $detail) {
            $productId = $detail->product_id;

            // Add columns for each product
            $arrColumn[] = $this->createBalanceColumn($productId, $previousRatios);
            $arrColumn[] = $this->createCoreColumn($productId, $currentRatios);
            $arrColumn[] = $this->createAverageColumn($productId, $previousRatios, $currentRatios);
        }

        // Calculate the total balance for all products
        $arrColumn[] = ['name' => 'balance_total', 'type' => 'custom', 'value' => function ($row) use ($productIds, $previousRatios) {
            $total = 0;
            foreach ($productIds as $productId) {
                $value = $previousRatios[$row->customer_id][$productId] ?? 0;
                $total += floatval($value);
            }
            return setCurrency($total);
        }];

        // Calculate the total core for all products
        $arrColumn[] = ['name' => 'core_total', 'type' => 'custom', 'value' => function ($row) use ($productIds, $currentRatios) {
            $total = 0;
            foreach ($productIds as $productId) {
                $value = $currentRatios[$row->customer_id][$productId] ?? 0;
                $total += floatval($value);
            }
            return setCurrency($total);
        }];

        // Calculate the total average for all products
        $arrColumn[] = ['name' => 'average_total', 'type' => 'custom', 'value' => function ($row) use ($productIds, $previousRatios, $currentRatios) {
            $total = 0;
            foreach ($productIds as $productId) {
                $balance = $previousRatios[$row->customer_id][$productId] ?? 0;
                $nominal = $currentRatios[$row->customer_id][$productId] ?? 0;

                $average = (floatval($balance) > 0 && floatval($nominal) > 0) ? ($nominal + $balance) / 2 : 0;
                $total += $average;
            }
            return setCurrency($total);
        }];

        return generateDatatable($query, $queryFilter, $arrColumn, editable: false, deleteable: false);
    }

    public function dataMonthly(Request $request, $settingId): JsonResponse | null
    {
        if (! $request->ajax()) {
            return null;
        }

        $month = $request->get('filter_1') ?? date('m');
        $year  = $request->get('filter_2') ?? date('Y');

        $lastDayOfTheMonth = date('t', strtotime($year . '-' . $month . '-01'));

        // Find the first work day (Monday-Friday) of the month
        $firstDay = null;
        for ($d = 1; $d <= $lastDayOfTheMonth; $d++) {
            $date      = date('Y-m-d', strtotime("$year-$month-$d"));
            $dayOfWeek = date('N', strtotime($date)); // 1 (Mon) - 7 (Sun)
            if ($dayOfWeek <= 5) {
                $firstDay = $date;
                break;
            }
        }

        // Find the last work day (Monday-Friday) of the month
        $lastDay = null;
        for ($d = $lastDayOfTheMonth; $d >= 1; $d--) {
            $date      = date('Y-m-d', strtotime("$year-$month-$d"));
            $dayOfWeek = date('N', strtotime($date)); // 1 (Mon) - 7 (Sun)
            if ($dayOfWeek <= 5) {
                $lastDay = $date;
                break;
            }
        }

        $settingProduct = SettingPositionProduct::find($settingId);
        $arrPositions   = explode(",", $settingProduct->positions);
        $query          = $this->agentService->getAgent(isSalesOnly: true)
            ->with(['unit'])
            ->whereIn('position_id', $arrPositions)
            ->select(['agents.*', 'customers.name as customer_name', 'customers.id as customer_id'])
            ->join('customers', 'agents.id', '=', 'customers.agent_id')
            ->when(Auth::user()->role->name == 'Manager', function ($query) {
                $query->where('leader_id', Auth::user()->model_id);
            })
            ->when(Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', Auth::user()->model->unit_id);
            })
            ->when(Auth::user()->role->name == 'Sales', function ($query) {
                $query->where('agents.id', Auth::user()->model->id);
            });

        // Filter function
        $filter      = $request->get('search')['value'];
        $queryFilter = function ($query) use ($filter) {
            if (isset($filter)) {
                $query->where('name', 'like', '%' . $filter . '%');
            }
        };

        $setting = $this->settingPositionProductService->getSettingPositionProductById($settingId);

        $arrColumn = [
            ['name' => 'name', 'type' => 'custom', 'value' => function ($row) {
                return view('psa.change-customers.span-agent', [
                    'agent' => $row?->name,
                    'unit'  => $row?->unit?->name,
                ]);
            }],
        ];

        foreach ($setting->details as $detail) {
            $productId = $detail->product_id;

            $firstNominal = 0;
            $lastNominal  = 0;
            $arrColumn[]  = ['isAdd' => true, 'name' => 'balance_' . $productId, 'type' => 'custom', 'value' => function ($row) use ($productId, $firstDay, &$firstNominal) {
                $nominal = $this->getSalesRatios()
                    ->where('customer_id', $row->customer_id)
                    ->where('product_id', $productId)
                    ->where('date', $firstDay)
                    ->first()?->nominal ?? '0';

                $firstNominal = floatval($nominal);
                return setCurrency($nominal);
            }];

            $arrColumn[] = ['isAdd' => true, 'name' => 'core_' . $productId, 'type' => 'custom', 'value' => function ($row) use ($productId, $lastDay, &$lastNominal) {
                $nominal = $this->getSalesRatios()
                    ->where('customer_id', $row->customer_id)
                    ->where('product_id', $productId)
                    ->where('date', $lastDay)
                    ->first()?->nominal ?? '0';

                $lastNominal = floatval($nominal);
                return setCurrency($nominal);
            }];

            $arrColumn[] = ['isAdd' => true, 'name' => 'delta_' . $productId, 'type' => 'custom', 'value' => function ($row) use (&$firstNominal, &$lastNominal) {
                $delta = $lastNominal - $firstNominal;
                return setCurrency($delta);
            }];
        }

        $arrColumn[] = [
            'name' => 'balance_total', 'type' => 'custom', 'value' => function ($row) use ($firstDay, $setting) {
                $total = 0;
                foreach ($setting->details as $detail) {
                    $nominal = $this->getSalesRatios()
                        ->where('customer_id', $row->customer_id)
                        ->where('product_id', $detail->product_id)
                        ->where('date', $firstDay)
                        ->first()?->nominal ?? '0';
                    $total += floatval($nominal);
                }
                return setCurrency($total);
            },
        ];

        $arrColumn[] = [
            'name' => 'core_total', 'type' => 'custom', 'value' => function ($row) use ($lastDay, $setting) {
                $total = 0;
                foreach ($setting->details as $detail) {
                    $nominal = $this->getSalesRatios()
                        ->where('customer_id', $row->customer_id)
                        ->where('product_id', $detail->product_id)
                        ->where('date', $lastDay)
                        ->first()?->nominal ?? '0';
                    $total += floatval($nominal);
                }
                return setCurrency($total);
            },
        ];

        $arrColumn[] = [
            'name' => 'delta_total', 'type' => 'custom', 'value' => function ($row) use ($firstDay, $lastDay, $setting) {
                $firstTotal = 0;
                $lastTotal  = 0;

                foreach ($setting->details as $detail) {
                    $firstNominal = $this->getSalesRatios()
                        ->where('customer_id', $row->customer_id)
                        ->where('product_id', $detail->product_id)
                        ->where('date', $firstDay)
                        ->first()?->nominal ?? '0';

                    $lastNominal = $this->getSalesRatios()
                        ->where('customer_id', $row->customer_id)
                        ->where('product_id', $detail->product_id)
                        ->where('date', $lastDay)
                        ->first()?->nominal ?? '0';

                    $firstTotal += floatval($firstNominal);
                    $lastTotal += floatval($lastNominal);
                }

                return setCurrency($lastTotal - $firstTotal);
            },
        ];

        $arrColumn[] = [
            'name' => 'average_total', 'type' => 'custom', 'value' => function ($row) use ($firstDay, $lastDay, $setting) {
                $firstTotal = 0;
                $lastTotal  = 0;

                foreach ($setting->details as $detail) {
                    $firstNominal = $this->getSalesRatios()
                        ->where('customer_id', $row->customer_id)
                        ->where('product_id', $detail->product_id)
                        ->where('date', $firstDay)
                        ->first()?->nominal ?? '0';

                    $lastNominal = $this->getSalesRatios()
                        ->where('customer_id', $row->customer_id)
                        ->where('product_id', $detail->product_id)
                        ->where('date', $lastDay)
                        ->first()?->nominal ?? '0';

                    $firstTotal += floatval($firstNominal);
                    $lastTotal += floatval($lastNominal);
                }

                return setCurrency(($lastTotal + $firstTotal) / 2);
            },
        ];

        return generateDatatable($query, $queryFilter, $arrColumn, editable: false, deleteable: false);
    }

    public function dataAverage(Request $request, $settingId): JsonResponse | null
    {
        if (! $request->ajax()) {
            return null;
        }

        $month = $request->get('filter_1') ?? date('m');
        $year  = $request->get('filter_2') ?? date('Y');

        $settingProduct = SettingPositionProduct::find($settingId);
        $arrPositions   = explode(",", $settingProduct->positions);
        $query          = $this->agentService->getAgent(isSalesOnly: true)
            ->with(['unit'])
            ->whereIn('position_id', $arrPositions)
            ->select(['agents.*', 'customers.name as customer_name', 'customers.id as customer_id'])
            ->join('customers', 'agents.id', '=', 'customers.agent_id')
            ->when(Auth::user()->role->name == 'Manager', function ($query) {
                $query->where('leader_id', Auth::user()->model_id);
            })
            ->when(Auth::user()->role->name == 'Pemimpin Cabang', function ($query) {
                $query->where('unit_id', Auth::user()->model->unit_id);
            })
            ->when(Auth::user()->role->name == 'Sales', function ($query) {
                $query->where('agents.id', Auth::user()->model->id);
            });

        // Filter function
        $filter      = $request->get('search')['value'];
        $queryFilter = function ($query) use ($filter) {
            if (isset($filter)) {
                $query->where('name', 'like', '%' . $filter . '%');
            }
        };

        $setting = $this->settingPositionProductService->getSettingPositionProductById($settingId);

        $arrColumn = [
            ['name' => 'name', 'type' => 'custom', 'value' => function ($row) {
                return view('psa.change-customers.span-agent', [
                    'agent' => $row?->name,
                    'unit'  => $row?->unit?->name,
                ]);
            }],
        ];

        $arrAverage = [];
        foreach ($setting->details as $detail) {
            $productId = $detail->product_id;

            $arrColumn[] = ['isAdd' => true, 'name' => 'average_' . $productId, 'type' => 'custom', 'value' => function ($row) use ($productId, $month, $year, &$arrAverage) {
                $ratios = $this->getSalesRatios()
                    ->where('customer_id', $row->customer_id)
                    ->where('product_id', $productId)
                    ->where('agent_id', $row->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();
                $total = 0;
                foreach ($ratios as $ratio) {
                    $nominal = $ratio->nominal ?? '0';
                    $total += floatval($nominal);
                }

                $average = count($ratios) > 0 ? $total / count($ratios) : 0;

                $arrAverage[$row->id][$row->customer_id][$productId] = $average;
                return setCurrency($average);
            }];
        }

        $arrColumn[] = ['isAdd' => true, 'name' => 'average_total', 'type' => 'custom', 'value' => function ($row) use ($month, $year, $setting) {
            $totalAverage = 0;
            $totalExistProduct = 0;
            foreach ($setting->details as $detail) {
                $productId = $detail->product_id;
                $ratios    = $this->getSalesRatios()
                    ->where('customer_id', $row->customer_id)
                    ->where('product_id', $productId)
                    ->where('agent_id', $row->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();
                $total = 0;
                foreach ($ratios as $ratio) {
                    $nominal = $ratio->nominal ?? '0';
                    $total += floatval($nominal);
                }

                $average = count($ratios) > 0 ? $total / count($ratios) : 0;

                $totalAverage += $average;

                if ($average > 0) {
                    $totalExistProduct++;
                }
            }

            return setCurrency($totalAverage > 0 ? $totalAverage / $totalExistProduct : 0);

        }];

        return generateDatatable($query, $queryFilter, $arrColumn, editable: false, deleteable: false);
    }

    /**
     * Preload all previous sales ratios data in a single query
     */
    private function preloadPreviousSalesRatios(array $customerIds, array $productIds, string $date): array
    {
        $result = [];

        // Get the latest record before the selected date for each customer/product combination
        $latestRatios = $this->getSalesRatios()
            ->select('customer_id', 'product_id', 'nominal', 'date')
            ->whereIn('customer_id', $customerIds)
            ->whereIn('product_id', $productIds)
            ->where('date', '<', $date)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(['customer_id', 'product_id'])
            ->map(function ($customerGroup) {
                return $customerGroup->map(function ($productGroup) {
                    return $productGroup->first();
                });
            });

        // Transform to a simple lookup array
        foreach ($latestRatios as $customerId => $products) {
            foreach ($products as $productId => $ratio) {
                $result[$customerId][$productId] = $ratio->nominal ?? '0';
            }
        }

        return $result;
    }

    /**
     * Preload all current sales ratios data in a single query
     */
    private function preloadCurrentSalesRatios(array $customerIds, array $productIds, string $date): array
    {
        $result = [];

        // Get the record for the selected date for each customer/product combination
        $currentRatios = $this->getSalesRatios()
            ->select('customer_id', 'product_id', 'nominal')
            ->whereIn('customer_id', $customerIds)
            ->whereIn('product_id', $productIds)
            ->where('date', $date)
            ->get();

        // Transform to a simple lookup array
        foreach ($currentRatios as $ratio) {
            $result[$ratio->customer_id][$ratio->product_id] = $ratio->nominal ?? '0';
        }

        return $result;
    }

    /**
     * Get base columns for the datatable
     */
    private function getBaseColumns(string $date): array
    {
        return [
            ['name' => 'name', 'type' => 'custom', 'value' => function ($row) {
                return view('psa.change-customers.span-agent', [
                    'agent' => $row?->name,
                    'unit'  => $row?->unit?->name,
                ]);
            }],
            ['name' => 'edit', 'type' => 'custom', 'value' => function ($row) use ($date) {
                return view('components.views.action', [
                    'url_slot'    => route('psa.sales-ratio.edit', [
                        'agentId'    => $row->id,
                        'customerId' => $row->customer_id,
                        'date'       => resetDate($date),
                    ]),
                    'icon_slot'   => 'fa fa-edit',
                    'isModalSlot' => false,
                ]);
            }],
        ];
    }

    /**
     * Create balance column for product
     */
    private function createBalanceColumn(string $productId, array $previousRatios): array
    {
        return [
            'name'  => 'balance_' . $productId,
            'type'  => 'custom',
            'isAdd' => true,
            'value' => function ($row) use ($productId, $previousRatios) {
                $nominal = $previousRatios[$row->customer_id][$productId] ?? '0';
                return setCurrency($nominal);
            },
        ];
    }

    /**
     * Create core column for product
     */
    private function createCoreColumn(string $productId, array $currentRatios): array
    {
        return [
            'name'  => 'core_' . $productId,
            'type'  => 'custom',
            'isAdd' => true,
            'value' => function ($row) use ($productId, $currentRatios) {
                $nominal = $currentRatios[$row->customer_id][$productId] ?? '0';
                return setCurrency($nominal);
            },
        ];
    }

    /**
     * Create delta column for product
     */
    private function createDeltaColumn(string $productId, array $previousRatios, array $currentRatios): array
    {
        return [
            'name'  => 'delta_' . $productId,
            'type'  => 'custom',
            'isAdd' => true,
            'value' => function ($row) use ($productId, $previousRatios, $currentRatios) {
                $balance = $previousRatios[$row->customer_id][$productId] ?? '0';
                $nominal = $currentRatios[$row->customer_id][$productId] ?? '0';

                $delta = $balance > 0 && $nominal > 0 ? $nominal - $balance : 0;

                return setCurrency($delta);
            },
        ];
    }

    private function createAverageColumn(string $productId, array $previousRatios, array $currentRatios): array
    {
        return [
            'name'  => 'average_' . $productId,
            'type'  => 'custom',
            'isAdd' => true,
            'value' => function ($row) use ($productId, $previousRatios, $currentRatios) {
                $balance = $previousRatios[$row->customer_id][$productId] ?? '0';
                $nominal = $currentRatios[$row->customer_id][$productId] ?? '0';

                $average = $balance > 0 && $nominal > 0 ? ($nominal + $balance) / 2 : 0;

                return setCurrency($average);
            },
        ];
    }

    public function save(Request $request, string $agentId, string $customerId, string $date): void
    {
        foreach ($request->get('nominal') as $key => $value) {
            SalesRatio::updateOrCreate([
                'agent_id'    => $agentId,
                'customer_id' => $customerId,
                'date'        => $date,
                'product_id'  => $key,
            ], [
                'unit_id' => $request->get('unit_id'),
                'nominal' => resetCurrency($value),
            ]);
        }
    }

    public function delete(string $id): void
    {
        $agentBooster = SalesRatio::find($id);
        //delete position product
        $agentBooster?->delete();
    }

    public function updateApproval(Request $request, string $id): void
    {
        $fields = [
            'approved_by'     => $request->get('approved_by'),
            'approved_status' => $request->get('approved_status'),
            'approved_date'   => resetDate($request->get('approved_date')),
            'approved_note'   => $request->get('approved_note'),
        ];

        SalesRatio::find($id)->update($fields);
    }
}
