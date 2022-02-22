<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $Roles = Role::with('product')->get();
        return view('backend.Role.index')->with('Roles',$Roles);
    }
    public function create()
    {
        $products=Product::All();
        // return $category;
        return view('backend.Role.create')->with('products',$products);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state'=> false,
                'message'=> $validator->errors(),
            ]);
        }
            $status = Role::create(['name' => $request->name]);

            if($status){
                return response()->json([
                    'state'=> true,
                ]);
                // request()->session()->flash('success','Role Ajouté avec succès');
            }
            else{
                // request()->session()->flash('error','Veuillez réessayer!!');
            }
            // return redirect()->route('users.index');
    }
    public function show($id)
    {
        $Role = Role::findOrFail($id);
        return response()->json($Role);
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

        $Role = Role::findOrFail($id);
        $status = $Role->fill($data)->save();

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

    public function destroy($id){
        $Role = Role::find($id);

        $Role->delete();

        return response()->json([
            'state'=> true,
        ]);
    }
}
