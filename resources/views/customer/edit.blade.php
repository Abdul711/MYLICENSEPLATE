@extends('layout')

@section('content')
<div class="container">

    <h2>Edit Plate</h2>

    {{-- Show validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('items.update', $item->id) }}">
        @csrf
        @method('PUT')

        {{-- Province --}}
      

        {{-- City --}}


        {{-- Plate Number --}}
        <div class="mb-3">
            <label>Plate Number</label>
            <input type="text" name="plate_number" class="form-control" 
                   value="{{ old('plate_number', $item->plate_number) }}">
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
                <option value="Available" {{ old('status', $item->status) == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Sold" {{ old('status', $item->status) == 'Sold' ? 'selected' : '' }}>Sold</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>

</div>

{{-- Province-City Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Province → Cities list
    const cityOptions = {
        'Punjab': ['Lahore', 'Faisalabad', 'Multan', 'Rawalpindi', 'Gujranwala', 'Sialkot', 'Bahawalpur', 'Sargodha'],
        'Balochistan': ['Quetta', 'Khuzdar', 'Turbat', 'Zhob', 'Gwadar', 'Loralai', 'Sibi'],
        'Sindh': ['Karachi', 'Hyderabad', 'Sukkur', 'Mirpurkhas', 'Larkana', 'Nawabshah', 'Thatta'],
        'KPK': ['Peshawar', 'Abbottabad', 'Mardan', 'Swat', 'Bannu', 'Dera Ismail Khan', 'Charsadda'],
        'Islamabad': ['Islamabad']
    };

    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');

    // Values coming from Blade (either old() or $item)
    const oldProvince = @json(old('province', $item->province));
    const oldCity = @json(old('city', $item->city));

    // Function to populate cities dropdown
    function populateCities(province, selectedCity = null) {
        const cities = cityOptions[province] || [];
        citySelect.innerHTML = '<option value="">Select City</option>';
        cities.forEach(function (city) {
            const option = document.createElement('option');
            option.value = city;
            option.text = city;
            if (city === selectedCity) {
                option.selected = true;
            }
            citySelect.appendChild(option);
        });
    }

    // Populate cities when page loads (edit mode)
    if (oldProvince) {
        populateCities(oldProvince, oldCity);
    }

    // Populate cities when province changes
    provinceSelect.addEventListener('change', function () {
        populateCities(this.value);
    });

});
</script>
@endsection
