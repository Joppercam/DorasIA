<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dorasia') }} - ¿Quién está viendo?</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="profile-selector">
        <div class="profile-container">
            <!-- Logo -->
            <div class="logo">
                <x-application-logo class="w-auto h-12 fill-current text-white" />
            </div>
            
            <h1 class="profile-title">¿Quién está viendo?</h1>
            
            <div class="profiles-list">
                @forelse ($profiles as $profile)
                    <div class="profile-item" data-profile-id="{{ $profile->id }}">
                        <div class="profile-image-container">
                            <img src="{{ asset('images/profiles/' . $profile->avatar) }}" 
                                alt="{{ $profile->name }}" 
                                class="profile-image">
                        </div>
                        <div class="profile-name">{{ $profile->name }}</div>
                    </div>
                @empty
                    <div class="flex flex-col items-center">
                        <p class="text-gray-400 text-xl mb-4">No tienes perfiles creados aún.</p>
                        <a href="{{ route('user-profiles.create') }}" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">
                            Crea tu primer perfil
                        </a>
                    </div>
                @endforelse
            </div>
            
            <a href="{{ route('user-profiles.index') }}" class="manage-profiles">
                Administrar Perfiles
            </a>
        </div>
    </div>
    
    <!-- Transition overlay -->
    <div class="netflix-transition"></div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileItems = document.querySelectorAll('.profile-item');
            const transitionOverlay = document.querySelector('.netflix-transition');
            
            profileItems.forEach(item => {
                item.addEventListener('click', function() {
                    const profileId = this.dataset.profileId;
                    
                    // Add selecting class for visual feedback
                    this.classList.add('selecting');
                    
                    // First fade everything out except the selected profile
                    profileItems.forEach(otherItem => {
                        if (otherItem !== this) {
                            otherItem.style.opacity = '0';
                            otherItem.style.transform = 'scale(0.8)';
                            otherItem.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        }
                    });
                    
                    // Zoom and center the selected profile
                    setTimeout(() => {
                        this.style.position = 'absolute';
                        this.style.top = '50%';
                        this.style.left = '50%';
                        this.style.transform = 'translate(-50%, -50%) scale(1.5)';
                        this.style.transition = 'all 0.7s ease';
                        
                        // Show transition overlay
                        setTimeout(() => {
                            transitionOverlay.classList.add('active');
                            
                            // Send AJAX request to set active profile
                            fetch(`/user-profiles/${profileId}/set-active-ajax`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Redirect to homepage after animation
                                    setTimeout(() => {
                                        window.location.href = '/';
                                    }, 500);
                                }
                            });
                        }, 700);
                    }, 400);
                });
            });
        });
    </script>
</body>
</html>