@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Modifier Zone de couverture</h5>
    <div class="card-body">
      <form method="post" action="{{route('zonecouverture.update',$boutique->id)}}">
        @csrf
        @method('PATCH')
        {{-- {{$parent_cats}} --}}
        {{-- {{dd($products)}} --}}
        <div class="form-group" id='parent_ct_div'>
            <label for="parent_id"> Boutique</label>
            <select name="boutique_id" class="form-control">
                    <option value='{{$boutique->id}}'>{{$boutique->libelle}}</option>
            </select>
          </div>

            <div class="form-group" id='parent_ct_div'>
                <label for="parent_id"> Zone</label>
                <select name="shipping_id[]" value="1" class="form-control selectpicker" multiple>
                    <option value="">--Selectionner une zone--</option>
                    @foreach($zones as $key=>$zone)
                        @if (in_array($zone->id,$tab))
                            <option value='{{$zone->id}}' selected>{{$zone->type}}</option>
                        @else
                            <option value='{{$zone->id}}' >{{$zone->type}}</option>
                        @endif

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

@endpush

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
</script>
<script>
  $('#is_parent').change(function(){
    var is_checked=$('#is_parent').prop('checked');
    // alert(is_checked);
    if(is_checked){
      $('#parent_cat_div').addClass('d-none');
      $('#parent_cat_div').val('');
    }
    else{
      $('#parent_cat_div').removeClass('d-none');
    }
  })
</script>
@endpush
