<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\BoutiqueUser;
use App\User;
use Illuminate\Http\Request;

class BoutiqueUserController extends Controller
{
    public function index()
    {
        $BoutiqueShipping = Boutique::has('user')->get();
        // foreach ($BoutiqueShipping as $key => $value) {
        //     dd($value->user);
        // }

        return view('backend.affectation.index')->with('boutiques',$BoutiqueShipping);
    }
    public function create()
    {
        $boutiques = Boutique::doesntHave('user')->get();
        $users = User::doesntHave('boutique')->role('gerant(e)')->get();

        return view('backend.affectation.create')->with('boutiques',$boutiques)->with('users',$users);
    }
    public function store(Request $request)
    {
        $request->validate([
            'boutique_id' => 'integer|required',
        ]);

        foreach ($request->user_id as  $value) {
            $data['boutique_id'] = $request['boutique_id'];
            $data['user_id'] = $value;
            $status = BoutiqueUser::create($data);
        }


            if($status){
                request()->session()->flash('success','Produit ajouté avec succès');
            }
            else{
                request()->session()->flash('error','Veuillez réessayer!!');
            }
            return redirect()->route('affectation.index');

    }
    public function show($id)
    {
        $BoutiqueShipping = BoutiqueUser::findOrFail($id);
        return response()->json($BoutiqueShipping);
    }
    public function edit($id){
        $boutique = Boutique::findOrFail($id);
        $tab=[];
        $users=[];

        foreach ($boutique->user as $key => $value) {
            array_push($tab,$value->id);
            array_push($users,$value);
        }

        $shipin = User::role('gerant(e)')->doesntHave('boutique')->get();

        foreach ($shipin as $key => $value) {
            array_push($users,$value);
        }


        return view('backend.affectation.edit')->with('boutique',$boutique)->with('users',$users)->with('tab',$tab);
    }
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'libelle' => 'string|required',
        //     'user_id' => 'integer|required',
        // ]);
        $boutique = Boutique::findOrFail($request['boutique_id']);
        $boutique->user()->detach();

        if ($request->user_id != null) {
            foreach ($request->user_id as  $value) {
                $data['boutique_id'] = $request['boutique_id'];
                $data['user_id'] = $value;
                $status = BoutiqueUser::create($data);
            }
        }else{
            $status = true;
        }
        if($status){
            request()->session()->flash('success','Affectation reussi avec succès');
        }
        else{
            request()->session()->flash('error','Veuillez reessayer!!');
        }

        return redirect()->route('affectation.index');
    }

    public function delete($id){
        $BoutiqueShipping = BoutiqueUser::find($id);

        $BoutiqueShipping->delete();

        return response()->json([
            'state'=> true,
        ]);
    }
}
