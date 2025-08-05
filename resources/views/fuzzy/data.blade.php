@extends('layouts.app')

@section('title', 'Data Fuzzy Input')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Data Input Harga Fuzzy</h2>
    <a href="{{ route('fuzzy.input') }}" class="btn btn-primary mb-3">Input Baru</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Harga</th>
                <th>Waktu Input</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>{{ $item->created_at->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
