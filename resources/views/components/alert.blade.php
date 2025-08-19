@if(session('error') || session('sukses'))
    <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} alert-dismissible fade show" role="alert">
        {{ session('error') ?? session('sukses') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif