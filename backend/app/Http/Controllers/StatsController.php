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
        return Internship::with('company') // ← very important
        ->selectRaw('company_id, COUNT(*) as total')
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->orderByDesc('total')
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'company_name' => $item->company->company_name ?? 'Neznáma firma',
                    'count' => $item->total,
                ];
            });
    }


}
