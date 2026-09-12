
  document.addEventListener('DOMContentLoaded', function () {
    const pwToggle = document.getElementById('pwToggle');
    const passwordInput = document.getElementById('password');

    if (pwToggle && passwordInput) {
      pwToggle.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent any accidental form triggers

        // Toggle type attribute
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

        // Optional: Update aria label for accessibility
        pwToggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
      });
    }
  });
