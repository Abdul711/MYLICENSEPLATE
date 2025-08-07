@props(['plate'])
<div class="d-flex flex-column justify-content-between border border-primary bg-warning rounded p-3 shadow">
    <div class="text-center">
         <div class="fw-bold" style="font-size: 1rem;">SINDH</div>
          <div style="font-size: 2rem; letter-spacing: 5px;">
            {{ $plate->plate_number ?? 'ABC 000' }}
        </div>
    </div>
    <div class="d-flex justify-content-center mt-2 ">
        <div class="text-muted"> {{$plate->city}}</div>
      
    </div>
      <div class="fw-bold text-center">PKR {{$plate->price }}</div>
</div>

