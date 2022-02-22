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
      <h6 class="m-0 font-weight-bold text-primary float-left">Liste des approvisionnements</h6>
      <a href="{{route('approvisionnement.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Ajouter une categorie"><i class="fas fa-plus"></i> Ajouter un approvisionnement</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">

        @if(count($boutiques)>0)
        <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>#</th>
              <th>Produit</th>
              <th>Details</th>
              <th>Quantité distribué</th>
              <th>Quantité vendu</th>
              {{-- <th>Action</th> --}}
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>#</th>
              <th>Produit</th>
              <th>Details</th>
              <th>Quantité distribué</th>
              <th>Quantité vendu</th>
              {{-- <th>Action</th> --}}
            </tr>
          </tfoot>
          <tbody>
            @foreach($boutiques as $key => $approvisionnement)
            {{-- {{dd($approvisionnement->boutique)}} --}}
              @php
                  $quantity_init_count = 0 ;
                  $quantity_count = 0 ;
                  foreach ($approvisionnement->boutique as  $value) {
                    $quantity_init_count +=$value->pivot->quantity_init;
                    $quantity_count +=$value->pivot->quantity;
                  }
              @endphp
                <tr>
                    <td>{{$key +1}}</td>
                    <td>{{$approvisionnement->title}}</td>
                    <td>
                        <table>
                            <thead>
                                <tr>
                                  <th>Boutiques</th>
                                  <th>Quantité total reçu</th>
                                  <th>Quantité restante</th>
                                  <th>Date</th>
                                </tr>
                              </thead>
                            @foreach ($approvisionnement->boutique as $appro )
                            <tr>
                                <td>{{$appro->libelle}}</td>
                                <td>{{$appro->pivot->quantity_init}}</td>
                                <td>{{$appro->pivot->quantity}}</td>
                                <td>{{$appro->pivot->created_at}}</td>
                              </tr>
                            @endforeach

                          </table>
                    </td>
                    <td>{{$quantity_init_count}}</td>
                    <td>{{$quantity_init_count-$quantity_count}}</td>

                </tr>
            @endforeach
          </tbody>
        </table>
        {{-- <span style="float:right">{{$products->links()}}</span> --}}
        @else
          <h6 class="text-center">Aucune approvisionnement trouvée !!! Veuillez créer un approvisionnement</h6>
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

      $('#banner-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[3,4,5]
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
