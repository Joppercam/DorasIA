<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $profiles = $user->profiles;
        
        return view('user-profiles.index', [
            'profiles' => $profiles,
            'activeProfileId' => session('active_profile_id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user-profiles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $profile = $request->user()->profiles()->create([
            'name' => $validated['name'],
            'avatar' => 'default.jpg',
        ]);
        
        // Set this profile as active
        session(['active_profile_id' => $profile->id]);
        
        return redirect()->route('user-profiles.index')
            ->with('success', 'Perfil creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
        // Check if the profile belongs to the current user
        if ($profile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este perfil.');
        }
        
        return view('user-profiles.show', [
            'profile' => $profile,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Profile $profile)
    {
        // Check if the profile belongs to the current user
        if ($profile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este perfil.');
        }
        
        return view('user-profiles.edit', [
            'profile' => $profile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profile $profile)
    {
        // Check if the profile belongs to the current user
        if ($profile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este perfil.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $profile->update($validated);
        
        return redirect()->route('user-profiles.index')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        // Check if the profile belongs to the current user
        if ($profile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar este perfil.');
        }
        
        // Check if this is the only profile
        if (auth()->user()->profiles()->count() === 1) {
            return redirect()->route('user-profiles.index')
                ->with('error', 'No puedes eliminar tu único perfil.');
        }
        
        // Check if this is the active profile
        if (session('active_profile_id') === $profile->id) {
            // Set another profile as active
            $newActiveProfile = auth()->user()->profiles()
                ->where('id', '!=', $profile->id)
                ->first();
            
            session(['active_profile_id' => $newActiveProfile->id]);
        }
        
        $profile->delete();
        
        return redirect()->route('user-profiles.index')
            ->with('success', 'Perfil eliminado correctamente.');
    }
    
    /**
     * Set the profile as active.
     */
    public function setActive(Request $request, Profile $profile)
    {
        // Check if the profile belongs to the current user
        if ($profile->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para usar este perfil.');
        }
        
        // Set the profile as active
        session(['active_profile_id' => $profile->id]);
        
        return redirect()->back()
            ->with('success', 'Perfil activo cambiado correctamente.');
    }
    
    /**
     * Display the Netflix-style profile selector.
     */
    public function selector(Request $request)
    {
        $user = $request->user();
        $profiles = $user->profiles;
        
        if ($profiles->isEmpty()) {
            return redirect()->route('user-profiles.create')
                ->with('info', 'Por favor, crea un perfil primero.');
        }
        
        return view('user-profiles.selector', [
            'profiles' => $profiles,
        ]);
    }
    
    /**
     * Set active profile via AJAX for animated transitions.
     */
    public function setActiveAjax(Request $request, Profile $profile)
    {
        // Check if the profile belongs to the current user
        if ($profile->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para usar este perfil.'
            ], 403);
        }
        
        // Set the profile as active
        session(['active_profile_id' => $profile->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Perfil activo cambiado correctamente.'
        ]);
    }
}