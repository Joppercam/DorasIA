@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Test Comments Components</h1>
    
    @php
        $title = \App\Models\Title::first();
    @endphp
    
    @if($title)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-bold mb-4">Enhanced Comments Component</h2>
                <x-enhanced-comments :title="$title" />
            </div>
            
            <div>
                <h2 class="text-xl font-bold mb-4">Simple Comments Component (Debug)</h2>
                <x-simple-comments :title="$title" />
            </div>
        </div>
        
        <div class="mt-8 bg-gray-800 p-6 rounded">
            <h3 class="text-lg font-bold mb-4">Title Info</h3>
            <p>ID: {{ $title->id }}</p>
            <p>Title: {{ $title->title }}</p>
            <p>Comments count: {{ $title->comments()->count() }}</p>
        </div>
    @else
        <p class="text-red-500">No titles found in database</p>
    @endif
</div>
@endsection