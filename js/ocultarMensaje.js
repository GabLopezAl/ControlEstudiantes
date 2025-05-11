// Ocultar el mensaje si se hace clic en cualquier botón (excepto el de submit)
document.querySelectorAll("button").forEach(btn => {
    btn.addEventListener("click", () => {
        const mensaje = document.getElementById("mensaje");
        if (mensaje) {
            mensaje.style.display = "none";
        }
    });
});