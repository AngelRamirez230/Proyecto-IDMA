<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de usuario</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('layouts.barraNavegacion')

    <main class="consulta">
        <h1 class="consulta-titulo">Detalle de usuario</h1>

        <section class="consulta-tabla-contenedor detalle-usuario">
            {{-- Encabezado básico --}}
            <div class="detalle-usuario__header">
                <div class="detalle-usuario__identidad">
                    <div class="detalle-usuario__nombre texto-titulo-md">
                        {{ $vm['nombreCompleto'] ?: 'Sin nombre' }}
                    </div>

                    <div class="detalle-usuario__meta texto-meta">
                        Usuario: {{ $usuario->nombreUsuario ?? 'N/D' }} |
                        Rol: {{ $usuario->tipoDeUsuario->nombreTipoDeUsuario ?? 'N/D' }} |
                        Estatus: {{ $usuario->estatus->nombreTipoDeEstatus ?? 'N/D' }}
                    </div>
                </div>

                <div class="detalle-usuario__acciones">
                    <a href="{{ route('consultaUsuarios') }}" class="btn-boton-formulario btn-cancelar">
                        Volver
                    </a>

                    {{-- Si luego se hace editar --}}
                    {{-- <a href="{{ route('usuarios.edit', $usuario->idUsuario) }}" class="btn-boton-formulario">Editar</a> --}}
                </div>
            </div>

            {{-- Secciones --}}
            @include('shared.moduloUsuarios.partials.detalle._datosPersonales', ['u' => $usuario])
            @include('shared.moduloUsuarios.partials.detalle._domicilio', ['u' => $usuario])
            @include('shared.moduloUsuarios.partials.detalle._nacimiento', ['u' => $usuario])
        </section>
    </main>
</body>
</html>