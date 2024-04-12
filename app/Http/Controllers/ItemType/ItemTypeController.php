<?php

namespace App\Http\Controllers\ItemType;

use App\Http\Controllers\Controller;
use App\Models\ItemType;
use Illuminate\Http\Request;

class ItemTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $item_types = ItemType::paginate(10);
        return view('item-types.index', compact('item_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('item-types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $item_type = ItemType::create([
            'name' => $request->name,
        ]);
        return redirect()->route('item-types.index')->with('success', 'Sukses! Jenis Barang ' . $item_type->name . ' berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item_type = ItemType::findOrFail($id);
        return view('item-types.show', compact('item_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item_type = ItemType::findOrFail($id);
        return view('item-types.edit', compact('item_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item_type = ItemType::findOrFail($id);
        $item_type->update([
            'name' => $request->name,
        ]);
        return redirect()->route('item-types.index')->with('success', 'Sukses! Jenis Barang ' . $item_type->name . ' berhasil diedit!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item_type = ItemType::findOrFail($id);
        $item_type->delete();
        return redirect()->route('item-types.index')->with('success', 'Sukses! Jenis Barang ' . $item_type->name . ' berhasil dihapus!');
    }
}
