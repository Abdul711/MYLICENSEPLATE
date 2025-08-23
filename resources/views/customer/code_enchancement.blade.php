@php
    $groupedPlates = $plates->groupBy('region');
@endphp

{{-- Punjab Plates --}}
@if(isset($groupedPlates['Punjab']))
    @foreach($groupedPlates['Punjab'] as $plate)
        <div class="col-md-3">
            <x-punjab-plate :plate="$plate" />
        </div>
    @endforeach
@endif

{{-- Sindh Plates --}}
@if(isset($groupedPlates['Sindh']))
    @foreach($groupedPlates['Sindh'] as $plate)
        <div class="col-md-3">
            <x-sindh-plate :plate="$plate" />
        </div>
    @endforeach
@endif

{{-- Balochistan Plates --}}
@if(isset($groupedPlates['Balochistan']))
    @foreach($groupedPlates['Balochistan'] as $plate)
        <div class="col-md-3">
            <x-balochistan-plate :plate="$plate" />
        </div>
    @endforeach
@endif

{{-- KPK Plates --}}
@if(isset($groupedPlates['KPK']))
    @foreach($groupedPlates['KPK'] as $plate)
        <div class="col-md-3">
            <x-kpk-plate :plate="$plate" />
        </div>
    @endforeach
@endif





@php
    $regions = ['Punjab', 'Sindh', 'Balochistan', 'KPK'];
@endphp

@foreach($regions as $region)
    @foreach($plates->where('region', $region) as $plate)
        <div class="col-md-3">
            @if($region === 'Punjab')
                <x-punjab-plate :plate="$plate" />
            @elseif($region === 'Sindh')
                <x-sindh-plate :plate="$plate" />
            @elseif($region === 'Balochistan')
                <x-balochistan-plate :plate="$plate" />
            @elseif($region === 'KPK')
                <x-kpk-plate :plate="$plate" />
            @endif
        </div>
    @endforeach
@endforeach
@foreach($plates as $plate)
    <div class="col-md-3">
        @php
            $component = strtolower($plate->region) . '-plate';
        @endphp

        @component($component, ['plate' => $plate])
        @endcomponent
    </div>
@endforeach
@foreach($plates as $plate)
    <div class="col-md-3">
        @php
            $component = 'components.' . strtolower($plate->region) . '-plate';
        @endphp

        @if(View::exists($component))
            @component($component, ['plate' => $plate])
            @endcomponent
        @else
            <x-default-plate :plate="$plate" />
        @endif
    </div>
@endforeach
@foreach($plates as $plate)
    @php $component = strtolower($plate->region) . '-plate'; @endphp
    <x-{{ $component }} :plate="$plate" />
@endforeach
@php
    $components = [
        'Punjab' => 'punjab-plate',
        'Sindh' => 'sindh-plate',
        'Balochistan' => 'balochistan-plate',
        'KPK' => 'kpk-plate',
    ];
@endphp

@foreach($plates as $plate)
    @if(isset($components[$plate->region]))
        <x-{{ $components[$plate->region] }} :plate="$plate" />
    @endif
@endforeach
@foreach($plates as $plate)
    @php $component = strtolower($plate->region) . '-plate'; @endphp
    <x-{{ $component }} :plate="$plate" />
@endforeach
@foreach($plates as $plate)
    @php $component = 'components.' . strtolower($plate->region) . '-plate'; @endphp
    @if(View::exists($component))
        @component($component, ['plate' => $plate]) @endcomponent
    @else
        <x-default-plate :plate="$plate" />
    @endif
@endforeach
@foreach($plates->where('region', 'Punjab') as $plate)
    <x-punjab-plate :plate="$plate" />
@endforeach
@foreach($plates->where('region', 'Sindh') as $plate)
    <x-sindh-plate :plate="$plate" />
@endforeach

@php $regions = ['Punjab', 'Sindh', 'Balochistan', 'KPK']; @endphp

@foreach($regions as $region)
    @foreach($plates->where('region', $region) as $plate)
        <x-{{ strtolower($region) }}-plate :plate="$plate" />
    @endforeach
@endforeach