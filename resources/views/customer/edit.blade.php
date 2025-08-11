@extends('layout')

@section('content')
    <div class="container">

        <h2>Edit Plate</h2>

        {{-- Show validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('items.update', $item->id) }}">
            @csrf
            @method('PUT')




            {{-- Plate Number --}}
            <div class="mb-3">
                <label>Plate Number</label>
                <input type="text" name="plate_number" class="form-control"
                    value="{{ old('plate_number', $item->plate_number) }}">
            </div>
        
            <div class="mb-3">
                <label class="form-label">State / Region</label>
                <select name="region" id="province" class="form-select">
                    <option value="" disabled {{ old('region') ? '' : 'selected' }}>Select Province
                    </option>
                    @foreach ($provinces as $region)
                        <option value="{{ $region->region }}" {{ $item->region == $region->region ? 'selected' : '' }}>


                            @if ($region->region == 'KPK')
                                Khyber Pakhtunkhwa
                            @else
                                {{ $region->region }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <select name="city" id="city" class="form-control">
                    <option value="">Select City</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->city }}" {{ $item->city == $city->city ? 'selected' : '' }}>
                            {{ $city->city }}</option>
                    @endforeach
                    <!-- Cities will be populated dynamically -->
                </select>
            </div>

            {{-- Price --}}
            <div class="mb-3">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control"
                    value="{{ old('price', $item->price) }}">
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Available" {{ old('status', $item->status) == 'Available' ? 'selected' : '' }}>Available
                    </option>
                    <option value="Sold" {{ old('status', $item->status) == 'Sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const cityOptions = {
            'Punjab': ['Lahore', 'Faisalabad', 'Multan', 'Rawalpindi', 'Gujranwala', 'Sialkot', 'Bahawalpur',
                'Sargodha'
            ],
            'Balochistan': ['Quetta', 'Khuzdar', 'Turbat', 'Zhob', 'Gwadar', 'Loralai', 'Sibi'],
            'Sindh': ['Karachi', 'Hyderabad', 'Sukkur', 'Mirpurkhas', 'Larkana', 'Nawabshah', 'Thatta'],
            'KPK': ['Peshawar', 'Abbottabad', 'Mardan', 'Swat', 'Bannu', 'Dera Ismail Khan', 'Charsadda'],
            'Islamabad': ['Islamabad']
        };

        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');

        const oldProvince = "{{ old('region') }}";
        const oldCity = "{{ old('city') }}";

        function populateCities(province, selectedCity = null) {
            const cities = cityOptions[province] || [];

            console.log(province);
            citySelect.innerHTML = '<option value="">Select City</option>';


            $.ajax({
                url: "{{ url('getCities') }}",
                data: {
                    province: province
                },
                type: 'GET',
                success: function(cities) {
                    console.log(cities);
                    cities.cities.forEach(function(city) {
                        const option = document.createElement('option');
                        option.value = city.city_name;
                        option.text = city.city_name;
                        if (city.city_name === selectedCity) {
                            option.selected = true;
                        }
                        citySelect.appendChild(option);
                    });
                }
            });



        }

        // Auto-select old values on load
        if (oldProvince) {
            populateCities(oldProvince, oldCity);
        }

        // Change handler
        provinceSelect.addEventListener('change', function() {
            populateCities(this.value);
        });
    </script>

    {{-- Province-City Script --}}
@endsection
