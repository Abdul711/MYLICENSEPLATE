@extends('layout')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow border-0 rounded-4">
                    <h2 class="text-center mb-4">License Plate</h2>
                    @foreach ($plates as $plate)
                        <div class="card-body p-4">


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
                                    @if ($plate->status == 'Sold')
                                        <span class=" badge bg-danger rounded-pill ">Sold</span>
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

                            @auth
                                @if (!$plate->is_sold && Auth::id() !== $plate->user_id)
                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Contact Seller</div>
                                        <div class="col-md-8">
                                            <a href="{{ url('contact_seller/', $plate->user_id) }}" class="btn btn-success">
                                                Contact Now</a>
                                        </div>
                                    </div>



                                    <div class="row mb-3">
                                        <div class="col-md-4 text-muted">Contact Number</div>
                                        <div class="col-md-8">{{ $plate->user->mobile ?? 'No Contact provided.' }}</div>
                                    </div>
                                @endif
                            @endauth
                @php
                        
                             $provinceLogos = [
            'Punjab'      => asset('glogo/punjab.jpeg'),
            'Sindh'       => asset('glogo/sindh.png'),
            'KPK'         => asset('glogo/KP_logo.png'),
            'Balochistan' => asset('glogo/balochistan.jpeg'),
        ];

        $provinceLogo = $provinceLogos[$plate->region] ?? null;
        @endphp
                            <hr>
                                @include("plates.plate_template",compact("plate","provinceLogo"))
                             <a href="{{ url('licenseplatedownload/'.$plate->id) }}" class="btn btn-outline-secondary mt-1">Download Image</a>
          
 
 
  <a href="{{ url('challandownload/'.$plate->id) }}" class="btn btn-outline-secondary mt-1">Download Challan</a>

                            <div class="text-center">
                               
                                @auth
                                    @if (!$plate->is_sold && Auth::id() !== $plate->user_id)
                                        <a href="{{ url('plates_buy/', $plate->id) }}" class="btn btn-success">Buy Now</a>
                                    @endif
                                @endauth
                            </div>

                        </div>
                    @endforeach
                     <a href="{{ url('licenseplate') }}" class="btn btn-outline-secondary m-1 text-center">⬅ Back to Listings</a>

                </div>

            </div>
        </div>
    </div>
@endsection
