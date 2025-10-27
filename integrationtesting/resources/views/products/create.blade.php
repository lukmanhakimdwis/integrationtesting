
@extends('layouts.app')
@section('content')
<h1 class="h4 mb-3">Tambah Produk</h1>
<form action="{{ route('products.store') }}" method="post" class="card p-3">
  @csrf
  <div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Harga</label>
    <input type="number" name="price" value="{{ old('price') }}" class="form-control" min="0" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Stok</label>
    <input type="number" name="stok" value="{{ old('stok', 0) }}" class="form-control" min="0" required>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button class="btn btn-primary">Simpan</button>
  </div>
</form>
@endsection
