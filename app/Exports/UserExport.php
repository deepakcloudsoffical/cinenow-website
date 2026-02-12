<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class UserExport implements FromCollection, WithHeadings, WithColumnWidths
{
    public array $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            // Capitalize each word and replace underscores with spaces
            $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = User::role('user');

        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {

                    case 'status':
                        $selectedData[$column] = 'Inactive';
                        if ($row[$column]) {
                            $selectedData[$column] = 'Active';
                        }
                        break;

                    case 'gender':
                        $selectedData[$column] = ucfirst($row[$column]);
                        break;

                    default:
                        $selectedData[$column] = $row[$column];
                        break;
                }
            }

            return $selectedData;
        });

        return $newQuery;
    }

    public function columnWidths(): array
    {
        $columnWidths = [];
        $columnIndex = 0;

        foreach ($this->columns as $column) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);

            switch ($column) {
                case 'first_name':
                case 'last_name':
                    $columnWidths[$columnLetter] = 20;
                    break;
                case 'email':
                    $columnWidths[$columnLetter] = 30;
                    break;
                case 'mobile':
                    $columnWidths[$columnLetter] = 20;
                    break;
                case 'gender':
                    $columnWidths[$columnLetter] = 10;
                    break;
                default:
                    $columnWidths[$columnLetter] = 15;
                    break;
            }

            $columnIndex++;
        }

        return $columnWidths;
    }
}
