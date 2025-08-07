@props(['plate'])
<div class="border border-dark rounded text-center p-3 bg-light" style="width: 100%; aspect-ratio: 4 / 2;">
    <div class="fw-bold" style="font-size: 1rem;">PUNJAB</div>
    <div class="d-flex justify-content-center align-items-center" style="height: 70%;">
        <div style="font-size: 2.5rem; letter-spacing: 5px;">
            {{ $plate->plate_number ?? 'ABC 000' }}
        </div>
        
    </div>
    <div class="fw-medium" style="font-size: 0.9rem;">{{ strtoupper($plate->city) }}</div>
     <div class="fw-bold">PKR {{$plate->price }}</div>
    
</div>

    <!-- Waste no more time arguing what a good                                                                                                     