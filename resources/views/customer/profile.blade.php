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
                            @foreach ($myplates as $plate)
                        
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{$plate->plate_number}}</span>
                                    <span>{{$plate->city}}</span>
                                        <span>{{$plate->region}}</span>
                                @if($plate->status == 'Sold')
                                    <span class="badge bg-danger rounded-pill">Sold</span>
                                @elseif($plate->status == 'Pending')
                                    <span class="badge bg-warning text-dark rounded-pill">Pending</span>    
                                @else
                                    <span class="badge bg-success rounded-pill">Available</span>
                                @endif
                                <span class="badge bg-secondary rounded-pill">{{ $plate->price }} PKR</span>
                                <a href="{{ url('plates/' . $plate->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                 <a href="{{ url('plates/' . $plate->id) }}" class="btn btn-sm btn-outline-danger">Delete</a>

                            </li>
                          
                            @endforeach
                          
                        
                        </ul>
                        <div class="mt-4 text-end">
                            <a href="#" class="btn btn-outline-primary">Edit Profile</a>
                    <a href="{{ route('plates.import') }}" class="btn btn-outline-primary">Import Plates CSV</a>
                           
                          <a href="{{url('/plates/add/multiple')}}" class="btn btn-success">Add Multiple Plates</a>
                            <a href="{{url('/plates/add')}}" class="btn btn-success">Add New Plate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
