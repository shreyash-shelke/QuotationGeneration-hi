@extends('layouts.app') <!-- Replace 'layouts.app' with the correct layout if different -->

@section('title', 'Quotation Details') <!-- Title for the page -->

@section('content')
<div class="container">
    <h1>Quotation Details</h1>
    <p>Below are the details of the selected quotation.</p>

    <!-- Example of displaying quotation details -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
   
        <tbody>
    @if($quotation && $quotation->items)
        @foreach($quotation->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ $item->price }}</td>
                <td>${{ $item->quantity * $item->price }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="6" class="text-center">No items found for this quotation.</td>
        </tr>
    @endif
</tbody>

    </table>

    <a href="{{ route('quotations.list') }}" class="btn btn-primary">Back to All Quotations</a>
</div>
@endsection
