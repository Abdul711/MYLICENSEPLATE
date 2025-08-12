


@props(['plate', 'cityUrduName'])


     <div class="d-flex flex-column justify-content-between border border-primary bg-danger rounded p-3 shadow">
         <img src="{{ asset('glogo/balochistan.jpeg') }}" width="40" height="40">
         <div class="text-center">
             @if ($plate->featured == 1)
                 <div class="fw-bold" style="font-size: 1rem;">بلوچستان</div>
             @else
                 <div class="fw-bold" style="font-size: 1rem;">{{ strtoupper($plate->region) }}
                 </div>
             @endif
             <div style="font-size: 2rem; letter-spacing: 5px; color: white;">
                 {{ $plate->plate_number ?? 'ABC 000' }}
             </div>
         </div>
         <div class="d-flex justify-content-center mt-2 ">
             @if ($plate->featured == 1)
                 <div class="text-muted      ">{{ $cityUrduName }}</div>
             @else
                 <div class="text-muted      ">{{ strtoupper($plate->city) }}</div>
             @endif
         </div>

     </div>
     <div class="fw-bold text-center">Owner: {{ $plate->user->name }}</div>
     <div class="fw-bold text-center">Number: {{ $plate->user->mobile }}</div>
     <div class="fw-bold text-center">PKR {{ $plate->price }}</div>
     <a href="{{ url('plates/' . $plate->id . '/show') }}" class="btn btn-primary">View Detail</a>
     @auth
         @if ($plate->user_id != Auth::user()->id)
             <a href="" class="btn btn-danger">Order</a>
         @endif
     @endauth

