<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =========================================================
       FUNCIONES GENERALES
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

    const setFirstOptionText = (select, text) => {
        if (!select || !select.options || select.options.length === 0) return;
        select.options[0].textContent = text;
    };

    /* =========================================================
       FUNCIONALIDAD SELECT BUSCABLE
    ========================================================= */
    function initSelectBuscable(wrapper) {
        const input  = wrapper.querySelector('.select-buscable-input');
        const list   = wrapper.querySelector('.select-buscable-list');
        const select = wrapper.querySelector('select');
        if (!input || !list || !select) return;

        const syncDisabled = () => {
            if (select.disabled) {
                input.setAttribute('readonly', 'readonly');
                list.style.display = 'none';
            } else {
                input.removeAttribute('readonly');
            }
        };
        syncDisabled();

        input.addEventListener('input', function () {
            if (input.hasAttribute('readonly')) return;
            const term = this.value.toLowerCase().trim();
            list.innerHTML = '';
            if (!term) { list.style.display = 'none'; return; }
            [...select.options].forEach(opt => {
                if (!opt.value) return;
                const text = opt.textContent.trim();
                if (text.toLowerCase().includes(term)) {
                    const li = document.createElement('li');
                    li.textContent = text;
                    li.addEventListener('click', () => {
                        select.value = opt.value;
                        input.value = text;
                        list.style.display = 'none';
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    list.appendChild(li);
                }
            });
            list.style.display = list.children.length ? 'block' : 'none';
        });

        document.addEventListener('click', e => {
            if (!wrapper.contains(e.target)) list.style.display = 'none';
        });

        const observer = new MutationObserver(syncDisabled);
        observer.observe(select, { attributes: true, attributeFilter: ['disabled'] });
    }

    document.querySelectorAll('.select-buscable-wrapper').forEach(initSelectBuscable);

    document.querySelectorAll('.select-buscable-wrapper').forEach(wrapper => {
        const input = wrapper.querySelector('.select-buscable-input');
        const select = wrapper.querySelector('select');
        if (input && select) {
            const selectedOption = select.querySelector('option[selected]');
            if (selectedOption) input.value = selectedOption.textContent;
        }
    });

    /* =========================================================
       DOMICILIO: ENTIDAD → MUNICIPIO → LOCALIDAD
    ========================================================= */
    const domEntidad   = document.getElementById('entidad');
    const domMunicipio = document.getElementById('municipio');
    const domLocalidad = document.getElementById('localidad');

    const municipioInput = getWrapperInput(domMunicipio);
    const localidadInput = getWrapperInput(domLocalidad);

    const inicializarDomicilio = async () => {
        const entidadVal = domEntidad?.value;
        const municipioVal = domMunicipio?.value;
        const localidadVal = domLocalidad?.value;

        if (!entidadVal) return;

        const resMunicipios = await fetch(`/api/municipios/${entidadVal}`);
        const dataMunicipios = await resMunicipios.json();
        fillSelect(domMunicipio, 'Seleccionar', dataMunicipios, 'idMunicipio', 'nombreMunicipio');
        if (municipioInput) municipioInput.removeAttribute('readonly');

        if (municipioVal) {
            domMunicipio.value = municipioVal;

            const resLocalidades = await fetch(`/api/localidades/${municipioVal}`);
            const dataLocalidades = await resLocalidades.json();
            fillSelect(domLocalidad, 'Seleccionar', dataLocalidades, 'idLocalidad', 'nombreLocalidad');
            if (localidadInput) localidadInput.removeAttribute('readonly');

            if (localidadVal) domLocalidad.value = localidadVal;
        }
    };

    domEntidad?.addEventListener('change', async () => {
        const idEntidad = domEntidad.value;
        resetSelect(domMunicipio, 'Seleccionar', true);
        resetSelect(domLocalidad, 'Seleccionar', true);
        if (municipioInput) { municipioInput.value = ''; municipioInput.placeholder = idEntidad ? 'Buscar municipio...' : 'Seleccione entidad'; if(!idEntidad) municipioInput.setAttribute('readonly','readonly'); }
        if (localidadInput) { localidadInput.value = ''; localidadInput.placeholder = 'Seleccione municipio'; localidadInput.setAttribute('readonly','readonly'); }

        if (!idEntidad) return;

        const resMunicipios = await fetch(`/api/municipios/${idEntidad}`);
        const dataMunicipios = await resMunicipios.json();
        fillSelect(domMunicipio, 'Seleccionar', dataMunicipios, 'idMunicipio', 'nombreMunicipio');
        if (municipioInput) municipioInput.removeAttribute('readonly');
    });

    domMunicipio?.addEventListener('change', async () => {
        const idMunicipio = domMunicipio.value;
        resetSelect(domLocalidad, 'Seleccionar', true);
        if (localidadInput) { localidadInput.value=''; localidadInput.placeholder=idMunicipio?'Buscar localidad...':'Seleccione municipio'; if(!idMunicipio) localidadInput.setAttribute('readonly','readonly'); }

        if (!idMunicipio) return;

        const resLocalidades = await fetch(`/api/localidades/${idMunicipio}`);
        const dataLocalidades = await resLocalidades.json();
        fillSelect(domLocalidad, 'Seleccionar', dataLocalidades, 'idLocalidad', 'nombreLocalidad');
        if (localidadInput) localidadInput.removeAttribute('readonly');
    });

    inicializarDomicilio();

    /* =========================================================
       LUGAR DE NACIMIENTO: PAIS → ENTIDAD → MUNICIPIO → LOCALIDAD
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
        if (modo==='NONE') {
            if(bloqueSelect) bloqueSelect.style.display='block';
            if(bloqueInput) bloqueInput.style.display='none';
            setFirstOptionText(nacEntidad,'Seleccionar país');
            if(nacEntidad){ nacEntidad.value=''; nacEntidad.disabled=true; }
            resetSelect(nacMunicipio,'Seleccionar',true);
            resetSelect(nacLocalidad,'Seleccionar',true);
            if(nacMunicipioInput){ nacMunicipioInput.value=''; nacMunicipioInput.placeholder='Seleccione entidad'; nacMunicipioInput.setAttribute('readonly','readonly'); }
            if(nacLocalidadInput){ nacLocalidadInput.value=''; nacLocalidadInput.placeholder='Seleccione municipio'; nacLocalidadInput.setAttribute('readonly','readonly'); }
            inputsManual.forEach(i=>{ i.disabled=true; i.value=''; });
            return;
        }
        if(modo==='MEXICO'){
            if(bloqueSelect) bloqueSelect.style.display='block';
            if(bloqueInput) bloqueInput.style.display='none';
            setFirstOptionText(nacEntidad,'Seleccionar');
            if(nacEntidad) nacEntidad.disabled=false;
            resetSelect(nacMunicipio,'Seleccionar',true);
            resetSelect(nacLocalidad,'Seleccionar',true);
            if(nacMunicipioInput){ nacMunicipioInput.value=''; nacMunicipioInput.placeholder='Seleccione entidad'; nacMunicipioInput.setAttribute('readonly','readonly'); }
            if(nacLocalidadInput){ nacLocalidadInput.value=''; nacLocalidadInput.placeholder='Seleccione municipio'; nacLocalidadInput.setAttribute('readonly','readonly'); }
            inputsManual.forEach(i=>{ i.disabled=true; i.value=''; });
            return;
        }
        // EXTRANJERO
        if(bloqueSelect) bloqueSelect.style.display='none';
        if(bloqueInput) bloqueInput.style.display='block';
        setFirstOptionText(nacEntidad,'Seleccionar');
        if(nacEntidad){ nacEntidad.value=''; nacEntidad.disabled=true; }
        resetSelect(nacMunicipio,'Seleccionar',true);
        resetSelect(nacLocalidad,'Seleccionar',true);
        if(nacMunicipioInput){ nacMunicipioInput.value=''; nacMunicipioInput.placeholder='Seleccione entidad'; nacMunicipioInput.setAttribute('readonly','readonly'); }
        if(nacLocalidadInput){ nacLocalidadInput.value=''; nacLocalidadInput.placeholder='Seleccione municipio'; nacLocalidadInput.setAttribute('readonly','readonly'); }
        inputsManual.forEach(i=>{ i.disabled=false; });
    };

    const inicializarNacimiento = async () => {
        if (!paisSelect || !paisSelect.value) { setModoNacimiento('NONE'); return; }
        setModoNacimiento(paisNormalizado()==='MEXICO'?'MEXICO':'EXTRANJERO');

        if(paisNormalizado()!=='MEXICO') return;

        const entidadVal = nacEntidad?.value;
        const municipioVal = nacMunicipio?.value;
        const localidadVal = nacLocalidad?.value;

        if(!entidadVal) return;

        const resMunicipios = await fetch(`/api/municipios/${entidadVal}`);
        const dataMunicipios = await resMunicipios.json();
        fillSelect(nacMunicipio,'Seleccionar',dataMunicipios,'idMunicipio','nombreMunicipio');
        if(nacMunicipioInput) nacMunicipioInput.removeAttribute('readonly');

        if(municipioVal){
            nacMunicipio.value=municipioVal;
            const resLocalidades = await fetch(`/api/localidades/${municipioVal}`);
            const dataLocalidades = await resLocalidades.json();
            fillSelect(nacLocalidad,'Seleccionar',dataLocalidades,'idLocalidad','nombreLocalidad');
            if(nacLocalidadInput) nacLocalidadInput.removeAttribute('readonly');
            if(localidadVal) nacLocalidad.value=localidadVal;
        }
    };

    paisSelect?.addEventListener('change', ()=>{ setModoNacimiento(paisNormalizado()==='MEXICO'?'MEXICO':'EXTRANJERO'); });
    nacEntidad?.addEventListener('change', async ()=>{
        if(paisNormalizado()!=='MEXICO') return;
        const idEntidad=nacEntidad.value;
        resetSelect(nacMunicipio,'Cargando...',true);
        resetSelect(nacLocalidad,'Seleccionar',true);
        if(nacMunicipioInput){ nacMunicipioInput.value=''; nacMunicipioInput.placeholder=idEntidad?'Buscar municipio...':'Seleccione entidad'; if(!idEntidad) nacMunicipioInput.setAttribute('readonly','readonly'); }
        if(nacLocalidadInput){ nacLocalidadInput.value=''; nacLocalidadInput.placeholder='Seleccione municipio'; nacLocalidadInput.setAttribute('readonly','readonly'); }
        if(!idEntidad) return;
        const resMunicipios = await fetch(`/api/municipios/${idEntidad}`);
        const dataMunicipios = await resMunicipios.json();
        fillSelect(nacMunicipio,'Seleccionar',dataMunicipios,'idMunicipio','nombreMunicipio');
        if(nacMunicipioInput) nacMunicipioInput.removeAttribute('readonly');
    });
    nacMunicipio?.addEventListener('change', async ()=>{
        if(paisNormalizado()!=='MEXICO') return;
        const idMunicipio = nacMunicipio.value;
        resetSelect(nacLocalidad,'Cargando...',true);
        if(nacLocalidadInput){ nacLocalidadInput.value=''; nacLocalidadInput.placeholder=idMunicipio?'Buscar localidad...':'Seleccione municipio'; if(!idMunicipio) nacLocalidadInput.setAttribute('readonly','readonly'); }
        if(!idMunicipio) return;
        const resLocalidades = await fetch(`/api/localidades/${idMunicipio}`);
        const dataLocalidades = await resLocalidades.json();
        fillSelect(nacLocalidad,'Seleccionar',dataLocalidades,'idLocalidad','nombreLocalidad');
        if(nacLocalidadInput) nacLocalidadInput.removeAttribute('readonly');
    });

    inicializarNacimiento();

});
</script>
