@extends('layouts.app')

@section('title', 'Registrarse - DORASIA')

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #141414 0%, #2a2a2a 100%); padding-top: 100px; padding-bottom: 50px;">
    <div style="width: 100%; max-width: 450px; padding: 0 20px;">
        <div style="background: rgba(20,20,20,0.95); border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 12px; padding: 2.5rem; backdrop-filter: blur(10px); box-shadow: 0 20px 60px rgba(0, 212, 255, 0.1);">
            
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; font-weight: bold; color: white; margin-bottom: 0.5rem;">
                    DORAS<span style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 50%, #9d4edd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">IA</span>
                </h1>
                <p style="color: #ccc; font-size: 1rem;">Únete a la comunidad de K-Drama fans</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Name Field -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; color: white; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Nombre Completo
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        style="width: 100%; padding: 0.875rem; background: rgba(40,40,40,0.8); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; font-size: 1rem; transition: all 0.3s ease;"
                        placeholder="Tu nombre completo"
                        onfocus="this.style.borderColor='rgba(0, 212, 255, 0.5)'; this.style.boxShadow='0 0 15px rgba(0, 212, 255, 0.2)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.boxShadow='none'"
                    >
                    @error('name')
                        <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

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
                        placeholder="Mínimo 6 caracteres"
                        onfocus="this.style.borderColor='rgba(0, 212, 255, 0.5)'; this.style.boxShadow='0 0 15px rgba(0, 212, 255, 0.2)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.boxShadow='none'"
                    >
                    @error('password')
                        <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div style="margin-bottom: 2rem;">
                    <label for="password_confirmation" style="display: block; color: white; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        Confirmar Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        style="width: 100%; padding: 0.875rem; background: rgba(40,40,40,0.8); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; font-size: 1rem; transition: all 0.3s ease;"
                        placeholder="Repite tu contraseña"
                        onfocus="this.style.borderColor='rgba(0, 212, 255, 0.5)'; this.style.boxShadow='0 0 15px rgba(0, 212, 255, 0.2)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.boxShadow='none'"
                    >
                    @error('password_confirmation')
                        <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    style="width: 100%; padding: 1rem; background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); border: none; border-radius: 8px; color: white; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; margin-bottom: 1.5rem;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 30px rgba(0, 212, 255, 0.3)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    Crear Cuenta
                </button>

                <!-- Links -->
                <div style="text-align: center;">
                    <p style="color: #ccc; font-size: 0.9rem;">
                        ¿Ya tienes una cuenta? 
                        <a href="{{ route('login') }}" style="color: #00d4ff; text-decoration: none; font-weight: 600; transition: color 0.3s ease;" onmouseover="this.style.color='#7b68ee'" onmouseout="this.style.color='#00d4ff'">
                            Inicia sesión aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Features -->
        <div style="margin-top: 2rem; text-align: center;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
                <div style="background: rgba(0, 212, 255, 0.1); border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 8px; padding: 1rem;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📺</div>
                    <p style="color: #ccc; font-size: 0.8rem; margin: 0;">Lista personalizada</p>
                </div>
                <div style="background: rgba(123, 104, 238, 0.1); border: 1px solid rgba(123, 104, 238, 0.3); border-radius: 8px; padding: 1rem;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">⭐</div>
                    <p style="color: #ccc; font-size: 0.8rem; margin: 0;">Califica series</p>
                </div>
                <div style="background: rgba(157, 78, 221, 0.1); border: 1px solid rgba(157, 78, 221, 0.3); border-radius: 8px; padding: 1rem;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💬</div>
                    <p style="color: #ccc; font-size: 0.8rem; margin: 0;">Comenta y conecta</p>
                </div>
            </div>
            <p style="color: #666; font-size: 0.8rem;">
                Al registrarte, aceptas nuestros términos y condiciones
            </p>
        </div>
    </div>
</div>

<style>
    input[type="text"], input[type="email"], input[type="password"] {
        box-sizing: border-box;
    }
    
    input[type="text"]::placeholder, input[type="email"]::placeholder, input[type="password"]::placeholder {
        color: #888;
    }
    
    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
        outline: none;
    }
    
    @media (max-width: 768px) {
        .features-grid {
            grid-template-columns: 1fr !important;
            gap: 0.5rem !important;
        }
    }
</style>
@endsection