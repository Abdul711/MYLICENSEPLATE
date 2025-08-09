@extends('layout')

@section('content')
<div class="container d-flex justify-content-center py-4">
    <div style="max-width: 800px; width: 100%;">
        <h2 class="mb-4 text-center">Edit Multiple License Plates</h2>

        <form action="{{ url('updateMultiple') }}" method="POST">
            @csrf

            <div id="plates-container">
                @foreach($plates as $index => $plate)
                <div class="row g-3 plate-row align-items-end mb-3" data-index="{{ $index }}">
                    <input type="hidden" name="id[]" value="{{ $plate['id'] }}">

                    {{-- Plate Number --}}
                    <div class="col-md-5">
                        <label class="form-label">Plate Number</label>
                        <input type="text" name="plate_number[]" class="form-control" value="{{ $plate['plate_number'] }}" placeholder="ABC-123" required>
                    </div>

                    {{-- Price --}}
                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price[]" class="form-control" value="{{ $plate['price'] }}" placeholder="Price" min="0" required>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status[]" class="form-select" required>
                            @foreach (['Available', 'Pending', 'Sold'] as $status)
                                <option value="{{ $status }}" {{ $plate['status'] === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-3 text-center">
                <button type="submit" class="btn btn-danger px-5">Update Plates</button>
            </div>
        </form>
    </div>
</div>
@endsection
