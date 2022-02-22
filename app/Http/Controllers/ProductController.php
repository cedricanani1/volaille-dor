<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\BoutiqueProduct;
use App\Models\BoutiqueUser;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Cart;
use Notification;
use App\Models\Order;
use App\Models\Shipping;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use Helper;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
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
            $products=Product::getAllProduct();
            return view('backend.product.index')->with('products',$products);
        }else{
            $products = Auth::user()->boutique->last()->products;
            // dd($products);
            return view('backend.product.index')->with('products',$products);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand=Brand::get();
        $category=Category::where('is_parent',1)->get();
        // return $category;
        return view('backend.product.create')->with('categories',$category)->with('brands',$brand);
    }

    public function sale(Request $request)
    {
        $ordercount =  Order::all();
        $order=new Order();
        $order_data=$request->all();
        $order_data['order_number']='ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']=$request->user()->id;
        $order_data['shipping_id']=$request->shipping;
        $order_data['first_name']='anomyne';
        $order_data['last_name']='anonyme';
        $order_data['address1']='surplace';
        $order_data['country']='CI';
        $order_data['phone']='xxxxxxxxxx';
        $order_data['email']='---';
        $shipping=Shipping::where('id',$order_data['shipping_id'])->pluck('price');
        // return session('coupon')['value'];
        // return $order_data['total_amount'];
        $order_data['status']="delivered";
        if(request('payment_method')=='paypal'){
            $order_data['payment_method']='paypal';
            $order_data['payment_status']='paid';
        }
        else{
            $order_data['payment_method']='cod';
            $order_data['payment_status']='paid';
        }

        $price = 0;
        $quantity=0;
        $globalUpdate=null;
        $boutiqueUser = BoutiqueUser::where('user_id',$request->user()->id)->get()->last();

        $boutiqueItem = Boutique::findOrFail($boutiqueUser->boutique_id);

        $boutiqueProduct =[];

        foreach ($boutiqueItem->products as $product) {
            array_push($boutiqueProduct, $product['pivot']->product_id);
        }
            foreach ($request->product_id as $key =>  $value) {
                if (in_array($value, $boutiqueProduct)) {

                    $boutiqueP = BoutiqueProduct::where('product_id',$value)->where('boutique_id',$boutiqueUser->boutique_id)->get()->last();
                    if ($request->quantity[$key] <= $boutiqueP->quantity) {
                        // dd('same',$product['pivot']->quantity);
                        $price +=( Product::findOrFail($value)->price * $request->quantity[$key]);
                        $quantity += $request->quantity[$key];
                    }else{
                        request()->session()->flash('error','La quantité demandée est superieur au stock!!');
                        return redirect()->route('product.index');
                    }
                }
            }
        $order_data['shipping_id']=$boutiqueItem->shipping_id;
        $order_data['sub_total']=$price;
        $order_data['quantity']=$quantity;

        if(session('coupon')){
            $order_data['coupon']=session('coupon')['value'];
        }

        $order_data['total_amount']=$price;

        $order->fill($order_data);
        $status=$order->save();


        //reduction
        foreach ($request->product_id as $key =>  $value) {
            if (in_array($value, $boutiqueProduct)) {
                $boutiqueP = BoutiqueProduct::where('product_id',$value)->where('boutique_id',$boutiqueUser->boutique_id)->get()->last();
                if ($request->quantity[$key]) {
                    if ($request->quantity[$key] <= $boutiqueP->quantity) {
                        $boutiqueP->quantity -= $request->quantity[$key];
                        $boutiqueP->save();

                        $globalUpdate = Product::findOrFail($value);
                        $globalUpdate->stock -=$request->quantity[$key];
                        $globalUpdate->save();

                        $cart = new Cart;
                        $cart->user_id = auth()->user()->id;
                        $cart->product_id = $globalUpdate->id;
                        $cart->price = $globalUpdate->price;
                        $cart->quantity = $request->quantity[$key];
                        $cart->order_id = $order->id;
                        $cart->amount=$cart->price*$cart->quantity;
                        if ($globalUpdate->stock < $cart->quantity || $globalUpdate->stock <= 0) return back()->with('error','Stock insuffisant!.');
                        $cart->save();

                        if ($boutiqueP->quantity < 15) {
                            $globalUpdate = Product::findOrFail($cart->product_id);
                            $boutique = Boutique::findOrFail($boutiqueP->boutique_id);
                            $user = BoutiqueUser::where('boutique_id',$boutiqueP->boutique_id)->first();
                            $users=User::where('role','admin')->orWhere('id',$user->user_id)->get();
                            $details=[
                                'title'=>' Attention !!! Le Stock de '.$globalUpdate->title.' pour la Boutique '.$boutique->libelle .' est inferieur à 15 ',
                                'actionURL'=>route('product.index'),
                                'fas'=>'fa-file-alt'
                            ];
                            Notification::send($users, new StatusNotification($details));
                        }
                    }else{
                        request()->session()->flash('error','La quantité demandée est superieur au stock!!');
                        return redirect()->route('product.index');
                    }
                }

            }
        }
        if($order)
        // dd($order->id);
        $users=User::where('role','admin')->orWhere('id',$request->user()->id)->get();
        $details=[
            'title'=>'Nouvel Achat effectué',
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
        // Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);

        // dd($users);
        request()->session()->flash('success','produit acheté avec succès');
        // return redirect()->route('product.index');
        return view('backend.order.show')->with('order',$order);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        // $this->validate($request,[
        //     'title'=>'string|required',
        //     'summary'=>'string|required',
        //     'description'=>'string|nullable',
        //     'photo'=>'string|required',
        //     'size'=>'nullable',
        //     'stock'=>"required|numeric",
        //     'cat_id'=>'required|exists:categories,id',
        //     'brand_id'=>'nullable|exists:brands,id',
        //     'child_cat_id'=>'nullable|exists:categories,id',
        //     'is_featured'=>'sometimes|in:1',
        //     'status'=>'required|in:active,inactive',
        //     'condition'=>'required|in:default,new,hot',
        //     'price'=>'required|numeric',
        //     'discount'=>'nullable|numeric'
        // ]);

        $data=$request->all();
        $slug=Str::slug($request->title);
        $count=Product::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_featured']=$request->input('is_featured',0);
        $size=$request->input('size');
        if($size){
            $data['size']=implode(',',$size);
        }
        else{
            $data['size']='';
        }
        // return $size;
        // return $data;
        $status=Product::create($data);
        if($status){
            request()->session()->flash('success','Produit ajouté avec succès');
        }
        else{
            request()->session()->flash('error','Veuillez réessayer!!');
        }
        return redirect()->route('product.index');

    }
    public function test(Request $request) {
        $status = Auth::user()->hasRole('admin');
        $tabs= $this->productStat($status,$request->to,$request->from,$request->boutiqueId);
        $labels=[];
        $datas=[];
        foreach ($tabs as $key => $value) {
            // foreach ($tab as $key => $value) {
                // $key->counter = $value->sum('quantity');

                $product = Product::findOrFail($key);
                $product->counter += $value->sum('quantity');
                $product->costTotal += $value->sum('amount');
                array_push($labels,$product->title);
                array_push($datas,$product->costTotal);
            // }
        }
        return response()->json(
            array(
                'labels'=> $labels,
                'datas'=> $datas
            ),
            200);
     }
    public function stat(Request $request)
    {

        $status = Auth::user()->hasRole('admin');
        // $status =1;

        $tabs= $this->productStat($status,$request->to,$request->from,$request->boutiqueId);

        $productStat=[];
            foreach ($tabs as $key => $value) {
                // foreach ($tab as $key => $value) {
                    // $key->counter = $value->sum('quantity');
                    $product = Product::findOrFail($key);
                    $product->counter += $value->sum('quantity');
                    $product->costTotal += $value->sum('amount');
                    array_push($productStat,$product);
                // }
            }
        $to = $request->to;
        $from = $request->from;
        $boutiqueId =  $request->boutiqueId;

        return view('backend.product.statistique')->with('boutiques',$productStat)->with('to',$to)->with('from',$from)->with('boutiqueId',$boutiqueId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    private function productStat($status,$to,$from,$boutiqueId){
        $shipping =[];
        if ($status) {
            if ($boutiqueId) {
                $boutiques = Boutique::with('zone')->where('id',$boutiqueId)->get();
                foreach ($boutiques as $key => $boutique) {
                    foreach ($boutique->zone as $key => $value) {
                        array_push($shipping,$value->id);
                    }
                }
            }else{
                $boutiques = Boutique::with('zone')->get();
                    foreach ($boutiques as $key => $boutique) {
                        foreach ($boutique->zone as $key => $value) {
                            array_push($shipping,$value->id);
                        }
                    }
            }
        }else{
            $boutiques = Boutique::with('zone')->where('id',Auth::user()->boutique->last()->id)->get();
            foreach ($boutiques as $key => $boutique) {
                foreach ($boutique->zone as $key => $value) {
                    array_push($shipping,$value->id);
                }
            }
        }

        $to = $to;
        $from = $from;
        $boutiqueId =  $boutiqueId;

            if ($to && $from) {
                $tabs= Cart::with('product')->whereHas('order', function($q) use ($shipping,$to,$from){
                    $q->where('status', 'delivered')
                    ->whereBetween(DB::raw("(STR_TO_DATE(orders.updated_at,'%Y-%m-%d'))"),[$to,$from])
                    ->whereIn('shipping_id',$shipping);
                })->get()->groupBy('product_id');
            }else{
                $tabs= Cart::with('product')->whereHas('order', function($q) use ($shipping){
                    $q->where('status', 'delivered')
                    ->whereIn('shipping_id',$shipping);
                })->get()->groupBy('product_id');
            }

            return $tabs;



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand=Brand::get();
        $product=Product::findOrFail($id);
        $category=Category::where('is_parent',1)->get();
        $items=Product::where('id',$id)->get();
        // return $items;
        return view('backend.product.edit')->with('product',$product)
                    ->with('brands',$brand)
                    ->with('categories',$category)->with('items',$items);
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
        $product=Product::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            'size'=>'nullable',
            'stock'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'brand_id'=>'nullable|exists:brands,id',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new,hot',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric'
        ]);

        $data=$request->all();
        $data['is_featured']=$request->input('is_featured',0);
        $size=$request->input('size');
        if($size){
            $data['size']=implode(',',$size);
        }
        else{
            $data['size']='';
        }
        // return $data;
        $status=$product->fill($data)->save();
        if($status){
            request()->session()->flash('success','Produit mis à jour avec succès');
        }
        else{
            request()->session()->flash('error','Veuillez réessayer!!');
        }
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        $status=$product->delete();

        if($status){
            request()->session()->flash('success','Produit supprimé avec succès');
        }
        else{
            request()->session()->flash('error','Erreur lors de la suppression du produit');
        }
        return redirect()->route('product.index');
    }
}
