@extends('layout')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">Multiple License Plates</h2>

        <form {{ route('multiplates.store') }} method="POST">
            @csrf
            <div id="plates-container">
                <!-- First plate row -->
                <div class="row g-3 plate-row align-items-end mb-3">
                    {{-- Province --}}
                    <div class="col-md-3">
                        <label class="form-label">Province</label>
                        <select name="province[]" class="form-select province-select">
                            <option value="">Select Province</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Sindh">Sindh</option>
                            <option value="Balochistan">Balochistan</option>
                            <option value="KPK">Khyber Pakhtunkhwa</option>

                        </select>
                    </div>

                    {{-- City --}}
                    <div class="col-md-2">
                        <label class="form-label">City</label>
                        <select name="city[]" class="form-select city-select">
                            <option value="">Select City</option>
                        </select>
                    </div>


                    {{-- Plate Number --}}
                    <div class="col-md-2">
                        <label class="form-label">Plate Number</label>
                        <input type="text" name="plate_number[]" class="form-control" placeholder="ABC-123">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Price</label>
                        <input type="text" name="price[]" class="form-control" placeholder="ABC-123">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status[]" class="form-select city-select">
                            @foreach (['Available', 'Pending', 'Sold'] as $region)
                                <option value="{{ $region }}" {{ old('status') == $region ? 'selected' : '' }}>
                                    {{ $region }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Remove Button --}}

                </div>
            </div>

            <div class="mt-3">

                <button type="button" id="add-plate" class="btn btn-primary">+ Add Another Plate</button>
                <button type="submit" class="btn btn-danger">Submit Multiple Plates</button>
            </div>



        </form>
    </div>



    <script>
        const provinceCities = {
            Punjab: ["Lahore", "Faisalabad", "Multan", "Rawalpindi", "Gujranwala"],
            Sindh: ["Karachi", "Hyderabad", "Sukkur", "Larkana", "Mirpur Khas"],
            Balochistan: ["Quetta", "Gwadar", "Turbat", "Khuzdar", "Zhob"],
            KPK: ["Peshawar", "Abbottabad", "Swat", "Kohat", "Mardan"]

        };

        // Function to update city dropdown for a given row
        function updateCityDropdown(provinceSelect) {
            const province = provinceSelect.value;
            const citySelect = provinceSelect.closest('.plate-row').querySelector('.city-select');
            citySelect.innerHTML = '<option value="">Select City</option>';

            if (province && provinceCities[province]) {
                provinceCities[province].forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.toLowerCase();
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
            }
        }

        // Handle province change for all existing & future rows
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('province-select')) {
                updateCityDropdown(e.target);
            }
        });

        // Add new plate row
        document.getElementById('add-plate').addEventListener('click', function() {
            const container = document.getElementById('plates-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'plate-row', 'align-items-end', 'mb-3');
            newRow.innerHTML = `
            <div class="col-md-3">
                <label class="form-label">Province</label>
                <select name="province[]" class="form-select province-select">
                    <option value="">Select Province</option>
                    <option value="Punjab">Punjab</option>
                    <option value="Sindh">Sindh</option>
                    <option value="Balochistan">Balochistan</option>
                    <option value="KPK">Khyber Pakhtunkhwa</option>
             
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">City</label>
                <select name="city[]" class="form-select city-select">
                    <option value="">Select City</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Plate Number</label>
                <input type="text" name="plate_number[]" class="form-control" placeholder="ABC-123">
            </div>
             <div class="col-md-2">
                <label class="form-label">Price</label>
                <input type="text" name="price[]" class="form-control" placeholder="ABC-123">
            </div>
               <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status[]" class="form-select">
                    <option value="Available">Available</option>
                    <option value="Pending">Pending</option>
                    <option value="Sold">Sold</option>
                 
                </select>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-danger remove-plate">&times;</button>
            </div>
        `;
            container.appendChild(newRow);
        });

        // Remove plate row
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-plate')) {
                e.target.closest('.plate-row').remove();
            }
        });
    </script>
@endsection
