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
                            <thead>
                                <tr>
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
@endsection
