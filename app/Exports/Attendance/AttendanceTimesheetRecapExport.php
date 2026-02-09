<?php
namespace App\Exports\Attendance;

ini_set('memory_limit', '-1');

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AttendanceTimesheetRecapExport implements WithMultipleSheets
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
            $sheets[] = new AttendanceTimesheetRecapSheet($value, [
                'data' => $this->data['data'][$key],
                'headerTitle' => $this->data['headerTitle'],
                'headerSubtitle' => $this->data['headerSubtitle'],
                'totalDays' => $this->data['totalDays'],
            ]);
        }

        return $sheets;
    }
}
