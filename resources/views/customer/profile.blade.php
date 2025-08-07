@extends('layout')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="https://i.pravatar.cc/100" class="rounded-circle me-3" width="80" height="80" alt="Profile">
                            <div>
                                <h4 class="mb-0">{{Auth::user()->name}}</h4>
                                <small class="text-muted">{{Auth::user()->email}}</small>
                            </div>
                        </div>
                        <hr>
                        <h5>My Plates</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>AAA-123</span>
                                <span class="badge bg-success rounded-pill">Available</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>XYZ-786</span>
                                <span class="badge bg-danger rounded-pill">Sold</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>CAR-2025</span>
                                <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                            </li>
                        </ul>
                        <div class="mt-4 text-end">
                            <a href="#" class="btn btn-outline-primary">Edit Profile</a>
                            <a href="{{url('/plates/add')}}" class="btn btn-success">Add New Plate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
