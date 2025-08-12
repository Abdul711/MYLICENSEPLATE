@extends('layout')

@section('content')
    <div class="container d-flex justify-content-center py-4">
        <div style="max-width: 800px; width: 100%;">
            <h2 class="mb-4 text-center">Edit Multiple License Plates</h2>

            <form action="{{ url('updateMultiple') }}" method="POST">
                @csrf

                <div id="plates-container">
                    @foreach ($plates as $index => $plate)
                        <div class="row g-3 plate-row align-items-end mb-3" data-index="{{ $index }}">
                            <input type="hidden" name="id[]" value="{{ $plate['id'] }}">

                            {{-- Plate Number --}}
                            <div class="col-md-2">
                                <label class="form-label">Plate Number</label>
                                <input type="text" name="plate_number[]" class="form-control"
                                    value="{{ $plate['plate_number'] }}" placeholder="ABC-123" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Province</label>
                                <select name="province[]" class="form-select province-select">
                                    <option value="">Select Province</option>

                                    @foreach ($regions as $region)
                                        @if ($region->region_name == 'KPK')
                                            <option value="{{ $region->region_name }}"
                                                {{ $plate->region == $region->region_name ? 'selected' : '' }}>
                                                {{ $region->full_form }}</option>
                                        @else
                                            <option value="{{ $region->region_name }}"
                                                {{ $plate->region == $region->region_name ? 'selected' : '' }}>
                                                {{ $region->region_name }}</option>
                                        @endif
                                    @endforeach


                                </select>
                            </div>

                            {{-- City --}}
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <select name="city[]" class="form-select city-select">
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->city_name }}"
                                    {{ $plate->city == $city->city_name ? 'selected' : '' }}          
                                            
                                            >{{ $city->city_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Price --}}
                            <div class="col-md-2">
                                <label class="form-label">Price</label>
                                <input type="number" name="price[]" class="form-control" value="{{ $plate['price'] }}"
                                    placeholder="Price" min="0" required>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status[]" class="form-select" required>
                                    @foreach (['Available', 'Pending', 'Sold'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $plate['status'] === $status ? 'selected' : '' }}>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateCityDropdown(provinceSelect) {
            const province = provinceSelect.value;
            const citySelect = provinceSelect.closest('.plate-row').querySelector('.city-select');
            citySelect.innerHTML = '<option value="">Select City</option>';


            $.ajax({
                url: "{{ url('getCities') }}",
                data: {
                    province: province
                },
                type: 'GET',
                success: function(cities) {
                    console.log(cities);
                    cities.cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_name;
                        option.textContent = city.city_name;
                        citySelect.appendChild(option);
                    });

                }
            });

            // if (province && provinceCities[province]) {
            //     provinceCities[province].forEach(city => {
            //         const option = document.createElement('option');
            //         option.value = city.toLowerCase();
            //         option.textContent = city;
            //         citySelect.appendChild(option);
            //     });
            // }
        }

        // Handle province change for all existing & future rows
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('province-select')) {
                updateCityDropdown(e.target);
            }
        });
    </script>
@endsection
