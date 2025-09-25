<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Parish;
use App\Models\Category;
use App\Models\Entry;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistiques principales
        $stats = [
            'total_entries' => Entry::count(),
            'total_parishes' => Parish::count(),
            'current_month_entries' => Entry::whereMonth('created_at', Carbon::now()->month)
                                          ->whereYear('created_at', Carbon::now()->year)
                                          ->count(),
            'total_amount' => 0, // À calculer plus tard avec les données JSON
        ];

        // Fiches récentes (5 dernières)
        $recent_entries = Entry::with(['parish', 'category'])
                              ->latest()
                              ->limit(5)
                              ->get();

        // Statistiques par catégorie
        $categories_stats = Category::withCount('entries')
                                  ->get()
                                  ->map(function ($category) {
                                      return [
                                          'name' => $category->name,
                                          'count' => $category->entries_count,
                                          'percentage' => 0, // À calculer
                                      ];
                                  });

        // Calculer les pourcentages
        $total_entries = $categories_stats->sum('count');
        if ($total_entries > 0) {
            $categories_stats = $categories_stats->map(function ($stat) use ($total_entries) {
                $stat['percentage'] = round(($stat['count'] / $total_entries) * 100, 1);
                return $stat;
            });
        }

        return view('dashboard', compact('stats', 'recent_entries', 'categories_stats'));
    }
}
