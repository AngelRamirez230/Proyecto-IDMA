<div>
    @props(['field'])

    @error($field)
        <div class="mensajeError">{{ $message }}</div>
    @enderror
</div>