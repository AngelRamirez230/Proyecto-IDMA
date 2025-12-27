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

    const fillSelect = (select, placeholder, data, valueKey, textKey) => {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>`;
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[textKey];
            select.appendChild(opt);
        });
        select.disabled = false;
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

    if (domEntidad && domMunicipio && domLocalidad) {

        const municipioInput = getWrapperInput(domMunicipio);
        const localidadInput = getWrapperInput(domLocalidad);

        // Estado inicial
        if (municipioInput) {
            municipioInput.placeholder = 'Seleccione entidad';
            municipioInput.setAttribute('readonly', 'readonly');
        }

        if (localidadInput) {
            localidadInput.placeholder = 'Seleccione municipio';
            localidadInput.setAttribute('readonly', 'readonly');
        }

        /* ===== ENTIDAD → MUNICIPIOS ===== */
        domEntidad.addEventListener('change', () => {
            const idEntidad = domEntidad.value;

            resetSelect(domMunicipio, 'Seleccionar', true);
            resetSelect(domLocalidad, 'Seleccionar', true);

            if (municipioInput) {
                municipioInput.value = '';
                municipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad';
                municipioInput.setAttribute('readonly', 'readonly');
            }

            if (localidadInput) {
                localidadInput.value = '';
                localidadInput.placeholder = 'Seleccione municipio';
                localidadInput.setAttribute('readonly', 'readonly');
            }

            if (!idEntidad) return;

            fetch(`/api/municipios/${idEntidad}`)
                .then(r => r.json())
                .then(data => {
                    fillSelect(domMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');

                    if (municipioInput) {
                        municipioInput.removeAttribute('readonly');
                        municipioInput.focus(); // ✅ FIX DOBLE CLIC
                    }
                });
        });

        /* ===== MUNICIPIO → LOCALIDADES ===== */
        domMunicipio.addEventListener('change', () => {
            const idMunicipio = domMunicipio.value;

            resetSelect(domLocalidad, 'Seleccionar', true);

            if (localidadInput) {
                localidadInput.value = '';
                localidadInput.placeholder = idMunicipio ? 'Buscar localidad...' : 'Seleccione municipio';
                localidadInput.setAttribute('readonly', 'readonly');
            }

            if (!idMunicipio) return;

            fetch(`/api/localidades/${idMunicipio}`)
                .then(r => r.json())
                .then(data => {
                    fillSelect(domLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');

                    if (localidadInput) {
                        localidadInput.removeAttribute('readonly');
                        localidadInput.focus(); // ✅ FIX DOBLE CLIC
                    }
                });
        });
    }

    /* =========================================================
       LUGAR DE NACIMIENTO
    ========================================================= */
    const paisSelect   = document.getElementById('paisNacimiento');
    const nacEntidad   = document.getElementById('entidadNacimientoSelect');
    const nacMunicipio = document.getElementById('municipioNacimientoSelect');
    const nacLocalidad = document.getElementById('localidadNacimientoSelect');

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

        if (modo === 'NONE') {
            bloqueSelect.style.display = 'block';
            bloqueInput.style.display  = 'none';

            nacEntidad.value = '';
            nacEntidad.disabled = true;

            resetSelect(nacMunicipio, 'Seleccionar', true);
            resetSelect(nacLocalidad, 'Seleccionar', true);

            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });
            return;
        }

        if (modo === 'MEXICO') {
            bloqueSelect.style.display = 'block';
            bloqueInput.style.display  = 'none';

            nacEntidad.disabled = false;

            resetSelect(nacMunicipio, 'Seleccionar', true);
            resetSelect(nacLocalidad, 'Seleccionar', true);

            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });
            return;
        }

        // EXTRANJERO
        bloqueSelect.style.display = 'none';
        bloqueInput.style.display  = 'block';

        nacEntidad.disabled = true;

        resetSelect(nacMunicipio, 'Seleccionar', true);
        resetSelect(nacLocalidad, 'Seleccionar', true);

        inputsManual.forEach(i => i.disabled = false);
    };

    setModoNacimiento(!paisSelect?.value ? 'NONE' :
        paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO'
    );

    paisSelect?.addEventListener('change', () => {
        setModoNacimiento(!paisSelect.value ? 'NONE' :
            paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO'
        );
    });

    /* ===== NACIMIENTO: ENTIDAD → MUNICIPIO ===== */
    nacEntidad?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idEntidad = nacEntidad.value;

        resetSelect(nacMunicipio, 'Seleccionar', true);
        resetSelect(nacLocalidad, 'Seleccionar', true);

        if (!idEntidad) return;

        fetch(`/api/municipios/${idEntidad}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');

                if (nacMunicipioInput) {
                    nacMunicipioInput.removeAttribute('readonly');
                    nacMunicipioInput.focus(); // ✅ FIX
                }
            });
    });

    /* ===== NACIMIENTO: MUNICIPIO → LOCALIDAD ===== */
    nacMunicipio?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idMunicipio = nacMunicipio.value;

        resetSelect(nacLocalidad, 'Seleccionar', true);

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');

                if (nacLocalidadInput) {
                    nacLocalidadInput.removeAttribute('readonly');
                    nacLocalidadInput.focus(); // ✅ FIX
                }
            });
    });

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
            if (!wrapper.contains(e.target)) {
                list.style.display = 'none';
            }
        });

        new MutationObserver(syncDisabled).observe(select, {
            attributes: true,
            attributeFilter: ['disabled']
        });
    }

    document.querySelectorAll('.select-buscable-wrapper')
        .forEach(initSelectBuscable);

});
</script>
