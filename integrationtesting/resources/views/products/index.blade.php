
@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Produk</h1>
  <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr>
            <th>Nama</th>
            <th class="text-end">Harga</th>
            <th class="text-end">Stok</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($products as $p)
          <tr>
            <td>{{ $p->name }}</td>
            <td class="text-end">Rp {{ number_format($p->price,0,',','.') }}</td>
            <td class="text-end">{{ $p->stok }}</td>
            <td class="text-end">
              <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
              <form action="{{ route('products.destroy', $p) }}" method="post" class="d-inline-block" onsubmit="return confirm('Yakin hapus produk?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
              <form action="{{ route('orders.store') }}" method="post" class="d-inline-flex gap-2 ms-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $p->id }}">
                <input type="number" class="form-control form-control-sm" name="qty" min="1" value="1" style="width:90px">
                <button class="btn btn-sm btn-success">Beli</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted py-4">Belum ada produk</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">
    {{ $products->links() }}
  </div>
</div>
@endsection
