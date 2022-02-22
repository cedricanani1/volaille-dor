<?php

namespace App\Http\Controllers;

use App\Exports\StockExport;
use App\Imports\StockImport;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('product')->get();
        return view('backend.stock.index')->with('stocks',$stocks);
    }
    public function create()
    {
        $products=Product::All();
        // return $category;
        return view('backend.stock.create')->with('products',$products);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'integer|required',
            'stock' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state'=> false,
                'message'=> $validator->errors(),
            ]);
        }
            $Stock = Stock::where('product_id',$request['product_id'])->get();
            $num = count($Stock)+1;
            $data['libelle'] = 'STOCK'.$num;
            $data['product_id'] = $request['product_id'];
            $data['quantite'] = $request['stock'];
            $status = Stock::create($data);
            $Product = Product::findOrFail($request['product_id']);
            $produ['stock'] = $request['stock'] + $Product->stock ;


            $status = $Product->fill($produ)->save();

            if($status){
                request()->session()->flash('success','Stock Ajouté avec succès');
            }
            else{
                request()->session()->flash('error','Veuillez réessayer!!');
            }
            return redirect()->route('stock.index');
    }
    public function show($id)
    {
        $Stock = Stock::findOrFail($id);
        return response()->json($Stock);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'integer|required',
            'quantite' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state'=> false,
                'message'=> $validator->errors(),
            ]);
        }
        $data['product_id'] = $request['product_id'];
        $data['quantite'] = $request['quantite'];

        $Stock = Stock::findOrFail($id);
        $status = $Stock->fill($data)->save();

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
    public function fileExport()
    {

        $fichier = Excel::download(new StockExport, 'stock.xlsx');
        return $fichier;
    }
    public function fileImport(Request $request)
    {
        $excel= Excel::import(new StockImport, $request->file('file'));
        return redirect()->route('stock.index');
    }
    public function destroy($id){
        $Stock = Stock::find($id);

        $Stock->delete();

        return response()->json([
            'state'=> true,
        ]);
    }
}
