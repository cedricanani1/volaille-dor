@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Statistique</h5>
    <div class="card-body">
      <form method="post" action="{{route('order.stats')}}">
        {{csrf_field()}}
        <div class="row">
            <div class="form-group col-md-6">
                <label for="inputTitle" class="col-form-label">date debut <span class="text-danger">*</span></label>
                  <input id="inputTitle" type="date" name="to" value="{{$to}}" placeholder="Enter title"   class="form-control">
                  @error('to')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="inputTitle" class="col-form-label">date fin <span class="text-danger">*</span></label>
                <input id="inputTitle" type="date" name="from" value="{{$from}}" placeholder="Enter title" class="form-control">
                    @error('from')
                <span class="text-danger">{{$message}}</span>
                    @enderror
            </div>
        </div>
        @php
            $boutiqueOption= \App\Models\Boutique::all();
        @endphp
        @role('admin')
        <div class="form-group">
            <label for="status" class="col-form-label">Boutique <span class="text-danger">*</span></label>
            <select name="boutiqueId" class="form-control">
                <option value=""> -- Selectionner une boutique --</option>
                @foreach ($boutiqueOption as $boutique )
                    @if ($boutiqueId == $boutique->id)
                        <option value="{{$boutique->id}}" selected> {{$boutique->libelle}} </option>
                        @else
                        <option value="{{$boutique->id}}"> {{$boutique->libelle}} </option>
                    @endif
                @endforeach
            </select>
            @error('status')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        @endrole
        <div class="form-group mb-3">
            <button class="btn btn-success" type="submit">Valider</button>
            <div class="float-right">
                <a onclick="getMessage()" class="btn btn-primary" style="color:white" type="button"> Voir le graphe</a>
            </div>
       </div>


        {{-- {{dd($orders)}} --}}
        {{-- @if (empty($orders)) --}}

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 text-center">
                        <label  for="">Chiffre d'affaire (Fcfa) / Boutique</label>
                        <canvas id="myChart" style="display: none"></canvas>
                    </div>
                    <div class="col-md-6 text-center">
                        <label  for="">Nombre de produit vendu / Boutique</label>
                        <canvas id="Chart" style="display: none"></canvas>
                    </div>

                </div>


                <div class="table-responsive">

                @if(count($boutiques)>0)
                <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Details</th>
                        <th>Chiffre d'affaire</th>
                        {{-- <th>Action</th> --}}
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th></th>
                        <th>Total</th>
                        {{-- @if (isset($sommetotal)) --}}
                        <th>{{ $sum }}</th>
                        {{-- @else --}}

                        {{-- @endif --}}

                        {{-- <th>Action</th> --}}
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach($boutiques as $key => $boutique)
                    {{-- {{dd($approvisionnement->boutique)}} --}}
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{$boutique->libelle}}</td>
                            <td>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>produits</th>
                                            <th>quantité</th>
                                            <th>Total</th>

                                        </tr>

                                        </thead>
                                    @foreach ($boutique->products as $appro )
                                        <tr>
                                            @php
                                                $after_discount=($appro->price-($appro->price*$appro->discount)/100)
                                            @endphp
                                            <td>{{$appro->title}}</td>
                                            {{-- <td>{{$after_discount}}</td> --}}
                                            @if ($appro->productCount)
                                                <td>{{$appro->productCount}}</td>
                                            @else
                                                <td>0</td>
                                            @endif
                                            <td>{{$appro->priceCount}}</td>

                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th>Charge de livraison</th>
                                        <th></th>
                                        <th> {{$boutique->charge}} </th>

                                    </tr>

                                </table>
                            </td>
                            <td>{{$boutique->amoutTotal}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{-- <span style="float:right">{{$boutiques->links()}}</span> --}}
                @else
                    <h6 class="text-center">Aucune approvisionnement trouvée !!! Veuillez créer un approvisionnement</h6>
                @endif
                </div>
            </div>
        {{-- @else
            <h6 class="text-center">Il n'y a pas eu de vente dans cette période</h6>
        @endif --}}

      </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js">
</script>

<script>
   function getMessage() {
    var token =  $('meta[name="csrf-token"]').attr('content');
    var to = $('input[name="to"]').val();
    var from= $('input[name="from"]').val();
    var boutiqueId = $('select[name="boutiqueId"]').val() ;

      $.ajax({
         type:'POST',
         url:'/statChart',
         headers: {
                    'X-CSRF-Token': token
               },
        //  data: { CSRF: getCSRFTokenValue()},
         data:{'to':to,'from': from,'boutiqueId':boutiqueId},
         success:function(result) {
            console.log(result)
            const ctx = document.getElementById('myChart');
            const ctx2 = document.getElementById('Chart');
            document.getElementById("myChart").style.display = "block";
            document.getElementById("Chart").style.display = "block";
            let tab=[]
            let nb=[]
            let color=["#eece01","#87d84d","#f8981f","#800000","#800080","#808000","#FFFF00","#808080","#FF0000"]
            let colornb=["#808000","#FFFF00","#808080","#FF0000","#808010","#FFFF10","#808010","#FF0010"]
            result.productName.forEach((element,index) => {
                    tab.push({
                        label: element,
                        type: "bar",
                        stack: "Base",
                        fill: true,
                        backgroundColor: color[index],
                        data: result.prod[index],
                    })
            });
            result.productName.forEach((element,index) => {
                nb.push({
                        label: element,
                        type: "bar",
                        stack: "Sensitivity",
                        backgroundColor: color[index],
                        data: result.nbStat[index],
                    })
            });
            const config1 = {
                type: 'bar',
                data: {
                    labels: result.labels,
                    datasets:tab
                },

            };
            const config2 = {
                type: 'bar',
                data: {
                    labels: result.labels,
                    datasets:nb,
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'TEST',
                        }
                    },
                    scales: {
                        beginAtZero: true,
                        y: {
                            ticks: {
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }

            };

            const myChart = new Chart(
                ctx,
                config1
            );
            const test = new Chart(
                ctx2,
                config2
            );
         }
      });

   }
</script>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
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
        transform: scale(3.2);
      }
  </style>
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
        $('#description').summernote({
        placeholder: "Write short description.....",
            tabsize: 2,
            height: 150
        });
    });
</script>
@endpush
