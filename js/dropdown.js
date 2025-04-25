const userToggle = document.getElementById('userToggle');
const dropdownMenu = document.getElementById('dropdownMenu');

userToggle.addEventListener('click', function (event) {
    event.stopPropagation();
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', function () {
    dropdownMenu.style.display = 'none';
});

dropdownMenu.addEventListener('click', function (event) {
    event.stopPropagation(); // Evita que el menú se cierre al hacer clic dentro
});