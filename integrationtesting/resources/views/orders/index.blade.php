
@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Orders</h1>
  <a href="{{ route('orders.create') }}" class="btn btn-primary">Tambah Order</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Produk</th>
            <th class="text-end">Qty</th>
            <th>Status</th>
            <th>TRX</th>
            <th>Waktu</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($orders as $o)
          <tr>
            <td>#{{ $o->id }}</td>
            <td>{{ $o->product?->name }}</td>
            <td class="text-end">{{ $o->qty }}</td>
            <td>
              <span class="badge {{ $o->status === 'paid' ? 'bg-success' : 'bg-secondary' }}">{{ $o->status }}</span>
            </td>
            <td><code>{{ $o->trx_id ?? '-' }}</code></td>
            <td>{{ $o->created_at->format('d-m-Y H:i') }}</td>
            <td class="text-end">
              <a href="{{ route('orders.edit', $o) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
              <form action="{{ route('orders.destroy', $o) }}" method="post" class="d-inline-block" onsubmit="return confirm('Hapus order? Stok akan dipulihkan jika sudah paid.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted py-4">Belum ada order</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">
    {{ $orders->links() }}
  </div>
</div>
@endsection
