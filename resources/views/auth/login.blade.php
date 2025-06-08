@extends('layouts.app')

@section('title', 'Iniciar Sesión - DORASIA')

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #141414 0%, #2a2a2a 100%); padding-top: 100px;">
    <div style="width: 100%; max-width: 450px; padding: 0 20px;">
        <div style="background: rgba(20,20,20,0.95); border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 12px; padding: 2.5rem; backdrop-filter: blur(10px); box-shadow: 0 20px 60px rgba(0, 212, 255, 0.1);">
            
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; font-weight: bold; color: white; margin-bottom: 0.5rem;">
                    DORAS<span style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 50%, #9d4edd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">IA</span>
                </h1>
                <p style="color: #ccc; font-size: 1rem;">Inicia sesión en tu cuenta</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div style="background: rgba(40, 167, 69, 0.2); border: 1px solid rgba(40, 167, 69, 0.5); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #28a745;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Field -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="email" style="display: block; color: white; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Correo Electrónico
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required
                        style="width: 100%; padding: 0.875rem; background: rgba(40,40,40,0.8); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; font-size: 1rem; transition: all 0.3s ease;"
                        placeholder="tu@email.com"
                        onfocus="this.style.borderColor='rgba(0, 212, 255, 0.5)'; this.style.boxShadow='0 0 15px rgba(0, 212, 255, 0.2)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.boxShadow='none'"
                    >
                    @error('email')
                        <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="password" style="display: block; color: white; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        style="width: 100%; padding: 0.875rem; background: rgba(40,40,40,0.8); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; font-size: 1rem; transition: all 0.3s ease;"
                        placeholder="••••••••"
                        onfocus="this.style.borderColor='rgba(0, 212, 255, 0.5)'; this.style.boxShadow='0 0 15px rgba(0, 212, 255, 0.2)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.boxShadow='none'"
                    >
                    @error('password')
                        <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div style="display: flex; align-items: center; margin-bottom: 2rem;">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        style="margin-right: 0.5rem; accent-color: #00d4ff;"
                    >
                    <label for="remember" style="color: #ccc; font-size: 0.9rem; cursor: pointer;">
                        Recordarme
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    style="width: 100%; padding: 1rem; background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); border: none; border-radius: 8px; color: white; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; margin-bottom: 1rem;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 30px rgba(0, 212, 255, 0.3)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    Iniciar Sesión
                </button>

                <!-- Divider -->
                <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                    <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.2);"></div>
                    <span style="margin: 0 1rem; color: #666; font-size: 0.9rem;">o</span>
                    <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.2);"></div>
                </div>

                <!-- Google Login Button -->
                <a href="{{ route('auth.google') }}" 
                   style="width: 100%; padding: 0.75rem; background: white; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: #333; font-weight: 500; font-size: 0.9rem; cursor: pointer; transition: all 0.3s ease; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(255, 255, 255, 0.1)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continuar con Google
                </a>

                <!-- Links -->
                <div style="text-align: center;">
                    <p style="color: #ccc; font-size: 0.9rem;">
                        ¿No tienes una cuenta? 
                        <a href="{{ route('register') }}" style="color: #00d4ff; text-decoration: none; font-weight: 600; transition: color 0.3s ease;" onmouseover="this.style.color='#7b68ee'" onmouseout="this.style.color='#00d4ff'">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Additional Info -->
        <div style="text-align: center; margin-top: 2rem;">
            <p style="color: #666; font-size: 0.8rem;">
                Al iniciar sesión, aceptas nuestros términos y condiciones
            </p>
        </div>
    </div>
</div>

<style>
    input[type="email"], input[type="password"] {
        box-sizing: border-box;
    }
    
    input[type="email"]::placeholder, input[type="password"]::placeholder {
        color: #888;
    }
    
    input[type="email"]:focus, input[type="password"]:focus {
        outline: none;
    }
</style>
@endsection