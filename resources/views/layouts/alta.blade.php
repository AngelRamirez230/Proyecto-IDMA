<script>
document.addEventListener('DOMContentLoaded', () => {

/* =========================================================
   UTILIDADES GENERALES
========================================================= */
const resetSelect = (select, placeholder = 'Seleccionar', disabled = true) => {
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

const getWrapperInput = (select) =>
    select?.closest('.select-buscable-wrapper')
          ?.querySelector('.select-buscable-input') || null;

const syncInputFromSelect = (select) => {
    const input = getWrapperInput(select);
    if (!select || !input) return;

    const opt = select.querySelector(`option[value="${select.value}"]`);
    if (opt) input.value = opt.textContent;
};

/* =========================================================
   SELECT BUSCABLE (NO BORRA NADA)
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

    input.addEventListener('input', () => {
        if (input.hasAttribute('readonly')) return;

        const term = input.value.toLowerCase().trim();
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

                li.addEventListener('click', () => {
                    select.value = opt.value;
                    input.value  = opt.textContent;
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

    new MutationObserver(syncDisabled)
        .observe(select, { attributes: true, attributeFilter: ['disabled'] });
}

document.querySelectorAll('.select-buscable-wrapper')
        .forEach(initSelectBuscable);

/* =========================================================
   SINCRONIZAR VALORES QUE VIENEN DE BLADE
========================================================= */
document.querySelectorAll('select').forEach(syncInputFromSelect);

/* =========================================================
   DOMICILIO
========================================================= */
const domEntidad   = document.getElementById('entidad');
const domMunicipio = document.getElementById('municipio');
const domLocalidad = document.getElementById('localidad');

const domMunInput = getWrapperInput(domMunicipio);
const domLocInput = getWrapperInput(domLocalidad);

const initDomicilio = async () => {
    if (!domEntidad?.value) return;

    const munVal = domMunicipio.value;
    const locVal = domLocalidad.value;

    // Cargar municipios
    const resMun = await fetch(`/api/municipios/${domEntidad.value}`);
    const dataMun = await resMun.json();
    fillSelect(domMunicipio, 'Seleccionar', dataMun, 'idMunicipio', 'nombreMunicipio');

    if (munVal) {
        // Si ya había un municipio guardado, lo seleccionamos
        const existsMun = Array.from(domMunicipio.options).some(opt => opt.value === munVal);
        if (!existsMun) {
            const tempOpt = document.createElement('option');
            tempOpt.value = munVal;
            tempOpt.textContent = domMunInput?.value || 'Seleccionar';
            domMunicipio.appendChild(tempOpt);
        }
        domMunicipio.value = munVal;
        syncInputFromSelect(domMunicipio);

        // Cargar localidades
        const resLoc = await fetch(`/api/localidades/${munVal}`);
        const dataLoc = await resLoc.json();
        fillSelect(domLocalidad, 'Seleccionar', dataLoc, 'idLocalidad', 'nombreLocalidad');

        if (locVal) {
            // Si ya había una localidad guardada, la agregamos si no existe
            const existsLoc = Array.from(domLocalidad.options).some(opt => opt.value === locVal);
            if (!existsLoc) {
                const tempOpt = document.createElement('option');
                tempOpt.value = locVal;
                tempOpt.textContent = domLocInput?.value || 'Seleccionar';
                domLocalidad.appendChild(tempOpt);
            }
            domLocalidad.value = locVal;
            syncInputFromSelect(domLocalidad);
        }
    }
};

domEntidad?.addEventListener('change', async () => {
    resetSelect(domMunicipio);
    resetSelect(domLocalidad);
    if (!domEntidad.value) return;

    const res = await fetch(`/api/municipios/${domEntidad.value}`);
    const data = await res.json();
    fillSelect(domMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');
});

domMunicipio?.addEventListener('change', async () => {
    resetSelect(domLocalidad);
    if (!domMunicipio.value) return;

    const res = await fetch(`/api/localidades/${domMunicipio.value}`);
    const data = await res.json();
    fillSelect(domLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');
});

initDomicilio();

/* =========================================================
   NACIMIENTO
========================================================= */
const paisSelect   = document.getElementById('paisNacimiento');
const nacEntidad   = document.getElementById('entidadNacimientoSelect');
const nacMunicipio = document.getElementById('municipioNacimientoSelect');
const nacLocalidad = document.getElementById('localidadNacimientoSelect');

const nacMunInput = getWrapperInput(nacMunicipio);
const nacLocInput = getWrapperInput(nacLocalidad);

const paisNormalizado = () =>
    paisSelect?.options[paisSelect.selectedIndex]?.dataset?.normalizado || '';

const initNacimiento = async () => {
    if (paisNormalizado() !== 'MEXICO' || !nacEntidad?.value) return;

    const munVal = nacMunicipio.value;
    const locVal = nacLocalidad.value;

    const resMun = await fetch(`/api/municipios/${nacEntidad.value}`);
    const dataMun = await resMun.json();
    fillSelect(nacMunicipio, 'Seleccionar', dataMun, 'idMunicipio', 'nombreMunicipio');

    if (munVal) {
        const existsMun = Array.from(nacMunicipio.options).some(opt => opt.value === munVal);
        if (!existsMun) {
            const tempOpt = document.createElement('option');
            tempOpt.value = munVal;
            tempOpt.textContent = nacMunInput?.value || 'Seleccionar';
            nacMunicipio.appendChild(tempOpt);
        }
        nacMunicipio.value = munVal;
        syncInputFromSelect(nacMunicipio);

        const resLoc = await fetch(`/api/localidades/${munVal}`);
        const dataLoc = await resLoc.json();
        fillSelect(nacLocalidad, 'Seleccionar', dataLoc, 'idLocalidad', 'nombreLocalidad');

        if (locVal) {
            const existsLoc = Array.from(nacLocalidad.options).some(opt => opt.value === locVal);
            if (!existsLoc) {
                const tempOpt = document.createElement('option');
                tempOpt.value = locVal;
                tempOpt.textContent = nacLocInput?.value || 'Seleccionar';
                nacLocalidad.appendChild(tempOpt);
            }
            nacLocalidad.value = locVal;
            syncInputFromSelect(nacLocalidad);
        }
    }
};

nacEntidad?.addEventListener('change', async () => {
    resetSelect(nacMunicipio);
    resetSelect(nacLocalidad);
    if (!nacEntidad.value) return;

    const res = await fetch(`/api/municipios/${nacEntidad.value}`);
    const data = await res.json();
    fillSelect(nacMunicipio, 'Seleccionar', data, 'idMunicipio', 'nombreMunicipio');
});

nacMunicipio?.addEventListener('change', async () => {
    resetSelect(nacLocalidad);
    if (!nacMunicipio.value) return;

    const res = await fetch(`/api/localidades/${nacMunicipio.value}`);
    const data = await res.json();
    fillSelect(nacLocalidad, 'Seleccionar', data, 'idLocalidad', 'nombreLocalidad');
});

initNacimiento();

});
</script>
