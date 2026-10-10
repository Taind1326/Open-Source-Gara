@if(session('success'))
    <div class="alert alert-ok" role="status">{{ session('success') }}</div>
@endif

@if($errors->has('error'))
    <div class="alert alert-err" role="alert">{{ $errors->first('error') }}</div>
@endif
