<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Internship;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InternshipController extends Controller
{
    /**
     * GET /api/internships
     * Zobrazí všetky praxe prihláseného študenta.
     */
    public function index(Request $request)
    {
        // Získame študentský profil prepojený s prihláseným používateľom
        $student = Auth::user()->student;

        // Ak používateľ nemá priradený študentský profil, vrátime chybu
        if (!$student) {
            return response()->json(['message' => 'Prístup zamietnutý. Používateľ nemá profil študenta.'], 403);
        }

        // Načítame praxe pre konkrétneho študenta
        $internships = Internship::with('company')
            ->where('student_id', $student->id) // <-- Používame ID študenta
            ->orderByDesc('created_at')
            ->get();

        // Dáta transformujeme do formátu, ktorý očakáva frontend
        $mappedInternships = $internships->map(function ($internship) {
            $startDate = $internship->start_date ? new \DateTime($internship->start_date) : null;
            $endDate = $internship->end_date ? new \DateTime($internship->end_date) : null;

            return [
                'id' => $internship->id,
                'firma' => $internship->company?->name ?? 'Neznáma firma',
                'rok' => $startDate ? $startDate->format('Y') : 'N/A',
                'semester' => $startDate ? ($startDate->format('m') < 7 ? 'LS' : 'ZS') : 'N/A',
                'termin' => $startDate && $endDate ? $startDate->format('d.m.Y') . ' – ' . $endDate->format('d.m.Y') : 'N/A',
                'stav' => strtoupper($internship->status ?? 'VYTVORENÁ'),
            ];
        });

        return response()->json($mappedInternships);
    }

    /**
     * POST /api/internships
     * Vytvorí novú prax pre prihláseného študenta.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firma' => 'required|string|max:255',
            'rok' => 'required|integer|min:2020',
            'semester' => ['required', 'string', Rule::in(['LS', 'ZS'])],
            'datumZaciatku' => 'required|date',
            'datumKonca' => 'required|date|after_or_equal:datumZaciatku',
        ]);

        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['message' => 'Prístup zamietnutý. Používateľ nemá profil študenta.'], 403);
        }

        // Nájde firmu podľa mena alebo vytvorí novú, ak neexistuje
        $company = Company::firstOrCreate(
            ['name' => $validated['firma']],
            ['statutary' => 'Nezadaný'] // Môžete doplniť defaultné hodnoty
        );

        // Vytvorí novú prax a priradí ju študentovi
        $internship = Internship::create([
            'student_id' => $student->id, // <-- Používame ID študenta
            'company_id' => $company->id,
            'status' => 'vytvorená', // Odporúčam malé písmená pre konzistenciu v DB
            'start_date' => $validated['datumZaciatku'],
            'end_date' => $validated['datumKonca'],
        ]);

        return response()->json([
            'message' => 'Prax bola úspešne vytvorená.',
            'internship' => $internship
        ], 201);
    }
}