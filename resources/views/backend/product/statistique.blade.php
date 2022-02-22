@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Statistique</h5>
    <div class="card-body">
      <form method="post" action="{{route('product.stat.post')}}" id="idform">
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
       @php
        $sum=0;
            foreach ($boutiques as $key => $value) {
                $sum += $value->costTotal;
            }
       @endphp

        {{-- {{dd($sum)}} --}}
        {{-- @if (empty($orders)) --}}
            <div class="card-body">
                <div class="table-responsive">
                <canvas id="myChart" style="display: none"></canvas>
                @if(count($boutiques)>0)
                <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Chiffre d'affaire</th>
                        {{-- <th>Action</th> --}}
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th></th>
                        <th></th>
                        <th>{{ $sum }}</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach($boutiques as $key => $boutique)
                        <tr>
                            <td>{{$key +1}}</td>
                            <td>{{$boutique->title}}</td>
                            <td>
                                {{$boutique->counter}}
                            </td>
                            <td>{{$boutique->costTotal}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                
                {{-- <span style="float:right">{{$boutiques->links()}}</span> --}}
                @else
                    <h6 class="text-center">Aucune vente de produit effectuée a cette période</h6>
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
<script>
    const labels = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    ];
    
</script>
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
         url:'/getmsg',
         headers: {
                    'X-CSRF-Token': token 
               },
        //  data: { CSRF: getCSRFTokenValue()},
         data:{'to':to,'from': from,'boutiqueId':boutiqueId},
         success:function(result) {
            // $("#msg").html(data.boutiques);
            document.getElementById("myChart").style.display = "block";
            console.log(result)
            const data = {
                labels: result.labels,
                datasets: [{
                    label: 'My First dataset',
                    backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(54, 162, 200)',
                    'rgb(25, 105, 86)',
                    'rgb(54, 162, 25)',
                    ],
                    data: result.datas,
                }]
            };
            const config = {
            type: 'pie',
            data: data,
            options: {}
            };

            
            

            const myChart = new Chart(
                document.getElementById('myChart'),
                config
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
