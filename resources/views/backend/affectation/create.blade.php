@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Ajouter une affectation</h5>
    <div class="row">
        <div class="col-md-12">
           @include('backend.layouts.notification')
        </div>
    </div>
    <div class="card-body">
      <form method="post" action="{{route('affectation.store')}}">
        {{csrf_field()}}

        {{-- {{$parent_cats}} --}}
        {{-- {{dd($products)}} --}}

        <div class="form-group" id='parent_ct_div'>
            <label for="parent_id"> Boutiques</label>
            <select name="boutique_id" class="form-control">
                <option value="">--Selectionner une Boutique--</option>
                @foreach($boutiques as $key=>$product)
                    <option value='{{$product->id}}'>{{$product->libelle}}</option>
                @endforeach
            </select>
          </div>
          <div class="form-group" id='parent_ct_div'>
            <label for="parent_id">Gerant(e)</label>
            <select name="user_id[]" class="form-control">
                <option value="">--Selectionner un(e) gerant(e) --</option>
                @foreach($users as $key=>$user)
                    <option value='{{$user->id}}'>{{$user->name}}</option>
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
