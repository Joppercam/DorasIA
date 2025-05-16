@props(['date', 'position' => 'top-left'])

@php
    $isNew = $date && $date instanceof \Carbon\Carbon && $date->gt(now()->subDays(7));
    $isRecent = $date && $date instanceof \Carbon\Carbon && $date->gt(now()->subDays(30));
    
    $positionClasses = [
        'top-left' => 'top-2 left-2',
        'top-right' => 'top-2 right-2',
        'bottom-left' => 'bottom-2 left-2',
        'bottom-right' => 'bottom-2 right-2',
    ];
    
    $positionClass = $positionClasses[$position] ?? $positionClasses['top-left'];
@endphp

@if($isNew)
    <div class="absolute {{ $positionClass }} bg-red-600 text-white text-xs font-bold px-2 py-1 rounded z-10">
        <i class="fas fa-sparkles mr-1"></i>NUEVO
    </div>
@elseif($isRecent)
    <div class="absolute {{ $positionClass }} bg-green-600 text-white text-xs font-bold px-2 py-1 rounded z-10">
        <i class="fas fa-clock mr-1"></i>RECIENTE
    </div>
@endif