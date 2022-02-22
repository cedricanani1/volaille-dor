@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Category</h5>
    <div class="card-body">
      <form method="post" action="{{route('boutique.update',$boutique->id)}}">
        @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">libellé <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="libelle" placeholder="Enter libelle"  value="{{$boutique->libelle}}" class="form-control">
          @error('libelle')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group" id='parent_ct_div'>
            <label for="parent_id"> Lieu <span class="text-danger">*</span></label></label>
            <select name="shipping_id" class="form-control">
                <option value="">--Selectionner un lieu--</option>
                @foreach($shippings as $key=>$shipping)
                @if ($shipping->id === $boutique->shipping_id)
                    <option value='{{$shipping->id}}' selected>{{$shipping->type}}</option>
                @else
                    <option value='{{$shipping->id}}'>{{$shipping->type}}</option>
                @endif

                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
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
