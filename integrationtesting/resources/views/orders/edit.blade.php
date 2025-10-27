
@extends('layouts.app')
@section('content')
<h1 class="h4 mb-3">Edit Order #{{ $order->id }}</h1>
<form action="{{ route('orders.update', $order) }}" method="post" class="card p-3">
  @csrf @method('PUT')
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3">
        <label class="form-label">Produk</label>
        <select name="product_id" class="form-select" {{ $order->status === 'paid' ? 'disabled' : '' }}>
          @foreach($products as $p)
            <option value="{{ $p->id }}" @selected(old('product_id', $order->product_id) == $p->id)>
              {{ $p->name }} — Rp {{ number_format($p->price,0,',','.') }} (stok: {{ $p->stok }})
            </option>
          @endforeach
        </select>
        @if($order->status === 'paid')
          <input type="hidden" name="product_id" value="{{ $order->product_id }}">
        @endif
      </div>
    </div>
    <div class="col-md-3">
      <div class="mb-3">
        <label class="form-label">Qty</label>
        <input type="number" name="qty" value="{{ old('qty', $order->qty) }}" class="form-control" min="1" {{ $order->status === 'paid' ? 'readonly' : '' }}>
      </div>
    </div>
    <div class="col-md-3">
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="pending" @selected(old('status', $order->status) === 'pending')>pending</option>
          <option value="paid" @selected(old('status', $order->status) === 'paid')>paid</option>
        </select>
      </div>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">TRX</label>
    <input type="text" class="form-control" value="{{ $order->trx_id ?? '-' }}" disabled>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button class="btn btn-primary">Update</button>
  </div>
</form>
@endsection
