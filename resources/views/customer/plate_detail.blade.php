@extends('layout')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">License Plate</h2>

                    <div class="mb-3 text-center">
                        <div class="display-6 fw-bold">{{ $plate->plate_number }}</div>
                        <span class="badge bg-primary">{{ $plate->region }}</span>
                        <span class="badge bg-secondary">{{ $plate->city }}</span>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Price</div>
                        <div class="col-md-8 fw-semibold">{{ number_format($plate->price) }} PKR</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Status</div>
                        <div class="col-md-8 fw-semibold">
                            @if($plate->is_sold)
                                <span class="text-danger">Sold</span>
                            @else
                                <span class="text-success">Available</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Posted By</div>
                        <div class="col-md-8">{{ $plate->user->name ?? 'Unknown' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Posted On</div>
                        <div class="col-md-8">{{ $plate->created_at->format('d M, Y') }}</div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <a href="{{ url('plates') }}" class="btn btn-outline-secondary">⬅ Back to Listings</a>

                        @auth
                            @if(!$plate->is_sold && Auth::id() !== $plate->user_id)
                                <a href="{{ url('plates_buy/', $plate->id) }}" class="btn btn-success">Buy Now</a>
                            @endif
                        @endauth
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
