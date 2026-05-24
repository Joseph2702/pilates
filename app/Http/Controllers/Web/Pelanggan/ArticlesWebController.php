<?php

namespace App\Http\Controllers\Web\Pelanggan;

use App\Domain\Entity\Artikel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticlesWebController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Artikel::with('user')
            ->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now());

        if ($search) {
            $query->where(fn ($q) => $q
                ->where('judul_artikel', 'ilike', '%'.$search.'%')
                ->orWhere('konten_artikel', 'ilike', '%'.$search.'%')
            );
        }

        $artikelList = $query->orderBy('tanggal_publish', 'desc')->paginate(9)->withQueryString();

        return view('web.articles.index', compact('artikelList', 'search'));
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
