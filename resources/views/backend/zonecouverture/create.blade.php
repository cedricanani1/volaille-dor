@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Ajouter Stock</h5>
    <div class="card-body">
      <form method="post" action="{{route('zonecouverture.store')}}">
        {{csrf_field()}}

        {{-- {{$parent_cats}} --}}
        {{-- {{dd($products)}} --}}
        <div class="form-group" id='parent_ct_div'>
          <label for="parent_id"> Boutique</label>
          <select name="boutique_id" class="form-control">
              <option value="">--Selectionner une Boutique--</option>
              @foreach($boutiques as $key=>$boutique)
                  <option value='{{$boutique->id}}'>{{$boutique->libelle}}</option>
              @endforeach
          </select>
        </div>
        <div class="form-group" id='parent_ct_div'>
            <label for="parent_id"> Zone</label>
            <select name="shipping_id[]" class="form-control selectpicker" multiple>
                <option value="">--Selectionner une zone--</option>
                @foreach($zones as $key=>$zone)
                    <option value='{{$zone->id}}'>{{$zone->type}}</option>
                @endforeach
            </select>
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
@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
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
