@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Ajouter Approvisionnement</h5>
    <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
            Importer un fichier excel
        </button>
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <form method="post" action="{{route('file-import-approv')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Ajouter un stock</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                        <div>
                            <label for="">importer un fichier excel</label>
                            <input type="file"  name="file" id="">
                        </div> <br> <br> <br>
                        <a class="btn btn-success" href="{{ route('file-export-approv') }}">exporter un model</a>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      <form method="post" action="{{route('approvisionnement.store')}}">
        {{csrf_field()}}

        {{-- {{$parent_cats}} --}}
        {{-- {{dd($products)}} --}}
        <div class="form-group" id='parent_ct_div'>
          <label for="parent_id"> Produits</label>
          <select name="product_id" class="form-control">
              <option value="">--Selectionner un Produit--</option>
              @foreach($products as $key=>$product)
                  <option value='{{$product->id}}'>{{$product->title}}</option>
              @endforeach
          </select>
        </div>
        <div class="form-group" id='parent_ct_div'>
            <label for="parent_id"> Boutiques</label>
            <select name="boutique_id" class="form-control">
                <option value="">--Selectionner une Boutique--</option>
                @foreach($boutiques as $key=>$product)
                    <option value='{{$product->id}}'>{{$product->libelle}}</option>
                @endforeach
            </select>
          </div>
        <div class="form-group">
            <label for="inputTitle" class="col-form-label">Quantité <span class="text-danger">*</span></label>
            <input id="inputTitle" type="text" name="stock" placeholder="Entrer quantité"  value="{{old('stock')}}" class="form-control">
            @error('stock')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>


        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Enregistrer</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
      $('#summary').summernote({
        placeholder: "Write short description.....",
          tabsize: 2,
          height: 120
      });
    });
</script>

@endpush
