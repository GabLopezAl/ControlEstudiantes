// document.addEventListener("DOMContentLoaded", function () {
//     const radios = document.querySelectorAll('input[name="rol"]');
//     radios.forEach(radio => {
//         radio.addEventListener("change", () => {
//             const rol = radio.value;

//             // Guardar el rol en localStorage
//             localStorage.setItem("rolSeleccionado", rol);

//             // Redirigir a la página correspondiente
//             if (rol === "admin") {
//                 window.location.href = "registroAdmin.html";
//             } else if (rol === "alumno") {
//                 window.location.href = "registroAlumno.html";
//             } else if (rol === "maestro") {
//                 window.location.href = "registroMaestro.html";
//             }
//         });
//     });
// });

document.addEventListener("DOMContentLoaded", function () {
    const radios = document.querySelectorAll('input[name="rol"]');
    radios.forEach(radio => {
        radio.addEventListener("change", () => {
            const rol = radio.value;

            if (rol === "admin") {
                window.location.href = "registroAdmin.html";
            } else if (rol === "alumno") {
                window.location.href = "registroAlumno.html";
            } else if (rol === "maestro") {
                window.location.href = "registroMaestro.html";
            }

        });
    });
});


// document.addEventListener("DOMContentLoaded", function () {
//     const radios = document.querySelectorAll('input[name="rol"]');
//     radios.forEach(radio => {
//         radio.addEventListener("change", () => {
//             const rol = radio.value;

//             // Redirigir a la página correspondiente
//             if (rol === "admin") {
//                 window.location.href = "registroAdmin.html?rol=" + rol;
//                 // Asignar el valor al campo oculto del formulario
//                 document.getElementById("rol").value = rol;
//             } else if (rol === "alumno") {
//                 window.location.href = "registroAlumno.html?rol=" + rol;
//                 // Asignar el valor al campo oculto del formulario
//                 document.getElementById("rol").value = rol;
//             } else if (rol === "maestro") {
//                 window.location.href = "registroMaestro.html?rol=" + rol;
//                 // Asignar el valor al campo oculto del formulario
//                 document.getElementById("rol").value = rol;
//             }
//         });
//     });



// // Asignar rol si viene por URL (ej: ?rol=admin)
// const params = new URLSearchParams(window.location.search);
// if (params.has("rol")) {
//     const rol = params.get("rol");
//     document.getElementById("rol").value = rol;
// }
// });

