@extends('layout')


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
@section('content')
    @php
        use App\Models\Region;
        $provinces = Region::all();

        // Province options for cloning new rows
        $provinceOptions = '<option value="">Select Province</option>';
        foreach ($provinces as $province) {
            $provinceOptions .= '<option value="' . $province->region_name . '">' . $province->full_form . '</option>';
        }
        $maxPlates = 10;
    @endphp

    <div class="container py-4">
        <h2 class="mb-4 text-center">Multiple License Plates</h2>

        <form action="{{ route('multiplates.store') }}" method="POST">
            @csrf
            <div id="plates-container">
                <!-- First plate row -->
                <div class="row g-3 plate-row align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Province</label>
                        <select name="province[]" class="form-select province-select">
                            <option value="">Select Province</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->region_name }}">{{ $province->full_form }}</option>
                            @endforeach
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
                        <input type="text" name="price[]" class="form-control" placeholder="123">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status[]" class="form-select">
                            <option value="Available">Available</option>
                            <option value="Pending">Pending</option>
                            <option value="Sold">Sold</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="button" id="add-plate" class="btn btn-primary">+ Add Another Plate</button>
                <button type="submit" class="btn btn-danger">Submit Multiple Plates</button>
            </div>
        </form>
    </div>

    {{-- jQuery + Select2 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script>
        const provinceOptions = `{!! $provinceOptions !!}`;
        const maxPlates = {{ $maxPlates }};



        // 🔹 Update cities when province changes
        function updateCityDropdown(provinceSelect) {
            const province = provinceSelect.value;
            const $citySelect = $(provinceSelect).closest('.plate-row').find('.city-select');
            $citySelect.empty().append('<option value="">Select City</option>');

            if (!province) return;

            $.ajax({
                url: "{{ url('getCities') }}",
                data: {
                    province: province
                },
                type: 'GET',
                success: function(cities) {
                    cities.cities.forEach(city => {
                        const option = new Option(city.city_name, city.city_name, false, false);
                        $citySelect.append(option);
                    });

                    // Refresh Select2 dropdown
                    $citySelect.trigger('change');
                }
            });
        }
  

        $(document).ready(function() {
            // Init first row





            // Province change listener
            $(document).on('change', '.province-select', function() {
                updateCityDropdown(this);
            });

            // Add new plate row
            $('#add-plate').on('click', function() {
                const container = $('#plates-container');
                const count = container.find('.plate-row').length;

                if (count >= maxPlates) {
                    alert(`You can only add up to ${maxPlates} plates.`);
                    return;
                }

                const newRow = $(`
                    <div class="row g-3 plate-row align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Province</label>
                            <select name="province[]" class="form-select province-select">
                                ${provinceOptions}
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
                            <input type="text" name="price[]" class="form-control" placeholder="123">
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
                    </div>
                `);

                container.append(newRow);
                initSelect2(newRow); // reinit Select2
            });



            // Remove plate row
            $(document).on('click', '.remove-plate', function() {
                $(this).closest('.plate-row').remove();
            });
        });
    </script>
@endsection
