@extends('layouts.main')

@section('content')
<h2>Resumen de generación de pagos</h2>

<p>Pagos creados: {{ $creados }}</p>

@if(count($duplicados) > 0)
    <h3>Referencias duplicadas</h3>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($duplicados as $dup)
                <tr>
                    <td>{{ $dup['estudiante'] }}</td>
                    <td>{{ $dup['referencia'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No hay referencias duplicadas.</p>
@endif

<a href="{{ route('admin.pagos.create') }}">Volver</a>
@endsection
