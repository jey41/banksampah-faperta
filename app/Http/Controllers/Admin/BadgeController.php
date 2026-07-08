<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserBadge;
use App\Services\GamificationService;

class BadgeController extends Controller
{
    /**
     * Lencana didefinisikan secara programatik di GamificationService::BADGES.
     * Halaman ini menampilkan katalog lencana + statistik berapa nasabah
     * yang telah membukanya (read-oriented; unlock logic tetap di service).
     */
    public function index()
    {
        abort_unless(auth()->user()->isStaff(), 403);

        $unlockedCounts = UserBadge::selectRaw('badge_key, COUNT(*) as total')
            ->groupBy('badge_key')
            ->pluck('total', 'badge_key');

        $badges = collect(GamificationService::BADGES)->map(function ($badge) use ($unlockedCounts) {
            $badge['unlocked_count'] = (int) ($unlockedCounts[$badge['key']] ?? 0);
            return $badge;
        });

        $levels = GamificationService::LEVELS;

        return view('admin.badges.index', compact('badges', 'levels'));
    }
}
