<?php
namespace App\Exports\Payroll;

ini_set('memory_limit', '-1');

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PayrollRecapExport implements WithMultipleSheets
{
    protected array $data;
    public function __construct($data)
    {
        $this->data  = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->data['units'] as $key => $value){
            $sheets[] = new PayrollRecapSheet($value, [
                'data' => $this->data['data'][$key],
                'headerTitle' => $this->data['headerTitle'],
                'headerSubtitle' => $this->data['headerSubtitle'],
                'additional_title' => $value,
            ]);
        }

        return $sheets;
    }
}
