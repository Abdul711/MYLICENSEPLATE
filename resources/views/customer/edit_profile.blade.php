
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h4 class="mb-4 text-center">Edit Profile</h4>
                    <form action="{{ url('profile_update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password <small class="text-muted">(optional)</small></label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

