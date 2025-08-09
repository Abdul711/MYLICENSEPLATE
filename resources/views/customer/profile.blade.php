@extends('layout')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="https://i.pravatar.cc/100" class="rounded-circle me-3" width="80" height="80"
                                alt="Profile">
                            <div>
                                <h4 class="mb-0">{{ Auth::user()->name }}</h4>
                                <small class="text-muted">{{ Auth::user()->email }}</small>

                                <p> <small class="text-muted">{{ Auth::user()->mobile }}</small>

                            </div>
                        </div>
                        <hr>
                        <h5>My Plates</h5>
                        <h6>My Total Plates: {{ $myplates->count() }}</h6>

                        <table class="table table-striped table-bordered">
                                <button class="btn btn-outline-danger" id="showSelected">Delete All</button>
                               <button class="btn btn-outline-primary" id="editAll">Edit All</button>
                                 <button class="btn btn-outline-primary" id="viewAll">View All</button>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="form-check-input" id="selectAllPlates"></th>
                                    <th>Plate Number</th>
                                    <th>City</th>
                                    <th>Region</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($myplates as $plate)
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input" name="plates[]"
                                                value="{{ $plate->id }}"></td>
                                        <td>{{ $plate->plate_number }}</td>
                                        <td>{{ $plate->city }}</td>
                                        <td>{{ $plate->region }}</td>
                                        <td>
                                            @if ($plate->status == 'Sold')
                                                <span class="badge bg-danger rounded-pill">Sold</span>
                                            @elseif($plate->status == 'Pending')
                                                <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                            @else
                                                <span class="badge bg-success rounded-pill">Available</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary rounded-pill">{{ $plate->price }} PKR</span>
                                        </td>
                                        <td>
                                            <a href="{{ url('plates/' . $plate->id . '/show') }}"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="{{ url('plates/' . $plate->id . '/edit') }}"
                                                class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="{{ url('plates/' . $plate->id . '/delete') }}"
                                                class="btn btn-sm btn-outline-danger">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4 text-end">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Edit Profile</a>
                            <a href="{{ route('plates.import') }}" class="btn btn-outline-primary">Import Plates CSV</a>
                        
                            <a href="{{ url('/plates/add/multiple') }}" class="btn btn-success">Add Multiple Plates</a>
                            <a href="{{ url('/plates/add') }}" class="btn btn-success">Add New Plate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAllPlates');
            const plateCheckboxes = document.querySelectorAll('input[name="plates[]"]');
            const showSelectedBtn = document.getElementById('showSelected');
               const editAllBtn = document.getElementById('editAll');
                  const viewAllBtn = document.getElementById('viewAll');
            // Select All toggle
            selectAllCheckbox.addEventListener('change', function() {
                plateCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Sync "Select All" when individual checkboxes change
            plateCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        const allChecked = Array.from(plateCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });
            showSelectedBtn.addEventListener('click', () => {
                const checkedCheckboxes = document.querySelectorAll('input[name="plates[]"]:checked');
                const checkedValues = Array.from(checkedCheckboxes).map(cb => cb.value);

                if (checkedValues.length === 0) {
                    alert('No plates selected.');
                    return;
                }

                // Send AJAX request with selected plates
                const token = document.querySelector('input[name="_token"]').value;
                checkedCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr'); // Adjust if not using <tr>
                    if (row) row.remove();
                });

                // Optionally, update the "Select All" checkbox state
                const remainingCheckboxes = document.querySelectorAll(
                    'input[name="plates[]"]');
                if (remainingCheckboxes.length === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.disabled = true;
                }
                alert(token);
                fetch('{{ url('plates/ajaxProcess') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,

                        },
                        body: JSON.stringify({
                            plates: checkedValues
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                       window.location.reload();
                    })
                    .catch(error => {
                        responseDiv.innerHTML = `<p style="color:red;">Request failed. Try again.</p>`;
                        console.error('Error:', error);
                    });

                alert('Selected plates: ' + checkedValues.join(', '));
            });

          editAllBtn.addEventListener('click', () => {
                const checkedValues = Array.from(document.querySelectorAll(
                        'input[name="plates[]"]:checked'))
                    .map(cb => cb.value);

                if (checkedValues.length === 0) {
                    alert('No plates selected.');
                    return;
                }

                // Build query param: plates[]=id1&plates[]=id2 ...
                 const query = `plates=${checkedValues.map(id => encodeURIComponent(id)).join(',')}`;

                // Redirect to new route with query params
                window.location.href = `/plates/summary?${query}`;
            }); // Show values of checked plates on button click
                viewAllBtn.addEventListener('click', () => {
                const checkedValues = Array.from(document.querySelectorAll(
                        'input[name="plates[]"]:checked'))
                    .map(cb => cb.value);

                if (checkedValues.length === 0) {
                    alert('No plates selected.');
                    return;
                }

                // Build query param: plates[]=id1&plates[]=id2 ...
                 const query = `plates=${checkedValues.map(id => encodeURIComponent(id)).join(',')}`;

                // Redirect to new route with query params
                window.location.href = `/plates/views?${query}`;
            }); 
        });
    </script>
@endsection
