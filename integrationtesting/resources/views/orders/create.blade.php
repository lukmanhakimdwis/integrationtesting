
@extends('layouts.app')
@section('content')
<h1 class="h4 mb-3">Tambah Order</h1>
<form action="{{ route('orders.store') }}" method="post" class="card p-3">
  @csrf
  <div class="mb-3">
    <label class="form-label">Produk</label>
    <select name="product_id" class="form-select" required>
      <option value="" disabled selected>Pilih produk...</option>
      @foreach($products as $p)
        <option value="{{ $p->id }}">{{ $p->name }} — Rp {{ number_format($p->price,0,',','.') }} (stok: {{ $p->stok }})</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Qty</label>
    <input type="number" name="qty" value="{{ old('qty', 1) }}" class="form-control" min="1" required>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button class="btn btn-primary">Buat & Bayar</button>
  </div>
</form>
@endsection
