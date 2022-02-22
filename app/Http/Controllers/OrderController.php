<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\BoutiqueProduct;
use App\Models\BoutiqueShipping;
use App\Models\BoutiqueUser;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use App\User;
use PDF;
use Notification;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = Auth::user()->hasRole('admin');

        if ($status) {
            $orders=Order::orderBy('id','DESC')->paginate(10);
            return view('backend.order.index')->with('orders',$orders);
        }else{
            $boutiqueId = Auth::user()->boutique->last()->zone;
            $shippingId=[];
            foreach ($boutiqueId as $key => $value) {
                array_push($shippingId,$value->id);
            }
            $orders = Order::whereIn('shipping_id',$shippingId)->orderBy('id','DESC')->paginate(10);
            return view('backend.order.index')->with('orders',$orders);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $this->validate($request,[
            'first_name'=>'string|required',
            'last_name'=>'string|required',
            'address1'=>'string|required',
            'address2'=>'string|nullable',
            'coupon'=>'nullable|numeric',
            'phone'=>'numeric|required',
            'post_code'=>'string|nullable',
            'email'=>'string|required'
        ]);
        // return $request->all();

        if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
            request()->session()->flash('error','Le panier est vide !');
            return back();
        }

        // $cart=Cart::get();
        // // return $cart;
        // $cart_index='ORD-'.strtoupper(uniqid());
        // $sub_total=0;
        // foreach($cart as $cart_item){
        //     $sub_total+=$cart_item['amount'];
        //     $data=array(
        //         'cart_id'=>$cart_index,
        //         'user_id'=>$request->user()->id,
        //         'product_id'=>$cart_item['id'],
        //         'quantity'=>$cart_item['quantity'],
        //         'amount'=>$cart_item['amount'],
        //         'status'=>'new',
        //         'price'=>$cart_item['price'],
        //     );

        //     $cart=new Cart();
        //     $cart->fill($data);
        //     $cart->save();
        // }

        // $total_prod=0;
        // if(session('cart')){
        //         foreach(session('cart') as $cart_items){
        //             $total_prod+=$cart_items['quantity'];
        //         }
        // }

        $order=new Order();
        $order_data=$request->all();
        $order_data['order_number']='ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']=$request->user()->id;
        $order_data['shipping_id']=$request->shipping;
        $shipping=Shipping::where('id',$order_data['shipping_id'])->pluck('price');
        // return session('coupon')['value'];
        $order_data['sub_total']=Helper::totalCartPrice();
        $order_data['quantity']=Helper::cartCount();
        if(session('coupon')){
            $order_data['coupon']=session('coupon')['value'];
        }
        if($request->shipping){
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0]-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0];
            }
        }
        else{
            if(session('coupon')){
                $order_data['total_amount']=Helper::totalCartPrice()-session('coupon')['value'];
            }
            else{
                $order_data['total_amount']=Helper::totalCartPrice();
            }
        }
        // return $order_data['total_amount'];
        $order_data['status']="new";
        if(request('payment_method')=='paypal'){
            $order_data['payment_method']='paypal';
            $order_data['payment_status']='paid';
        }
        else{
            $order_data['payment_method']='cod';
            $order_data['payment_status']='Unpaid';
        }
        $order->fill($order_data);
        $status=$order->save();
        if($order)
        // dd($order->id);

        $boutique = BoutiqueShipping::where('shipping_id',$request->shipping)->first();
        if ($boutique) {
            $user = BoutiqueUser::where('boutique_id',$boutique->boutique_id)->first();
            if ($user) {
                $users=User::where('role','admin')->orWhere('id',$user->user_id)->get();
            }else{
                $users=User::where('role','admin')->get();
            }
        }else{
            $users=User::where('role','admin')->get();
        }



        $details=[
            'title'=>'Nouvelle commande crée',
            'actionURL'=>route('order.show',$order->id),
            'fas'=>'fa-file-alt'
        ];
        Notification::send($users, new StatusNotification($details));
        if(request('payment_method')=='paypal'){
            return redirect()->route('payment')->with(['id'=>$order->id]);
        }
        else{
            session()->forget('cart');
            session()->forget('coupon');
        }
        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);

        // dd($users);
        request()->session()->flash('success','Votre produit placé avec succès dans la commande');
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=Order::with('cart_info')->find($id);
        // dd($order);
        return view('backend.order.show')->with('order',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order=Order::find($id);
        return view('backend.order.edit')->with('order',$order);
    }
    public function orderStat()
    {
        $order=Order::where('status','delivered');
        $boutiques =  Boutique::all();

        return view('backend.order.statistique')->with('order',$order)->with('boutiques',$boutiques);
    }
    public function getStats(Request $request){
        $rolename = Auth::user()->roles->pluck('name');
        $roleSelect=[];
            foreach ($rolename as $key => $value) {
                array_push($roleSelect,$value);
            }

        if (in_array("admin", $roleSelect)) {
            $year=\Carbon\Carbon::now()->year;
            // dd($year);
            $items=Order::with(['cart_info'])->whereYear('created_at',$year)->where('status','delivered')->get()
                ->groupBy(function($d){
                    return \Carbon\Carbon::parse($d->created_at)->locale('fr')->format('m');
                });

        }elseif (in_array("gerant(e)", $roleSelect)) {
                $boutiqueId = Auth::user()->boutique->last()->zone;
                $shippingId=[];
                foreach ($boutiqueId as $key => $value) {
                    array_push($shippingId,$value->id);
                }
                $year=\Carbon\Carbon::now()->year;
                // dd($year);
                $items=Order::with(['cart_info'])->whereIn('shipping_id',$shippingId)->whereYear('created_at',$year)->where('status','delivered')->get()
                    ->groupBy(function($d){
                        return \Carbon\Carbon::parse($d->created_at)->locale('fr')->format('m');
                    });
        }

        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->cart_info->sum('amount');
                // dd($amount);
                $m=intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        setlocale(LC_TIME, 'fr');
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
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
        $order=Order::find($id);
        $this->validate($request,[
            'status'=>'required|in:new,process,delivered,cancel'
        ]);
        $data=$request->all();
        // return $request->status;
        if($request->status=='delivered'){
            foreach($order->cart as $cart){
                $product=$cart->product;
                $product->stock -=$cart->quantity;
                $product->save();

                $boutiqueShipping =  BoutiqueShipping::where('shipping_id',$order->shipping_id)->first();
                if ($boutiqueShipping) {
                    $boutiqueProduct = BoutiqueProduct::where('boutique_id',$boutiqueShipping->boutique_id)
                                                  ->where('product_id',$cart->product_id)->first();
                    if ($boutiqueProduct) {
                        if ($boutiqueProduct->quantity >= $cart->quantity) {
                            $boutiqueProduct->quantity -= $cart->quantity;
                            $boutiqueProduct->save();
                        }
                    }else{
                        request()->session()->flash('error','Il n\'y a pas produit pour cette zone');
                        return redirect()->route('order.index');
                    }
                }else{
                    request()->session()->flash('error','Il n\'y a pas de boutique dans cette zone');
                    return redirect()->route('order.index');
                }




                if ($boutiqueProduct->quantity < 15) {
                    $globalUpdate = Product::findOrFail($cart->product_id);
                    $boutique = Boutique::findOrFail($boutiqueShipping->boutique_id);
                    $user = BoutiqueUser::where('boutique_id',$boutiqueProduct->boutique_id)->first();
                    if ($user) {
                        $users=User::where('role','admin')->orWhere('id',$user->user_id)->get();
                    }else{
                        $users=User::where('role','admin')->get();
                    }

                    $details=[
                        'title'=>' Attention !!! Le Stock de '.$globalUpdate->title.' pour la Boutique '.$boutique->libelle .' est inferieur à 15 ',
                        'actionURL'=>route('product.index'),
                        'fas'=>'fa-file-alt'
                    ];
                    Notification::send($users, new StatusNotification($details));
                }
            }
        }

        $status=$order->fill($data)->save();
        if($status){
            request()->session()->flash('success','Commande mise à jour avec succès');
        }
        else{
            request()->session()->flash('error','Erreur lors de la mise à jour de la commande');
        }
        return redirect()->route('order.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order=Order::find($id);
        if($order){
            $status=$order->delete();
            if($status){
                request()->session()->flash('success','Commande supprimée avec succès');
            }
            else{
                request()->session()->flash('error','La commande ne peut pas être supprimée');
            }
            return redirect()->route('order.index');
        }
        else{
            request()->session()->flash('error','La commande est introuvable');
            return redirect()->back();
        }
    }

    public function orderTrack(){
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request){
        // return $request->all();
        $order=Order::where('user_id',auth()->user()->id)->where('order_number',$request->order_number)->first();
        if($order){
            if($order->status=="new"){
            request()->session()->flash('success','Votre commande a été enregistrée. S\'il vous plaît, attendez.');
            return redirect()->route('home');

            }
            elseif($order->status=="process"){
                request()->session()->flash('success','Votre commande est en cours de traitement, veuillez patienter.');
                return redirect()->route('home');

            }
            elseif($order->status=="delivered"){
                request()->session()->flash('success','Votre commande est livrée avec succès.');
                return redirect()->route('home');

            }
            else{
                request()->session()->flash('error','Votre commande annulée. Veuillez réessayer');
                return redirect()->route('home');

            }
        }
        else{
            request()->session()->flash('error','Numéro de commande invalide, veuillez réessayer');
            return back();
        }
    }

    // PDF generate
    public function pdf(Request $request){
        ini_set('max_execution_time', 300);
        $order=Order::getAllOrder($request->id);
        // return $order;
        $file_name=$order->order_number.'-'.$order->first_name.'.pdf';
        // return $file_name;
        $pdf=PDF::loadview('backend.order.pdf',compact('order'));
        return $pdf->download($file_name);
    }
    // Income chart
    public function incomeChart(Request $request){
        $rolename = Auth::user()->roles->pluck('name');
        $roleSelect=[];
            foreach ($rolename as $key => $value) {
                array_push($roleSelect,$value);
            }
        if (in_array("admin", $roleSelect)) {
            $year=\Carbon\Carbon::now()->year;
            // dd($year);
            $items=Order::with(['cart_info'])->whereYear('created_at',$year)->where('status','delivered')->get()
                ->groupBy(function($d){
                    return \Carbon\Carbon::parse($d->created_at)->locale('fr')->format('m');
                });
        }elseif (in_array("gerant(e)", $roleSelect)) {
                $boutiqueId = Auth::user()->boutique->last()->zone;
                $shippingId=[];
                foreach ($boutiqueId as $key => $value) {
                    array_push($shippingId,$value->id);
                }
                $year=\Carbon\Carbon::now()->year;
                // dd($year);
                $items=Order::with(['cart_info'])->whereIn('shipping_id',$shippingId)->whereYear('created_at',$year)->where('status','delivered')->get()
                    ->groupBy(function($d){
                        return \Carbon\Carbon::parse($d->created_at)->locale('fr')->format('m');
                    });
        }

        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->cart_info->sum('amount');
                // dd($amount);
                $m=intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        setlocale(LC_TIME, 'fr');
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }
}
