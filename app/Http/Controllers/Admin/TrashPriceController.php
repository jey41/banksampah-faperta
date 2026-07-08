<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrashPriceRequest;
use App\Models\TrashPrice;
use Illuminate\Http\Request;

class TrashPriceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TrashPrice::class, 'trash_price');
    }

    public function index(Request $request)
    {
        $query = TrashPrice::query()->latest();

        if ($type = $request->get('category_type')) {
            $query->where('category_type', $type);
        }
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $prices = $query->paginate(15)->withQueryString();

        return view('admin.trash-prices.index', compact('prices'));
    }

    public function create()
    {
        return view('admin.trash-prices.create', ['price' => new TrashPrice()]);
    }

    public function store(TrashPriceRequest $request)
    {
        TrashPrice::create($request->validated());

        return redirect()->route('cms.trash-prices.index')
            ->with('success', 'Harga sampah berhasil ditambahkan.');
    }

    public function edit(TrashPrice $trashPrice)
    {
        return view('admin.trash-prices.edit', ['price' => $trashPrice]);
    }

    public function update(TrashPriceRequest $request, TrashPrice $trashPrice)
    {
        $trashPrice->update($request->validated());

        return redirect()->route('cms.trash-prices.index')
            ->with('success', 'Harga sampah berhasil diperbarui.');
    }

    public function destroy(TrashPrice $trashPrice)
    {
        $trashPrice->delete();

        return redirect()->route('cms.trash-prices.index')
            ->with('success', 'Harga sampah berhasil dihapus.');
    }
}
