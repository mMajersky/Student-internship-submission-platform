<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * 1) Number of internships per academic year (line chart)
     */
    public function studentsTrend()
    {
        $data = Internship::select('academy_year', DB::raw('COUNT(*) as count'))
            ->whereNotNull('academy_year')
            ->groupBy('academy_year')
            ->orderBy('academy_year')
            ->get();

        return response()->json($data);
    }

    /**
     * 2) Internship practice types distribution (doughnut / pie chart)
     */
    public function internshipTypes()
    {
        $data = Internship::select('type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('type')
            ->groupBy('type')
            ->get();

        return response()->json($data);
    }

    /**
     * 3) Top companies by number of internships (bar chart)
     */
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

    /**
     * 4) All companies with internship counts (bar chart – full list)
     */
    public function allCompanies()
    {
        $data = Internship::select('company_id', DB::raw('COUNT(*) as count'))
            ->with('company:id,name')
            ->groupBy('company_id')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'company_name' => $row->company->name ?? 'Unknown company',
                'count' => $row->count,
            ]);

        return response()->json($data);
    }

    public function internshipSummary()
    {
        $running = Internship::whereNotIn('status', [
            Internship::STATUS_DEFENDED,
            Internship::STATUS_NOT_DEFENDED,
        ])->count();

        $finished = Internship::whereIn('status', [
            Internship::STATUS_DEFENDED,
            Internship::STATUS_NOT_DEFENDED,
        ])->count();

        return response()->json([
            'running' => $running,
            'finished' => $finished,
        ]);
    }

}
