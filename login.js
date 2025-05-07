document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const loginMessageDiv = document.getElementById('login-message');

    // --- VERY INSECURE - Hardcoded Credentials for Demo Only ---
    const validUsername = 'admin';
    const validPassword = 'password123'; // Change this!

    loginForm.addEventListener('submit', (event) => {
        event.preventDefault(); // Prevent default form submission

        const enteredUsername = usernameInput.value;
        const enteredPassword = passwordInput.value;

        if (enteredUsername === validUsername && enteredPassword === validPassword) {
            // Login successful
            loginMessageDiv.style.display = 'none'; // Hide error message
            // Store login status (use sessionStorage for session-only persistence)
            sessionStorage.setItem('isLoggedIn_EliteFootwear', 'true');
            // Redirect to the new dashboard page
            window.location.href = 'dashboard.html'
        } else {
            // Login failed
            loginMessageDiv.textContent = 'Invalid username or password.';
            loginMessageDiv.style.display = 'block'; // Show error message
            sessionStorage.removeItem('isLoggedIn_EliteFootwear'); // Ensure flag is removed on failure
        }
    });
});