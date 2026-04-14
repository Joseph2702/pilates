<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Domain\Entity\Artikel;
use App\Http\Controllers\Controller;

class ArticlesWebController extends Controller
{
    public function index()
    {
        $artikelList = Artikel::with('user')
            ->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now())
            ->orderBy('tanggal_publish', 'desc')
            ->paginate(9);

        return view('web.articles.index', compact('artikelList'));
    }

    public function show(int $id)
    {
        $artikel = Artikel::with('user')
            ->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now())
            ->findOrFail($id);

        $relatedArticles = Artikel::with('user')
            ->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now())
            ->where('id_artikel', '!=', $id)
            ->orderBy('tanggal_publish', 'desc')
            ->limit(3)
            ->get();

        return view('web.articles.show', compact('artikel', 'relatedArticles'));
    }
}
