@extends('layouts.app')

@section('title', 'Edit Aturan Fuzzy')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <x-alert/>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Aturan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rules.update', $rule->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label class="form-label">Jika Harga</label>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="harga_fuzzy">
                                    <option value="Murah" {{ $rule->harga_fuzzy == 'Murah' ? 'selected' : '' }}>Murah</option>
                                    <option value="Sedang" {{ $rule->harga_fuzzy == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="Mahal" {{ $rule->harga_fuzzy == 'Mahal' ? 'selected' : '' }}>Mahal</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">and Rating</label>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="rating_fuzzy">
                                    <option value="Rendah" {{ $rule->rating_fuzzy == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                    <option value="Sedang" {{ $rule->rating_fuzzy == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="Tinggi" {{ $rule->rating_fuzzy == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">maka</label>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="menu_id">
                                    @foreach($menus as $menu)
                                        <option value="{{ $menu->id }}" {{ $rule->menu_id == $menu->id ? 'selected' : '' }}>{{ $menu->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Perbarui Aturan
                                </button>
                                <a href="{{ route('rules.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection