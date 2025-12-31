<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =========================================================
       HELPERS GENERALES
    ========================================================= */
    const resetSelect = (select, placeholder, disabled = true) => {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = disabled;
    };

    const fillSelect = (select, placeholder, data, valueKey, textKey, selectedValue = null) => {
        if (!select) return;

        select.innerHTML = `<option value="">${placeholder}</option>`;

        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[textKey];

            if (selectedValue && String(selectedValue) === String(item[valueKey])) {
                opt.selected = true;
            }

            select.appendChild(opt);
        });

        select.disabled = false;
    };

    const setFirstOptionText = (select, text) => {
        if (select?.options?.length) {
            select.options[0].textContent = text;
        }
    };

    const getWrapperInput = (selectEl) => {
        return selectEl
            ?.closest('.select-buscable-wrapper')
            ?.querySelector('.select-buscable-input') || null;
    };

    /* =========================================================
       DOMICILIO
    ========================================================= */
    const domEntidad   = document.getElementById('entidad');
    const domMunicipio = document.getElementById('municipio');
    const domLocalidad = document.getElementById('localidad');

    const oldEntidad   = domEntidad?.dataset.old || null;
    const oldMunicipio = domMunicipio?.dataset.old || null;
    const oldLocalidad = domLocalidad?.dataset.old || null;

    const municipioInput = getWrapperInput(domMunicipio);
    const localidadInput = getWrapperInput(domLocalidad);

    /* ===== ESTADO INICIAL ===== */
    if (municipioInput) {
        municipioInput.placeholder = 'Seleccione entidad';
        municipioInput.setAttribute('readonly', 'readonly');
    }

    if (localidadInput) {
        localidadInput.placeholder = 'Seleccione municipio';
        localidadInput.setAttribute('readonly', 'readonly');
    }

    /* ===== ENTIDAD → MUNICIPIOS ===== */
    domEntidad?.addEventListener('change', () => {

        const idEntidad = domEntidad.value;

        resetSelect(domMunicipio, 'Seleccionar', true);
        resetSelect(domLocalidad, 'Seleccionar', true);

        if (!idEntidad) return;

        fetch(`/api/municipios/${idEntidad}`)
            .then(r => r.json())
            .then(data => {

                fillSelect(
                    domMunicipio,
                    'Seleccionar',
                    data,
                    'idMunicipio',
                    'nombreMunicipio',
                    oldMunicipio
                );

                if (municipioInput) {
                    municipioInput.placeholder = 'Buscar municipio...';
                    municipioInput.removeAttribute('readonly');
                }

                if (oldMunicipio) {
                    domMunicipio.dispatchEvent(new Event('change'));
                }
            });
    });

    /* ===== MUNICIPIO → LOCALIDADES ===== */
    domMunicipio?.addEventListener('change', () => {

        const idMunicipio = domMunicipio.value;

        resetSelect(domLocalidad, 'Seleccionar', true);

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {

                fillSelect(
                    domLocalidad,
                    'Seleccionar',
                    data,
                    'idLocalidad',
                    'nombreLocalidad',
                    oldLocalidad
                );

                if (localidadInput) {
                    localidadInput.placeholder = 'Buscar localidad...';
                    localidadInput.removeAttribute('readonly');
                }
            });
    });

    /* ===== RESTAURAR DOMICILIO ===== */
    if (oldEntidad) {
        domEntidad.value = oldEntidad;
        domEntidad.dispatchEvent(new Event('change'));
    }

    /* =========================================================
       LUGAR DE NACIMIENTO
    ========================================================= */
    const paisSelect   = document.getElementById('paisNacimiento');
    const nacEntidad   = document.getElementById('entidadNacimientoSelect');
    const nacMunicipio = document.getElementById('municipioNacimientoSelect');
    const nacLocalidad = document.getElementById('localidadNacimientoSelect');

    const oldNacEntidad   = nacEntidad?.dataset.old || null;
    const oldNacMunicipio = nacMunicipio?.dataset.old || null;
    const oldNacLocalidad = nacLocalidad?.dataset.old || null;

    const bloqueSelect = document.getElementById('bloque-select-nacimiento');
    const bloqueInput  = document.getElementById('bloque-input-nacimiento');
    const inputsManual = bloqueInput ? bloqueInput.querySelectorAll('input') : [];

    const nacMunicipioInput = getWrapperInput(nacMunicipio);
    const nacLocalidadInput = getWrapperInput(nacLocalidad);

    const paisNormalizado = () => {
        const opt = paisSelect?.options[paisSelect.selectedIndex];
        return opt?.dataset?.normalizado?.toUpperCase() || '';
    };

    const setModoNacimiento = (modo) => {

        bloqueSelect.classList.remove('activo');
        bloqueInput.classList.remove('activo');

        if (modo === 'NONE') {
            nacEntidad.disabled = nacMunicipio.disabled = nacLocalidad.disabled = true;
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);
            inputsManual.forEach(i => { i.disabled = true; });
            return;
        }

        if (modo === 'MEXICO') {
            bloqueSelect.classList.add('activo');
            nacEntidad.disabled = false;
            nacMunicipio.disabled = nacLocalidad.disabled = true;
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);
            inputsManual.forEach(i => { i.disabled = true; });
            return;
        }

        if (modo === 'EXTRANJERO') {
            bloqueInput.classList.add('activo');
            nacEntidad.value = '';
            nacEntidad.disabled = nacMunicipio.disabled = nacLocalidad.disabled = true;
            resetSelect(nacMunicipio, 'Seleccionar municipio', true);
            resetSelect(nacLocalidad, 'Seleccionar localidad', true);
            inputsManual.forEach(i => { i.disabled = false; });
        }
    };

    /* ===== ESTADO INICIAL NACIMIENTO ===== */
    if (paisSelect?.value) {
        setModoNacimiento(paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO');
    } else {
        setModoNacimiento('NONE');
    }

    paisSelect?.addEventListener('change', () => {
        if (!paisSelect.value) {
            setModoNacimiento('NONE');
            return;
        }
        setModoNacimiento(paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO');
    });

    /* ===== NACIMIENTO ENTIDAD → MUNICIPIO ===== */
    nacEntidad?.addEventListener('change', () => {

        if (paisNormalizado() !== 'MEXICO') return;

        const idEntidad = nacEntidad.value;
        setFirstOptionText(nacEntidad, 'Seleccionar');

        resetSelect(nacMunicipio, 'Cargando...', true);
        resetSelect(nacLocalidad, 'Seleccionar', true);

        if (!idEntidad) return;

        fetch(`/api/municipios/${idEntidad}`)
            .then(r => r.json())
            .then(data => {

                fillSelect(
                    nacMunicipio,
                    'Seleccionar',
                    data,
                    'idMunicipio',
                    'nombreMunicipio',
                    oldNacMunicipio
                );

                if (oldNacMunicipio) {
                    nacMunicipio.dispatchEvent(new Event('change'));
                }
            });
    });

    /* ===== NACIMIENTO MUNICIPIO → LOCALIDAD ===== */
    nacMunicipio?.addEventListener('change', () => {

        if (paisNormalizado() !== 'MEXICO') return;

        const idMunicipio = nacMunicipio.value;
        resetSelect(nacLocalidad, 'Cargando...', true);

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {

                fillSelect(
                    nacLocalidad,
                    'Seleccionar',
                    data,
                    'idLocalidad',
                    'nombreLocalidad',
                    oldNacLocalidad
                );
            });
    });

    if (oldNacEntidad) {
        nacEntidad.value = oldNacEntidad;
        nacEntidad.dispatchEvent(new Event('change'));
    }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function initSelectBuscable(wrapper) {
        const input  = wrapper.querySelector('.select-buscable-input');
        const list   = wrapper.querySelector('.select-buscable-list');
        const select = wrapper.querySelector('select');

        if (!input || !list || !select) return;

        function syncDisabled() {
            if (select.disabled) {
                input.setAttribute('readonly', 'readonly');
                input.value = '';
                list.style.display = 'none';
            } else {
                input.removeAttribute('readonly');
            }
        }

        syncDisabled();

        input.addEventListener('input', function () {
            if (input.hasAttribute('readonly')) return;

            const term = this.value.toLowerCase().trim();
            list.innerHTML = '';

            if (!term) {
                list.style.display = 'none';
                return;
            }

            [...select.options].forEach(opt => {
                if (!opt.value) return;
                if (opt.textContent.toLowerCase().includes(term)) {
                    const li = document.createElement('li');
                    li.textContent = opt.textContent;
                    li.onclick = () => {
                        select.value = opt.value;
                        input.value = opt.textContent;
                        list.style.display = 'none';
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    };
                    list.appendChild(li);
                }
            });

            list.style.display = list.children.length ? 'block' : 'none';
        });

        document.addEventListener('click', e => {
            if (!wrapper.contains(e.target)) list.style.display = 'none';
        });

        new MutationObserver(syncDisabled).observe(select, {
            attributes: true,
            attributeFilter: ['disabled']
        });
    }

    document
        .querySelectorAll('.select-buscable-wrapper')
        .forEach(initSelectBuscable);

});
</script>
