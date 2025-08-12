

@php
     $provinceLogos = [
        'Punjab'      => "#007bff",
        'Sindh'       => "#ffc107",
        'KPK'         => "#17a2b8",
        'Balochistan' => "#dc3545",
    ];
  $provinceColor = $provinceLogos[$plate->region] ?? null;
@endphp




<div
    style="display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #0d6efd; background-color:{{$provinceColor}}; border-radius: 8px; padding: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);">

   <img src="{{ $message->embed($provinceLogo) }}"  style="width: 40px; height: 40px;">
    <div style="text-align: center;">

        <div style="font-weight: bold; font-size: 1rem;">{{ strtoupper($plate->region) }}</div>


        <div style="font-size: 2rem; letter-spacing: 5px; color: white; font-weight: bold;">
            {{ $plate->plate_number ?? 'ABC 000' }}
        </div>
    </div>

    <div style="display: flex; justify-content: center; margin-top: 8px; color: #6c757d;">

        <div>{{ strtoupper($plate->city) }}</div>

    </div>
</div>


