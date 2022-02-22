<?php

namespace App\Http\Controllers;

use App\Exports\ApprovisionnementExport;
use App\Imports\ApprovisionnementImport;
use App\Models\BoutiqueProduct;
use App\Models\Product;
use App\Models\Boutique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BoutiqueProductController extends Controller
{
    public function index()
    {
        $boutiques = Boutique::has('products')->get();
        // foreach ($boutique as $key => $value) {

        //     dd($value->products);

        // }
        return view('backend.approvisionnement.index')->with('boutiques',$boutiques);
    }
    public function productBoutique()
    {
        $boutiques = Product::with('boutique')->has('boutique')->get();

        return view('backend.approvisionnement.approByProduct')->with('boutiques',$boutiques);
    }
    public function create()
    {
        $boutiques = Boutique::all();
        $products = Product::all();

        return view('backend.approvisionnement.create')->with('boutiques',$boutiques)->with('products',$products);
    }
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'boutique_id' => 'integer|required',
        //     'product_id' => 'integer|required',
        //     'stock' => 'integer|required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'state'=> false,
        //         'message'=> $validator->errors(),
        //     ]);
        // }

            $status = false;
            $Product = Product::with('boutique')->findOrFail($request['product_id']);
            $productApprov = 0;
            foreach ($Product->boutique as $value) {
                $productApprov += $value->pivot->quantity;
            }
            $test = $Product->stock-$productApprov;
            if ($test > $request['stock'] ) {
                $status = BoutiqueProduct::where('product_id',$request['product_id'])->where('boutique_id',$request['boutique_id'])->first();
                if ($status) {
                    $status->quantity_init += $request['stock'];
                    $status->quantity += $request['stock'];
                    $status->save();
                }else{
                    $data['boutique_id'] = $request['boutique_id'];
                    $data['product_id'] = $request['product_id'];
                    $data['quantity_init'] = $request['stock'];
                    $data['quantity'] = $request['stock'];
                    $status = BoutiqueProduct::create($data);
                }

            }else{

                request()->session()->flash('error','Votre stock n`\'est pas assez pour approvisionner une boutique');
                return redirect()->route('approvisionnement.create');
            }

            if($status){
                request()->session()->flash('success','Produit ajouté avec succès');
            }
            else{
                request()->session()->flash('error','Veuillez réessayer!!');
            }
            return redirect()->route('approvisionnement.index');

    }
    public function fileExport()
    {

        $fichier = Excel::download(new ApprovisionnementExport, 'approvisionnement.xlsx');
        return $fichier;
    }
    public function fileImport(Request $request)
    {
        $excel= Excel::import(new ApprovisionnementImport, $request->file('file'));
        return redirect()->route('approvisionnement.index');
    }
    public function show($id)
    {
        $BoutiqueProduct = BoutiqueProduct::findOrFail($id);
        return response()->json($BoutiqueProduct);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'boutique_id' => 'integer|required',
            'product_id' => 'integer|required',
            'quantite' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state'=> false,
                'message'=> $validator->errors(),
            ]);
        }

        $data['boutique_id'] = $request['boutique_id'];
        $data['product_id'] = $request['product_id'];
        $data['quantity_init'] = $request['quantite'];

        $BoutiqueProduct = BoutiqueProduct::findOrFail($id);
        $status = $BoutiqueProduct->fill($data)->save();

        if ($status) {
            return response()->json([
                'state'=> true,
            ]);
        }else{
            return response()->json([
                'state'=> false,
            ]);
        }
    }

    public function delete($id){
        $BoutiqueProduct = BoutiqueProduct::find($id);

        $BoutiqueProduct->delete();

        return response()->json([
            'state'=> true,
        ]);
    }
}
