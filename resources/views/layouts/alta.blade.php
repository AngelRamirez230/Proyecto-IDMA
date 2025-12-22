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

    const setFirstOptionText = (select, text) => {
        if (!select || !select.options || select.options.length === 0) return;
        select.options[0].textContent = text;
    };

    const getWrapperInput = (selectEl) => {
        return selectEl
            ?.closest('.select-buscable-wrapper')
            ?.querySelector('.select-buscable-input') || null;
    };

    /* =========================================================
       DOMICILIO
       entidad → municipio → localidad
    ========================================================= */
    const domEntidad   = document.getElementById('entidad');
    const domMunicipio = document.getElementById('municipio');
    const domLocalidad = document.getElementById('localidad');

    if (domEntidad && domMunicipio && domLocalidad) {

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
        domEntidad.addEventListener('change', () => {
            const idEntidad = domEntidad.value;

            resetSelect(domMunicipio, 'Seleccionar', true);
            resetSelect(domLocalidad, 'Selecciona un municipio', true);

            if (municipioInput) {
                municipioInput.value = '';
                municipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad';
                if (!idEntidad) municipioInput.setAttribute('readonly', 'readonly');
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
                        municipioInput.placeholder = 'Buscar municipio...';
                        municipioInput.removeAttribute('readonly');
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
                if (!idMunicipio) localidadInput.setAttribute('readonly', 'readonly');
            }

            if (!idMunicipio) return;

            fetch(`/api/localidades/${idMunicipio}`)
                .then(r => r.json())
                .then(data => {
                    fillSelect(domLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');

                    if (localidadInput) {
                        localidadInput.placeholder = 'Buscar localidad...';
                        localidadInput.removeAttribute('readonly');
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
        // NONE | MEXICO | EXTRANJERO

        if (modo === 'NONE') {
            if (bloqueSelect) bloqueSelect.style.display = 'block';
            if (bloqueInput)  bloqueInput.style.display  = 'none';

            // Entidad nacimiento: placeholder “Seleccionar país”
            setFirstOptionText(nacEntidad, 'Seleccionar país');

            if (nacEntidad) {
                nacEntidad.value = '';
                nacEntidad.disabled = true;
            }

            resetSelect(nacMunicipio, 'Seleccionar', true);
            resetSelect(nacLocalidad, 'Seleccionar', true);

            if (nacMunicipioInput) {
                nacMunicipioInput.value = '';
                nacMunicipioInput.placeholder = 'Seleccione entidad';
                nacMunicipioInput.setAttribute('readonly', 'readonly');
            }

            if (nacLocalidadInput) {
                nacLocalidadInput.value = '';
                nacLocalidadInput.placeholder = 'Seleccione municipio';
                nacLocalidadInput.setAttribute('readonly', 'readonly');
            }

            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });

            return;
        }

        if (modo === 'MEXICO') {
            if (bloqueSelect) bloqueSelect.style.display = 'block';
            if (bloqueInput)  bloqueInput.style.display  = 'none';

            // Entidad nacimiento: placeholder “Seleccionar”
            setFirstOptionText(nacEntidad, 'Seleccionar');

            if (nacEntidad) {
                nacEntidad.disabled = false;
            }

            resetSelect(nacMunicipio, 'Seleccionar', true);
            resetSelect(nacLocalidad, 'Seleccionar', true);

            if (nacMunicipioInput) {
                nacMunicipioInput.value = '';
                nacMunicipioInput.placeholder = 'Seleccione entidad';
                nacMunicipioInput.setAttribute('readonly', 'readonly');
            }

            if (nacLocalidadInput) {
                nacLocalidadInput.value = '';
                nacLocalidadInput.placeholder = 'Seleccione municipio';
                nacLocalidadInput.setAttribute('readonly', 'readonly');
            }

            inputsManual.forEach(i => {
                i.disabled = true;
                i.value = '';
            });

            return;
        }

        // EXTRANJERO
        if (bloqueSelect) bloqueSelect.style.display = 'none';
        if (bloqueInput)  bloqueInput.style.display  = 'block';

        // En extranjero no usamos selects, pero dejamos consistente el texto
        setFirstOptionText(nacEntidad, 'Seleccionar');

        if (nacEntidad) {
            nacEntidad.value = '';
            nacEntidad.disabled = true;
        }

        resetSelect(nacMunicipio, 'Seleccionar', true);
        resetSelect(nacLocalidad, 'Seleccionar', true);

        if (nacMunicipioInput) {
            nacMunicipioInput.value = '';
            nacMunicipioInput.placeholder = 'Seleccione entidad';
            nacMunicipioInput.setAttribute('readonly', 'readonly');
        }

        if (nacLocalidadInput) {
            nacLocalidadInput.value = '';
            nacLocalidadInput.placeholder = 'Seleccione municipio';
            nacLocalidadInput.setAttribute('readonly', 'readonly');
        }

        inputsManual.forEach(i => {
            i.disabled = false;
        });
    };

    // Estado inicial
    if (!paisSelect || !paisSelect.value) {
        setModoNacimiento('NONE');
    } else {
        setModoNacimiento(paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO');
    }

    // Cambio de país
    paisSelect?.addEventListener('change', () => {
        if (!paisSelect.value) {
            setModoNacimiento('NONE');
            return;
        }
        setModoNacimiento(paisNormalizado() === 'MEXICO' ? 'MEXICO' : 'EXTRANJERO');
    });

    // Entidad (nacimiento) → municipios
    nacEntidad?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idEntidad = nacEntidad.value;

        // Cambiar texto de option inicial, según país ya seleccionado
        // (aquí el país ya está seleccionado, así que debe ser “Seleccionar”)
        setFirstOptionText(nacEntidad, 'Seleccionar');

        resetSelect(nacMunicipio, 'Cargando...', true);
        resetSelect(nacLocalidad, 'Seleccionar', true);

        if (nacMunicipioInput) {
            nacMunicipioInput.value = '';
            nacMunicipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad';
            if (!idEntidad) nacMunicipioInput.setAttribute('readonly', 'readonly');
        }

        if (nacLocalidadInput) {
            nacLocalidadInput.value = '';
            nacLocalidadInput.placeholder = 'Seleccione municipio';
            nacLocalidadInput.setAttribute('readonly', 'readonly');
        }

        if (!idEntidad) return;

        fetch(`/api/municipios/${idEntidad}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');

                if (nacMunicipioInput) {
                    nacMunicipioInput.placeholder = 'Buscar municipio...';
                    nacMunicipioInput.removeAttribute('readonly');
                }
            });
    });

    // Municipio (nacimiento) → localidades
    nacMunicipio?.addEventListener('change', () => {
        if (paisNormalizado() !== 'MEXICO') return;

        const idMunicipio = nacMunicipio.value;

        resetSelect(nacLocalidad, 'Cargando...', true);

        if (nacLocalidadInput) {
            nacLocalidadInput.value = '';
            nacLocalidadInput.placeholder = idMunicipio ? 'Buscar localidad...' : 'Seleccione municipio';
            if (!idMunicipio) nacLocalidadInput.setAttribute('readonly', 'readonly');
        }

        if (!idMunicipio) return;

        fetch(`/api/localidades/${idMunicipio}`)
            .then(r => r.json())
            .then(data => {
                fillSelect(nacLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');

                if (nacLocalidadInput) {
                    nacLocalidadInput.placeholder = 'Buscar localidad...';
                    nacLocalidadInput.removeAttribute('readonly');
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

        /* =====================================================
           SINCRONIZAR ESTADO
           - select.disabled  → input.readonly
        ===================================================== */
        function syncDisabled() {
            if (select.disabled) {
                input.setAttribute('readonly', 'readonly');
                input.value = '';
                list.style.display = 'none';
            } else {
                input.removeAttribute('readonly');
            }
        }

        // Estado inicial
        syncDisabled();

        /* =====================================================
           INPUT → FILTRADO
        ===================================================== */
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

                const text = opt.textContent.trim();

                if (text.toLowerCase().includes(term)) {
                    const li = document.createElement('li');
                    li.textContent = text;

                    li.addEventListener('click', () => {
                        select.value = opt.value;
                        input.value  = text;
                        list.style.display = 'none';

                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    list.appendChild(li);
                }
            });

            list.style.display = list.children.length ? 'block' : 'none';
        });

        /* =====================================================
           CLICK FUERA → CERRAR LISTA
        ===================================================== */
        document.addEventListener('click', function (e) {
            if (!wrapper.contains(e.target)) {
                list.style.display = 'none';
            }
        });

        /* =====================================================
           OBSERVAR CAMBIOS EN disabled DEL SELECT
        ===================================================== */
        const observer = new MutationObserver(syncDisabled);
        observer.observe(select, {
            attributes: true,
            attributeFilter: ['disabled']
        });
    }

    document
        .querySelectorAll('.select-buscable-wrapper')
        .forEach(initSelectBuscable);

});
</script>


<script>
    document.getElementById('planEstudios').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const licenciatura = selectedOption.getAttribute('licenciatura') || '';
        document.getElementById('licenciatura').value = licenciatura;
    });
</script>