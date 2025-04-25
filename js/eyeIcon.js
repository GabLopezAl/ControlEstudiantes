function togglePassword() {
    const input = document.getElementById("password");
    const toggle = document.getElementById("togglePassword");
  
    if (input.type === "password") {
      input.type = "text";
      toggle.textContent = "🔓";
    } else {
      input.type = "password";
      toggle.textContent = "🔒";
    }
  }