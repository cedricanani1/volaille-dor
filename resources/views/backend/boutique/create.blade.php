@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Ajouter une boutique</h5>
    <div class="card-body">
      <form method="post" action="{{route('boutique.store')}}">
        {{csrf_field()}}

        {{-- {{$parent_cats}} --}}
        {{-- {{dd($products)}} --}}
        <div class="form-group">
            <label for="inputTitle" class="col-form-label">Libellé <span class="text-danger">*</span></label>
            <input id="inputTitle" type="text" name="libelle" placeholder="Entrer le nom de la boutique"  value="{{old('libelle')}}" class="form-control">
            @error('libelle')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        {{-- <div class="form-group">
            <label for="inputTitle" class="col-form-label">Lieu <span class="text-danger">*</span></label>
            <input id="inputTitle" type="text" name="lieu" placeholder="Entrer le lieu"  value="{{old('lieu')}}" class="form-control">
            @error('lieu')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div> --}}
        <div class="form-group" id='parent_ct_div'>
            <label for="parent_id"> Lieu <span class="text-danger">*</span></label></label>
            <select name="shipping_id" class="form-control">
                <option value="">--Selectionner un lieu--</option>
                @foreach($shippings as $key=>$shipping)
                    <option value='{{$shipping->id}}'>{{$shipping->type}}</option>
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
