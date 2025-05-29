document.addEventListener('DOMContentLoaded', function() {
    
    const registrationForm = document.querySelector('.registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', validateRegistrationForm);
    }


    const loginForm = document.querySelector('.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }
});

function validateRegistrationForm(e) {
    const fullname = document.getElementById('fullname').value;
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const voterId = document.getElementById('voter_id').value;
    const phone = document.getElementById('phone').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    let isValid = true;
    let errorMessage = '';

    
    if (!/^[A-Za-z\s]{3,}$/.test(fullname)) {
        errorMessage += 'Full name should contain only letters and spaces (minimum 3 characters)\n';
        isValid = false;
    }


    if (!/^[A-Za-z0-9]{4,20}$/.test(username)) {
        errorMessage += 'Username should be 4-20 characters long and contain only letters and numbers\n';
        isValid = false;
    }

   
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errorMessage += 'Please enter a valid email address\n';
        isValid = false;
    }

  
    if (!/^[A-Za-z0-9]{8,14}$/.test(voterId)) {
        errorMessage += 'Voter ID should be 8-14 characters long and contain only letters and numbers\n';
        isValid = false;
    }

   
    if (!/^\d{11}$/.test(phone)) {
        errorMessage += 'Phone number should be 11 digits\n';
        isValid = false;
    }


    if (password.length < 6) {
        errorMessage += 'Password must be at least 6 characters long\n';
        isValid = false;
    }

  
    if (password !== confirmPassword) {
        errorMessage += 'Passwords do not match\n';
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
        alert(errorMessage);
    }
}

function validateLoginForm(e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    let isValid = true;
    let errorMessage = '';

   
    if (username.trim() === '') {
        errorMessage += 'Username is required\n';
        isValid = false;
    }

  
    if (password.trim() === '') {
        errorMessage += 'Password is required\n';
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
        alert(errorMessage);
    }
}


function showError(inputElement, message) {
    const formGroup = inputElement.parentElement;
    const errorDiv = formGroup.querySelector('.error-message') || document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    if (!formGroup.querySelector('.error-message')) {
        formGroup.appendChild(errorDiv);
    }
}