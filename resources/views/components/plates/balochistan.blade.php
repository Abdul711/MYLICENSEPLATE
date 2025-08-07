@props(['plate'])
<div class="border border-dark rounded text-center p-3 bg-white" style="width: 100%; aspect-ratio: 4 / 2;">
    <div class="fw-bold" style="font-size: 1rem;">BALOCHISTAN</div>
    <div class="d-flex justify-content-center align-items-center" style="height: 60%;">
        <div style="font-size: 2.3rem; letter-spacing: 4px;">
            {{ $plate->plate_number ?? 'AB 1234' }}
        </div>
    </div>
    <div class="fw-medium" style="font-size: 0.9rem;">QUETTA</div>
     <span class="fw-bold">PKR {{$plate->price }}</span>
</div>