@extends('layout')

@section('content')
    <div class="container py-4">
        <h2 class="text-center mb-4">Available Plates</h2>
        <h2 class="text-center mb-4"> {{ $plates->count() }} Plates Found</h2>
        <div class="row mb-4">
            <div class="col-md-12">
                <form action="{{ url('plates') }}" method="GET" class="d-flex justify-content-end">
                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Search by plate number or city">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <div class="row g-3">
                <div class="mb-3">
                    <a href="{{ route('plates.export') }}" class="btn btn-outline-secondary">Export Plates CSV</a>
                  
                </div>


                {{-- Loop through each plate --}}
                @foreach ($plates as $plate)
                    @if ($plate->region == 'Punjab')
                        <div class="col-md-3">
                            <div
                                class="d-flex flex-column justify-content-between border border-primary bg-primary rounded p-3 shadow">
                                <div class="text-center">
                                    <div class="fw-bold" style="font-size: 1rem;">{{ strtoupper($plate->region) }}</div>
                                    <div style="font-size: 2rem; letter-spacing: 5px;">
                                        {{ $plate->plate_number ?? 'ABC 000' }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-2 ">
                                    <div class="text-muted      ">{{ strtoupper($plate->city) }}</div>

                                </div>

                            </div>
                            <div class="fw-bold text-center">Owner: {{ $plate->user->name }}</div>
                            <div class="fw-bold text-center">Number: {{ $plate->user->mobile }}</div>
                            <div class="fw-bold text-center">PKR {{ $plate->price }}</div>
                            <a href="" class="btn btn-primary">View Detail</a>
                            @auth
                                @if ($plate->user_id != Auth::user()->id)
                                    <a href="" class="btn btn-danger">Order</a>
                                @endif

                            @endauth
                        </div>
                    @elseif ($plate->region == 'Sindh')
                        <div class="col-md-3">
                            <div
                                class="d-flex flex-column justify-content-between border border-primary bg-warning rounded p-3 shadow">
                                <div class="text-center">
                                    <div class="fw-bold" style="font-size: 1rem;">{{ strtoupper($plate->region) }}</div>
                                    <div style="font-size: 2rem; letter-spacing: 5px;">
                                        {{ $plate->plate_number ?? 'ABC 000' }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-2 ">
                                    <div class="text-muted      ">{{ strtoupper($plate->city) }}</div>

                                </div>

                            </div>
                            <div class="fw-bold text-center">Owner: {{ $plate->user->name }}</div>
                            <div class="fw-bold text-center">Number: {{ $plate->user->mobile }}</div>
                            <div class="fw-bold text-center">PKR {{ $plate->price }}</div>
                            <a href="" class="btn btn-primary">View Detail</a>
                            @auth
                                @if ($plate->user_id != Auth::user()->id)
                                    <a href="" class="btn btn-danger">Order</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                    @if ($plate->region == 'Balochistan')
                        <div class="col-md-3">
                            <div
                                class="d-flex flex-column justify-content-between border border-primary bg-danger rounded p-3 shadow">
                                <div class="text-center">
                                    <div class="fw-bold" style="font-size: 1rem;">{{ strtoupper($plate->region) }}</div>
                                    <div style="font-size: 2rem; letter-spacing: 5px;">
                                        {{ $plate->plate_number ?? 'ABC 000' }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-2 ">
                                    <div class="text-muted      ">{{ strtoupper($plate->city) }}</div>

                                </div>

                            </div>
                            <div class="fw-bold text-center">Owner: {{ $plate->user->name }}</div>
                            <div class="fw-bold text-center">Number: {{ $plate->user->mobile }}</div>
                            <div class="fw-bold text-center">PKR {{ $plate->price }}</div>
                            <a href="" class="btn btn-primary">View Detail</a>
                            @auth
                                @if ($plate->user_id != Auth::user()->id)
                                    <a href="" class="btn btn-danger">Order</a>
                                @endif
                            @endauth
                        </div>
                    @elseif ($plate->region == 'KPK')
                        <div class="col-md-3">
                            <div
                                class="d-flex flex-column justify-content-between border border-primary bg-info rounded p-3 shadow">
                                <div class="text-center">
                                    <div class="fw-bold" style="font-size: 1rem;">{{ strtoupper($plate->region) }}</div>
                                    <div style="font-size: 2rem; letter-spacing: 5px;">
                                        {{ $plate->plate_number ?? 'ABC 000' }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-2 ">
                                    <div class="text-muted      ">{{ strtoupper($plate->city) }}</div>

                                </div>

                            </div>
                            <div class="fw-bold text-center">Owner: {{ $plate->user->name }}</div>
                            <div class="fw-bold text-center">Number: {{ $plate->user->mobile }}</div>
                            <div class="fw-bold text-center">PKR {{ $plate->price }}</div>
                            <a href="" class="btn btn-primary">View Detail</a>
                            @auth
                                @if ($plate->user_id != Auth::user()->id)
                                    <a href="" class="btn btn-danger">Order</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endsection
