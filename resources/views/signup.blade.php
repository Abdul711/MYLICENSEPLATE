<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Responsive Signup Form</title>
  <link rel="stylesheet" href="{{asset('css/style.css')}}" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

</head>
<body>
  <div class="signup-container">
    <form class="signup-form" method="POST" ">
      <h2>Sign Up</h2>
        @csrf
      <input type="text" placeholder="Full Name" name="name" />
   @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
       
   @enderror
      <input type="email" placeholder="Email Address" name="email" />
        @error('email')
                <div class="alert alert-danger">{{ $message }}

                </div>
        @enderror
      <input type="password" placeholder="Password"  name="password" />
        @error('password')
                <div class="alert alert-danger">{{ $message }}</div>
        @enderror
     
            <input type="text" placeholder="Mobile"  name="mobile" />
     @error('mobile')
            <div class="alert alert-danger">{{ $message }}</div>
         
     @enderror
         
       <div class="form-check">
            <input type="checkbox" class="form-check-input" id="terms" name="terms">
            <label class="form-check-label" for="terms">I agree to the terms and conditions</label>
        </div>

      <input type="submit"/>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @if(session('success'))
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
