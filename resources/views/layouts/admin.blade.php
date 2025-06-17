<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Panel de Administración | Dorasia</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <style>
        .sidebar {
            transition: transform 0.3s ease;
        }
        .sidebar.collapsed {
            transform: translateX(-250px);
        }
        
        .main-content {
            transition: margin-left 0.3s ease;
        }
        .main-content.expanded {
            margin-left: 0;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 bg-gray-800 border-r border-gray-700 z-30">
            <div class="p-6">
                <div class="flex items-center mb-8">
                    <h1 class="text-xl font-bold text-white">
                        DORAS<span class="text-blue-400">IA</span> Admin
                    </h1>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                        <i data-feather="home" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('admin.series') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.series') ? 'bg-gray-700 text-white' : '' }}">
                        <i data-feather="tv" class="w-5 h-5 mr-3"></i>
                        Series
                    </a>
                    
                    <a href="{{ route('admin.movies') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.movies') ? 'bg-gray-700 text-white' : '' }}">
                        <i data-feather="film" class="w-5 h-5 mr-3"></i>
                        Películas
                    </a>
                    
                    <a href="{{ route('admin.users') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.users') ? 'bg-gray-700 text-white' : '' }}">
                        <i data-feather="users" class="w-5 h-5 mr-3"></i>
                        Usuarios
                    </a>
                    
                    <a href="{{ route('admin.comments') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.comments') ? 'bg-gray-700 text-white' : '' }}">
                        <i data-feather="message-circle" class="w-5 h-5 mr-3"></i>
                        Comentarios
                    </a>
                </nav>
                
                <div class="mt-auto pt-8">
                    <div class="border-t border-gray-700 pt-6">
                        <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-colors">
                            <i data-feather="external-link" class="w-4 h-4 mr-2"></i>
                            Ver sitio
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-colors w-full text-left">
                                <i data-feather="log-out" class="w-4 h-4 mr-2"></i>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div id="main-content" class="main-content flex-1 ml-64">
            <!-- Header -->
            <header class="bg-gray-800 border-b border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="text-gray-400 hover:text-white mr-4 lg:hidden">
                            <i data-feather="menu" class="w-6 h-6"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-300">{{ auth()->user()->name }}</span>
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <script>
        // Initialize Feather icons
        feather.replace();
        
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        
        sidebarToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    </script>
</body>
</html>