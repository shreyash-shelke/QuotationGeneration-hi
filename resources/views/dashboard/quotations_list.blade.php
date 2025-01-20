<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotations List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Quotations List</h1>

        <!-- Search Form -->
        <form method="GET" action="{{ route('quotations.list') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by phone or name" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Quotations Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Quotation Number</th>
                    <th>Description</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quotations as $quotation)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $quotation->quotation_number }}</td>
                        <td>{{ $quotation->description }}</td>
                        <td>{{ $quotation->phone }}</td>
                        <td>₹{{ number_format($quotation->amount, 2) }}</td>
                        <td>{{ $quotation->created_at->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('quotation.view', $quotation->id) }}" class="btn btn-info btn-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No quotations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        {{ $quotations->links('pagination::bootstrap-5') }}
    </div>
</body>
</html>
