<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ServicesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('evaluation_summaries as es')
            ->join('employeeprofiles as e', 'es.evaluatee_id', '=', 'e.employeeprofiles_id')
            ->select(
                'e.first_name',
                'e.last_name',
                DB::raw('AVG(es.total_score) as avg_score'),
                DB::raw('COUNT(es.evaluation_summary_id) as evaluation_count')
            )
            ->whereNotNull('es.total_score')
            ->groupBy('e.employeeprofiles_id', 'e.first_name', 'e.last_name')
            ->orderByDesc('avg_score')
            ->get();
    }

    public function headings(): array
    {
        return ['First Name', 'Last Name', 'Average Score', 'Evaluations Count'];
    }
}
