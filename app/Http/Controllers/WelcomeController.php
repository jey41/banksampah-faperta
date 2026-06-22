<?php

namespace App\Http\Controllers;

use App\Models\TrashPrice;
use App\Models\Article;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        $prices = TrashPrice::orderBy('category')->orderBy('name')->take(5)->get();
        $articles = Article::where('status', 'published')->orderBy('created_at', 'desc')->take(3)->get();

        $totalCarbon = \App\Models\DepositItem::whereHas('deposit', function ($query) {
            $query->where('status', 'approved');
        })->sum('total_carbon');

        $totalWaste = \App\Models\Deposit::where('status', 'approved')->sum('weight_total');

        return Inertia::render('Welcome', [
            'prices' => $prices,
            'articles' => $articles,
            'totalCarbonContribution' => (float)$totalCarbon,
            'totalWaste' => (float)$totalWaste,
        ]);
    }

    public function prices(): Response
    {
        $prices = TrashPrice::orderBy('category')->orderBy('name')->get();

        return Inertia::render('Public/PriceCatalog', [
            'prices' => $prices,
        ]);
    }

    public function articles(): Response
    {
        $articles = Article::where('status', 'published')->orderBy('created_at', 'desc')->paginate(9);

        return Inertia::render('Public/ArticleDirectory', [
            'articles' => $articles,
        ]);
    }

    public function article(string $slug): Response
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $recentArticles = Article::where('id', '!=', $article->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Public/ArticleView', [
            'article' => $article,
            'recentArticles' => $recentArticles,
        ]);
    }
}

