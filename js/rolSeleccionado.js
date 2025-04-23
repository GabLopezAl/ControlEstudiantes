// document.addEventListener("DOMContentLoaded", function () {
//     const rolGuardado = localStorage.getItem("rolSeleccionado");

//     if (rolGuardado) {
//         const radio = document.querySelector(`input[name="rol"][value="${rolGuardado}"]`);
//         if (radio) {
//             radio.checked = true;
//         }
//     }
// });

document.addEventListener("DOMContentLoaded", function () {
    const radios = document.querySelectorAll('input[name="rol"]');

    // Restaurar del localStorage
    const rolGuardado = localStorage.getItem("rolSeleccionado");
    if (rolGuardado) {
        const radio = document.querySelector(`input[name="rol"][value="${rolGuardado}"]`);
        if (radio) {
            radio.checked = true;
        }
    }

    // Escuchar cambios en los radios
    radios.forEach(radio => {
        radio.addEventListener("change", () => {
            const rol = radio.value;

            // Guardar nuevo valor
            localStorage.setItem("rolSeleccionado", rol);

            // Redirigir a la página del rol con parámetro en la URL
            window.location.href = `registro${rol.charAt(0).toUpperCase() + rol.slice(1)}.html?rol=${rol}`;
        });
    });
});

