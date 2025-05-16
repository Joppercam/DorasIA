@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Test Profile Edit Links</h1>
    
    @auth
        <div class="bg-gray-900 p-6 rounded-lg">
            <h2 class="text-xl mb-4">Current User Info:</h2>
            <p>Email: {{ auth()->user()->email }}</p>
            <p>ID: {{ auth()->user()->id }}</p>
            
            @if(auth()->user()->getActiveProfile())
                <h3 class="text-lg mt-4 mb-2">Active Profile:</h3>
                <p>Name: {{ auth()->user()->getActiveProfile()->name }}</p>
                <p>ID: {{ auth()->user()->getActiveProfile()->id }}</p>
                <p>User ID: {{ auth()->user()->getActiveProfile()->user_id }}</p>
                
                <h3 class="text-lg mt-4 mb-2">Edit Links:</h3>
                
                <div class="space-y-2">
                    <div>
                        <a href="{{ route('profiles.edit', auth()->user()->getActiveProfile()) }}" 
                           class="bg-blue-600 px-4 py-2 rounded inline-block">
                            Edit Profile (Model)
                        </a>
                        <span class="ml-2 text-sm">{{ route('profiles.edit', auth()->user()->getActiveProfile()) }}</span>
                    </div>
                    
                    <div>
                        <a href="{{ route('profiles.edit', auth()->user()->getActiveProfile()->id) }}" 
                           class="bg-green-600 px-4 py-2 rounded inline-block">
                            Edit Profile (ID)
                        </a>
                        <span class="ml-2 text-sm">{{ route('profiles.edit', auth()->user()->getActiveProfile()->id) }}</span>
                    </div>
                    
                    <div>
                        <a href="/profiles/{{ auth()->user()->getActiveProfile()->id }}/edit" 
                           class="bg-purple-600 px-4 py-2 rounded inline-block">
                            Edit Profile (Direct URL)
                        </a>
                        <span class="ml-2 text-sm">/profiles/{{ auth()->user()->getActiveProfile()->id }}/edit</span>
                    </div>
                </div>
                
                <h3 class="text-lg mt-4 mb-2">Can Edit Check:</h3>
                <p>Can edit profile: 
                    @can('update', auth()->user()->getActiveProfile())
                        <span class="text-green-500">YES</span>
                    @else
                        <span class="text-red-500">NO</span>
                    @endcan
                </p>
            @else
                <p class="text-red-500">No active profile found</p>
            @endif
        </div>
    @else
        <p>Please login to test</p>
    @endauth
</div>
@endsection