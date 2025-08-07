@extends('layout')

@section('content')
    <div class="container py-4">
        <h2 class="text-center mb-4">Available Plates</h2>

        <div class="row g-3">
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
                            <a href="" class="btn btn-danger">Order</a>
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
                            <a href="" class="btn btn-danger">Order</a>
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
                            <a href="" class="btn btn-danger">Order</a>
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
                            <a href="" class="btn btn-danger">Order</a>
                        @endauth
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
