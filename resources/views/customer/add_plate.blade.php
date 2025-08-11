@extends('layout')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h4 class="mb-4 text-center">Add New License Plate</h4>
                        <form action="{{ url('plates_add') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Plate Number</label>
                                <input type="text" name="plate_number" class="form-control" placeholder="e.g., ABC-123">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">State / Region</label>
                                <select name="region" id="province" class="form-select">
                                    <option value="" disabled {{ old('region') ? '' : 'selected' }}>Select Province
                                    </option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->region_name }}"
                                            {{ old('region') == $region->region_name ? 'selected' : '' }}>
                                            {{ $region->full_form }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <select name="city" id="city" class="form-control">
                                    <option value="">Select City</option>
                                    <!-- Cities will be populated dynamically -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">


                                    @foreach (['Available', 'Pending', 'Sold'] as $region)
                                        <option value="{{ $region }}"
                                            {{ old('status') == $region ? 'selected' : '' }}>{{ $region }}</option>
                                    @endforeach







                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (PKR)</label>
                                <input type="number" name="price" class="form-control" placeholder="e.g., 15000">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Add Plate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const cityOptions = {
            'Punjab': ['Lahore', 'Faisalabad', 'Multan', 'Rawalpindi', 'Gujranwala', 'Sialkot', 'Bahawalpur',
                'Sargodha'],
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



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @error('plate_number')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops ! Something went wrong',
                text: '{{ $message }}'
            });
        </script>
    @enderror
    @error('region')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops ! Something went wrong',
                text: '{{ $message }}'
            });
        </script>
    @enderror
    @error('city')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops ! Something went wrong',
                text: '{{ $message }}'
            });
        </script>
    @enderror
    @error('status')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops ! Something went wrong',
                text: '{{ $message }}'
            });
        </script>
    @enderror
    @error('price')
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops ! Something went wrong',
                text: '{{ $message }}'
            });
        </script>
    @enderror

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    </body>

    </html>
@endsection
