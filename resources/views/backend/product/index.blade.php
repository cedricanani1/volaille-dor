@extends('backend.layouts.master')

@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Liste des produits</h6>
      @role('admin|manager')
        <a href="{{route('product.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Ajouter un produit"><i class="fas fa-plus"></i> Ajouter un produit</a>
      @endrole
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($products)>0)
        <form method="post" action="{{route('product.sale')}}">
            {{csrf_field()}}
            <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Categorie</th>
                <th>Publier</th>
                <th>Prix</th>
                <th>Réduction</th>
                <th>Condition</th>
                @role('gerant(e)')
                <th>Stock actuel</th>
                <th>Stock vendu</th>
                @endrole
                @role('admin|manager')
                <th>Stock distribué</th>
                <th>Stock en reserve</th>
                @endrole
                <th>Photo</th>
                <th>Status</th>
                @role('admin|manager')
                <th>Action</th>
                @endrole
                @role('gerant(e)')
                <th>Quantité</th>
                @endrole
                </tr>
            </thead>
            <tfoot>
                <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Categorie</th>
                <th>Publier</th>
                <th>Prix</th>
                <th>Reduction</th>
                <th>Condition</th>
                <th>Stock distribué</th>
                <th>Stock en reserve</th>
                <th>Photo</th>
                <th>Status</th>
                @role('admin|manager')
                <th>Action</th>
                @endrole
                @role('gerant(e)')
                <th>Quantité</th>
                @endrole
                </tr>
            </tfoot>
            <tbody>

                @foreach($products as $key => $product)
                    @php
                    $sub_cat_info=DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
                    // dd($sub_cat_info);
                    $brands=DB::table('brands')->select('title')->where('id',$product->brand_id)->get();
                    @endphp
                    @php
                        $Product = App\Models\Product::with('boutique')->findOrFail($product->id);
                        $productApprov = 0;
                        foreach ($Product->boutique as $value) {
                            $productApprov += $value->pivot->quantity;
                        }
                        $reserve = $Product->stock-$productApprov;
                    @endphp
                <input type="hidden" name="product_id[]" value="{{$product->id}}">
                    <tr>
                        <td>{{$key +1}}</td>
                        <td>{{$product->title}}</td>
                        <td>{{$product->cat_info['title']}}
                        <sub>
                            @foreach($sub_cat_info as $data)
                            {{$data->title}}
                            @endforeach
                        </sub>
                        </td>
                        <td>{{(($product->is_featured==1)? 'Yes': 'No')}}</td>
                        <td> {{$product->price}} Fcfa</td>
                        <td>  {{$product->discount}}% </td>
                        <td>{{$product->condition}}</td>
                        <td>
                        @if($product->stock>0)
                        @role('gerant(e)')
                            <span class="badge badge-success">{{$product->pivot->quantity}}</span>
                            @elserole('admin|manager')
                            <span class="badge badge-danger">{{$productApprov}}</span>
                            {{-- <span class="badge badge-danger">{{$product->stock}}</span> --}}
                        @endrole

                        @endif
                        </td>

                        <td>
                            @role('gerant(e)')
                                <span class="badge badge-danger">{{$product->pivot->quantity_init -$product->pivot->quantity}}</span>
                            @elserole('admin|manager')
                                <span class="badge badge-success">{{$reserve}}</span>
                                {{-- <span class="badge badge-danger">{{$product->stock}}</span> --}}
                            @endrole

                        </td>
                        <td>
                            @if($product->photo)
                                @php
                                $photo=explode(',',$product->photo);
                                // dd($photo);
                                @endphp
                                <img src="{{$photo[0]}}" class="img-fluid zoom" style="max-width:80px" alt="{{$product->photo}}">
                            @else
                                <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                            @endif
                        </td>
                        <td>
                            @if($product->status=='active')
                                <span class="badge badge-success">{{$product->status}}</span>
                            @else
                                <span class="badge badge-warning">{{$product->status}}</span>
                            @endif
                        </td>
                        @role('admin|manager')
                        <td>
                            <a href="{{route('product.edit',$product->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{route('product.destroy',[$product->id])}}">
                        @csrf
                        @method('delete')
                            <button class="btn btn-danger btn-sm dltBtn" data-id={{$product->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                        @endrole
                        @role('gerant(e)')
                        <td>
                            <input type="number" name="quantity[]" min="0" max="{{$product->pivot->quantity}}" class="form-control">
                        </td>
                        @endrole
                        {{-- Delete Modal --}}
                        {{-- <div class="modal fade" id="delModal{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="#delModal{{$user->id}}Label" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="#delModal{{$user->id}}Label">Delete user</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                <form method="post" action="{{ route('categorys.destroy',$user->id) }}">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-danger" style="margin:auto; text-align:center">Parmanent delete user</button>
                                </form>
                                </div>
                            </div>
                            </div>
                        </div> --}}
                    </tr>
                @endforeach
            </tbody>
            </table>
            @role('gerant(e)')
                <div class="form-group mb-3 float-right">
                    <button class="btn btn-success" type="submit">Valider</button>
                </div>
            @endrole

        </form>
        {{-- <span style="float:right">{{$products->links()}}</span> --}}
        @else
          <h6 class="text-center">Aucun produit trouvé !!! Veuillez créer un produit</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
      .zoom {
        transition: transform .2s; /* Animation */
      }

      .zoom:hover {
        transform: scale(5);
      }
  </style>
@endpush

@push('scripts')

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>

      $('#product-dataTable').DataTable( {
        "scrollX": false
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[10,11,12]
                }
            ]
        } );

        // Sweet alert

        function deleteData(id){

        }
  </script>
  <script>
      $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
              var dataID=$(this).data('id');
              // alert(dataID);
              e.preventDefault();
              swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                       form.submit();
                    } else {
                        swal("Your data is safe!");
                    }
                });
          })
      })
  </script>
@endpush
