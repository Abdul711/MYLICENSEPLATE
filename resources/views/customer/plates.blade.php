@extends('layout')

@section('content')
    <div class="container py-4">
        <h2 class="text-center mb-4">Available Plates</h2>
        <h2 class="text-center mb-4"> {{ $plates->count() }} Plates Found</h2>
        <div class="row mb-4">
            <div class="col-md-12">
                <form action="{{ url('plates') }}" method="GET" class="d-flex justify-content-end">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" name="start_with" class="form-control" placeholder="Start with"
                                value="{{ request('start_with') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="contain" class="form-control" placeholder="Contain"
                                value="{{ request('contain') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="end_with" class="form-control" placeholder="End with"
                                value="{{ request('end_with') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="length" class="form-control" placeholder="Length"
                                value="{{ request('length') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="max_price" class="form-control" placeholder="Max Price"
                                value="{{ request('max_price') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="min_price" class="form-control" placeholder="Min Price"
                                value="{{ request('min_price') }}">
                        </div>

                        <div class="col-md-2">
                            <select name="region" id="province" class="form-select mt-1">
                                <option value="" disabled {{ request('region') ? '' : 'selected' }}>Select Province
                                </option>

                                @foreach ($regions as $region)
                                    <option value="{{ $region->region }}"
                                        {{ request('region') == $region->region ? 'selected' : '' }}>
                                        {{ $region->region }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="city" id="city"  class="form-select mt-1">
                                <option value="" disabled {{ request('city') ? '' : 'selected' }}>Select City
                                </option>

                                @foreach ($cities as $city)
                                    <option value="{{ $city->city }}"
                                        {{ request('city') == $city->city? 'selected' : '' }}>
                                        {{ $city->city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100 mt-1">Search</button>

                        </div>

                    </div>



                </form>
                @if (request()->has('start_with') ||
                        request()->has('contain') ||
                        request()->has('end_with') ||
                        request()->has('length') ||
                        request()->has('max_price') ||
                        request()->has('min_price'))
                    <p class="mb-0">
                        @if (!empty(request()->all()))
                            Current search:
                        @endif
                        @if (request()->has('start_with') && request('start_with') != '')
                            <span class="btn-simple btn py-0 px-2">#start_with : {{ request('start_with') }}</span>
                        @endif
                        @if (request()->has('contain') && request('contain') != '')
                            <span class="btn-simple btn py-0 px-2">#contain : {{ request('contain') }}</span>
                        @endif
                        @if (request()->has('end_with') && request('end_with') != '')
                            <span class="btn-simple btn py-0 px-2">#end_with : {{ request('end_with') }}</span>
                        @endif
                        @if (request()->has('length') && request('length') != '')
                            <span class="btn-simple btn py-0 px-2">#length : {{ request('length') }}</span>
                        @endif
                        @if (request()->has('max_price') && request('max_price') != '')
                            <span class="btn-simple btn py-0 px-2">#max_price : {{ request('max_price') }}</span>
                        @endif
                        @if (request()->has('min_price') && request('min_price') != '')
                            <span class="btn-simple btn py-0 px-2">#min_price : {{ request('min_price') }}</span>
                        @endif
                        @if (request()->has('region') && request('region') != '')
                            <span class="btn-simple btn py-0 px-2">#province : {{ request('region') }}</span>
                        @endif

                        @if (request()->has('city') && request('city') != '')
                            <span class="btn-simple btn py-0 px-2">#city : {{ request('city') }}</span>
                        @endif
                        @if (!empty(request()->all()))
                            <a class="btn-danger btn py-0 px-2" href="{{ url('plates') }}"><i class="fa fa-trash"
                                    aria-hidden="true"></i>Cancel</a>
                        @endif
                    </p>
                @endif


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
                                    <div style="font-size: 2rem; letter-spacing: 5px; color: white;">
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
                            <a href="{{ url('plates/' . $plate->id . '/show') }}" class="btn btn-primary">View Detail</a>
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
                                    <div style="font-size: 2rem; letter-spacing: 5px; color: white;">
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
                            <a href="{{ url('plates/' . $plate->id . '/show') }}" class="btn btn-primary">View Detail</a>
                            @auth
                                @if ($plate->user_id != Auth::user()->id)
                                    <a href="" class="btn btn-danger">Order</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                    @if ($plate->region == 'Balochistan')
                        <div class="col-md-3" >
                              
                            <div
                                class="d-flex flex-column justify-content-between border border-primary bg-danger rounded p-3 shadow">
                                <div class="text-center">
                                    <div class="fw-bold" style="font-size: 1rem;">{{ strtoupper($plate->region) }}</div>
                                    <div style="font-size: 2rem; letter-spacing: 5px; color: white;">
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
                            <a href="{{ url('plates/' . $plate->id . '/show') }}" class="btn btn-primary">View Detail</a>
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
                                    <div style="font-size: 2rem; letter-spacing: 5px; color: white;">
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
                            <a href="{{ url('plates/' . $plate->id . '/show') }}" class="btn btn-primary">View Detail</a>
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
        <script>
    const cityOptions = {
        'Punjab': ['Lahore', 'Faisalabad', 'Multan', 'Rawalpindi', 'Gujranwala','Sialkot', 'Bahawalpur', 'Sargodha'],
        'Balochistan': ['Quetta', 'Khuzdar', 'Turbat','Zhob','Gwadar','Loralai','Sibi'],
        'Sindh': ['Karachi', 'Hyderabad', 'Sukkur','Mirpurkhas','Larkana','Nawabshah','Thatta'],
        'KPK': ['Peshawar', 'Abbottabad', 'Mardan','Swat','Bannu','Dera Ismail Khan','Charsadda'],
        'Islamabad': ['Islamabad']
    };

    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');

    const oldProvince = "{{ old('region') }}";
    const oldCity = "{{ old('city') }}";

    function populateCities(province, selectedCity = null) {
        const cities = cityOptions[province] || [];
        citySelect.innerHTML = '<option value="">Select City</option>';
        cities.forEach(function(city) {
            const option = document.createElement('option');
            option.value = city;
            option.text = city;
            if (city === selectedCity) {
                option.selected = true;
            }
            citySelect.appendChild(option);
        });
    }

    // Auto-select old values on load
    if (oldProvince) {
        populateCities(oldProvince, oldCity);
    }

    // Change handler
    provinceSelect.addEventListener('change', function () {
        populateCities(this.value);
    });
</script>
    @endsection
