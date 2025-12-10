<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    // 1) Počet študentov podľa akademického roka (line chart)
    public function studentsTrend()
    {
        $data = Internship::select('academy_year', DB::raw('COUNT(*) as count'))
            ->groupBy('academy_year')
            ->orderBy('academy_year')
            ->get();

        return response()->json($data);
    }

    // 2) Typy praxí (pie chart)
    public function internshipTypes()
    {
        $data = Internship::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        return response()->json($data);
    }

    // 3) Top firmy podľa počtu praxí (bar chart)
    public function topCompanies()
    {
        return Internship::query()
            ->join('companies', 'internships.company_id', '=', 'companies.id')
            ->select('companies.name as company_name')
            ->selectRaw('COUNT(internships.id) as count')
            ->groupBy('companies.name')
            ->orderByDesc('count')
            ->limit(3)
            ->get();
    }
    public function allCompanies()
    {
        $data = Internship::select('company_id', DB::raw('COUNT(*) as count'))
            ->with('company:id,name')
            ->groupBy('company_id')
            ->orderByDesc('count')
            ->get()
            ->map(fn($row) => [
                'company_name' => $row->company->name ?? 'Neznáma firma',
                'count' => $row->count
            ]);

        return response()->json($data);
    }




}
