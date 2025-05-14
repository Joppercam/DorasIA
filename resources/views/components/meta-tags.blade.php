@if($title)
    @push('meta-title'){{ $title }}@endpush
@endif

@if($description)
    @push('meta-description'){{ $description }}@endpush
@endif

@if($image)
    @push('meta-image'){{ $image }}@endpush
@endif

@once
    @prepend('scripts')
    <script>
        // Configuración para metadatos dinámicos
        document.addEventListener('DOMContentLoaded', function() {
            // Aquí podría ir código para actualizar metadatos dinámicamente
            // Por ejemplo, para videos o contenido que cambia sin recargar la página
        });
    </script>
    @endprepend
@endonce