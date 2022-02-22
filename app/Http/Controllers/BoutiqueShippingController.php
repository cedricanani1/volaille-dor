<?php

namespace App\Http\Controllers;

use App\Models\BoutiqueShipping;
use App\Models\Boutique;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class BoutiqueShippingController extends Controller
{
    public function index()
    {
        $BoutiqueShipping = Boutique::all();
        return view('backend.zonecouverture.index')->with('boutique',$BoutiqueShipping);
    }
    public function create()
    {
        $boutiques = Boutique::all();
        $zones = Shipping::doesntHave('boutique')->get();

        return view('backend.zonecouverture.create')->with('boutiques',$boutiques)->with('zones',$zones);
    }
    public function store(Request $request)
    {
        $request->validate([
            'boutique_id' => 'integer|required',
        ]);

        foreach ($request->shipping_id as  $value) {
            $data['boutique_id'] = $request['boutique_id'];
            $data['shipping_id'] = $value;
            $status = BoutiqueShipping::create($data);
        }
            if($status){
                request()->session()->flash('success','Zone ajouté avec succès');
            }
            else{
                request()->session()->flash('error','Veuillez réessayer!!');
            }
            return redirect()->route('zonecouverture.index');

    }
    public function show($id)
    {
        $BoutiqueShipping = BoutiqueShipping::findOrFail($id);
        return response()->json($BoutiqueShipping);
    }
    public function edit($id){
        $boutique = Boutique::findOrFail($id);
        $tab=[];
        $zones=[];
        foreach ($boutique->zone as $key => $value) {
            array_push($tab,$value->id);
            array_push($zones,$value);
        }
        $shipin = Shipping::doesntHave('boutique')->get();
        foreach ($shipin as $key => $value) {
            array_push($zones,$value);
        }

        return view('backend.zonecouverture.edit')->with('boutique',$boutique)->with('tab',$tab)->with('zones',$zones);
    }
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'libelle' => 'string|required',
        //     'user_id' => 'integer|required',
        // ]);
        $boutique = Boutique::findOrFail($request['boutique_id']);
        $boutique->zone()->detach();

        foreach ($request->shipping_id as  $value) {
            $data['boutique_id'] = $request['boutique_id'];
            $data['shipping_id'] = $value;
            $status = BoutiqueShipping::create($data);
        }

        $status = $boutique->fill($data)->save();

        if($status){
            request()->session()->flash('success','Zone de couverture modifié avec succès');
        }
        else{
            request()->session()->flash('error','Veuillez réessayer!!');
        }
        return redirect()->route('zonecouverture.index');
    }
    public function statChart(Request $request)
    {

        $status = Auth::user()->hasRole('admin');

        $to = $request->to;
        $from = $request->from;
        $boutiqueId =  $request->boutiqueId;
        $boutiques = $this->stat($status,$boutiqueId,$to,$from);
        $labels=[];
        $datas=[];
        $productName =[];

        $products =  Product::all();
        $pr=[];
        $nbS=[];
        foreach ($products as $key => $product) {
            $dat=[];
            $nbStat=[];
            foreach ($boutiques as $key => $boutique) {
                if (!in_array($boutique->libelle,$labels)) {
                    array_push($labels,$boutique->libelle);
                }
                $tab=[];
                foreach ($boutique->products as $key => $prod) {
                    if ($prod->id == $product->id) {
                        // $object = new stdClass();
                        // $object->ca = $prod->priceCount;
                        // $object->nb = $prod->productCount;
                        // $nbStat[] = $object;
                        // array_push($dat,$nbStat);
                        array_push($dat,$prod->priceCount);
                        array_push($nbStat,$prod->productCount);
                    }
                }

            }

            array_push($pr,$dat);
            array_push($productName,$product->title);
            array_push($nbS,$nbStat);
        }

        // foreach ($boutiques as $key => $boutique) {
        //     $tab=[];
        //     $products =  Product::all();
        //     foreach ($boutique->products as $key => $product) {
        //         array_push($tab,$product->id);
        //     }
        //         array_push($labels,$boutique->libelle);
        //         $dat=[];
        //         $label=[];
        //         foreach ($products as $key => $product) {
        //             array_push($label,$product->title);
        //             if (in_array($product->id,$tab)) {
        //                 $priceCount= $boutique->products->where('id',$product->id)->first();
        //                 if ($priceCount->priceCount>0) {
        //                     array_push($dat,$priceCount->priceCount);
        //                 }else{
        //                     array_push($dat,0);
        //                 }
        //             }else{
        //                 array_push($dat,0);
        //             }
        //         }
        //         $boutique->labels = $label;
        //         $boutique->stat = $dat;
        //     # code...prudtc
        // }
        // array_push($datas,$boutique->amoutTotal);

        return response()->json(
            array(
                'labels'=> $labels,
                'prod'=> $pr,
                'nbStat'=> $nbS,
                'productName'=> $productName,
                'datas'=> $boutiques
            ),
        200);


    }
    public function statVente(Request $request)
    {

        $status = Auth::user()->hasRole('admin');

        $to = $request->to;
        $from = $request->from;
        $boutiqueId =  $request->boutiqueId;
        $boutiques = $this->stat($status,$boutiqueId,$to,$from);
        $sum = $boutiques->sum('amoutTotal');

        return view('backend.order.statistique')->with('sum',$sum)->with('boutiques',$boutiques)->with('to',$to)->with('from',$from)->with('boutiqueId',$boutiqueId);
    }

    public function delete($id){
        $BoutiqueShipping = BoutiqueShipping::find($id);

        $BoutiqueShipping->delete();

        return response()->json([
            'state'=> true,
        ]);
    }

    private function stat($status,$boutiqueId,$to,$from){

        if ($status) {
            if ($boutiqueId) {
                $boutiques = Boutique::with('zone')->where('id',$boutiqueId)->get();
            }else{
                $boutiques = Boutique::with('zone')->get();
            }

        }else{
            $boutiques = Boutique::with('zone')->where('id',Auth::user()->boutique->last()->id)->get();
        }
        $to = $to;
        $from = $from;
        $boutiqueId =  $boutiqueId;
        $tab=[];
        $productsId=[];
        foreach ($boutiques as $key => $boutique) {
            foreach ($boutique->products as $key => $product) {
                array_push($productsId,$product->id);
            }
            foreach ($boutique->zone as $key => $value) {
                array_push($tab,$value->id);
            }

            if ($to && $from) {
                $orders= \App\Models\Order::with('cart_info')->where('status','delivered')
                                                        ->whereIn('shipping_id',$tab)
                                                        ->whereBetween(DB::raw("(STR_TO_DATE(orders.updated_at,'%Y-%m-%d'))"),[$to,$from])
                                                        ->get();
            } else {
                $orders= \App\Models\Order::with('cart_info','shipping')->where('status','delivered')
                                                        ->whereIn('shipping_id',$tab)
                                                        ->get();
            }
            $livraisonCount=0;
            foreach ($orders as $key => $order) {
                $livraisonCount += $order->shipping->price;
            }
            $boutique->amoutTotal=$orders->sum('total_amount');
            $boutique->charge=$orders->sum('total_amount')-$orders->sum('sub_total');
            $boutique->orders = $orders;
            foreach ($boutique->products as $key => $product) {
                foreach ($orders as $key => $order) {
                    foreach ($order->cart_info as $key => $cart) {
                        if ($cart->product_id == $product->id) {
                            $product->productCount +=$cart->quantity;
                            $product->priceCount +=$cart->amount;
                        }
                    }
                }
            }

            $boutique->products;
            $tab=[];

        }
        return $boutiques;

    }
}
