<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\Shipping;
use Illuminate\Http\Request;

class BoutiqueController extends Controller
{
    public function index()
    {
        $Boutique = Boutique::with('lieu')->get();
        return view('backend.boutique.index')->with('boutiques',$Boutique);
    }
    public function create()
    {
        // return $category;
        $shippings=Shipping::all();
        return view('backend.boutique.create')->with('shippings',$shippings);
    }
    public function edit($id)
    {
        $boutique=Boutique::findOrFail($id);
        $shippings=Shipping::all();
        // return $items;
        return view('backend.boutique.edit')->with('boutique',$boutique)->with('shippings',$shippings);
    }
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'string|required',
            'shipping_id' => 'integer|required',
        ]);

            $data['libelle'] = $request['libelle'];
            $data['shipping_id'] = $request['shipping_id'];
            $status = Boutique::create($data);

            if($status){
                request()->session()->flash('success','Produit ajouté avec succès');
            }
            else{
                request()->session()->flash('error','Veuillez réessayer!!');
            }
            return redirect()->route('boutique.index');

    }
    public function show($id)
    {
        $Boutique = Boutique::findOrFail($id);
        return response()->json($Boutique);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'libelle' => 'string|required',
            'shipping_id' => 'integer|required',
        ]);
        $data['libelle'] = $request['libelle'];
        $data['shipping_id'] = $request['shipping_id'];
        $Boutique = Boutique::findOrFail($id);
        $status = $Boutique->fill($data)->save();

        if($status){
            request()->session()->flash('success','Produit ajouté avec succès');
        }
        else{
            request()->session()->flash('error','Veuillez réessayer!!');
        }
        return redirect()->route('boutique.index');
    }

    public function delete($id){
        $Boutique = Boutique::find($id);

        $Boutique->delete();

        return response()->json([
            'state'=> true,
        ]);
    }
}
