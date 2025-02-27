// État global
let isLogin = true;
let isLoading = false;

// Éléments DOM
const form = document.getElementById('authForm');
const registerFields = document.getElementById('registerFields');
const confirmPasswordField = document.getElementById('confirmPasswordField');
const rememberMeGroup = document.getElementById('rememberMeGroup');
const toggleModeBtn = document.getElementById('toggleMode');
const formTitle = document.getElementById('formTitle');
const submitBtnText = document.getElementById('submitBtnText');
const successModal = document.getElementById('successModal');
const successMessage = document.getElementById('successMessage');

// Gestion des mots de passe
document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const icon = this.querySelector('svg');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    });
});

// Changement de mode (connexion/inscription)
toggleModeBtn.addEventListener('click', function() {
    isLogin = !isLogin;
    registerFields.style.display = isLogin ? 'none' : 'block';
    confirmPasswordField.style.display = isLogin ? 'none' : 'block';
    rememberMeGroup.style.display = isLogin ? 'block' : 'none';
    formTitle.textContent = isLogin ? 'Connexion Professeur' : 'Inscription Professeur';
    submitBtnText.textContent = isLogin ? 'Se connecter' : "S'inscrire";
    this.textContent = isLogin ? 'Créer un compte' : 'Déjà inscrit ? Se connecter';
    
    // Reset form
    form.reset();
    clearErrors();
});

// Validation du formulaire
function validateForm() {
    let isValid = true;
    clearErrors();

    if (!isLogin) {
        const requiredFields = ['nom', 'prenom', 'dateNaissance', 'email'];
        requiredFields.forEach(field => {
            const input = form.elements[field];
            if (!input.value) {
                showError(input, `Le ${field} est requis`);
                isValid = false;
            }
        });

        const password = form.elements.password.value;
        const confirmPassword = form.elements.confirmPassword.value;
        if (password !== confirmPassword) {
            showError(form.elements.confirmPassword, 'Les mots de passe ne correspondent pas');
            isValid = false;
        }
    }

    if (!form.elements.matricule.value) {
        showError(form.elements.matricule, 'Le matricule est requis');
        isValid = false;
    }
    if (!form.elements.password.value) {
        showError(form.elements.password, 'Le mot de passe est requis');
        isValid = false;
    }

    return isValid;
}

// Affichage des erreurs
function showError(input, message) {
    const group = input.closest('.input-group');
    const error = document.createElement('p');
    error.className = 'error-message';
    error.innerHTML = `
        <svg class="lucide-alert-circle" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" style="margin-right: 0.25rem;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        ${message}
    `;
    group.appendChild(error);
    input.style.borderColor = '#ef4444';
    input.style.backgroundColor = '#fef2f2';
}

// Nettoyage des erreurs
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(error => error.remove());
    document.querySelectorAll('.input-field').forEach(input => {
        input.style.borderColor = '#e5e7eb';
        input.style.backgroundColor = 'white';
    });
}

// Soumission du formulaire
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!validateForm() || isLoading) return;

    isLoading = true;
    const submitBtn = form.querySelector('.submit-btn');
    submitBtnText.innerHTML = '<div class="loading-spinner"></div>';
    submitBtn.disabled = true;

    // Simulation de l'envoi
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Affichage du succès
    successMessage.textContent = isLogin ? 'Connexion réussie!' : 'Inscription réussie!';
    successModal.classList.add('active');

    await new Promise(resolve => setTimeout(resolve, 2000));

    successModal.classList.remove('active');
    submitBtnText.textContent = isLogin ? 'Se connecter' : "S'inscrire";
    submitBtn.disabled = false;
    isLoading = false;
    form.reset();
});

// Animation des champs au chargement
document.querySelectorAll('.input-group').forEach((group, index) => {
    group.style.animationDelay = `${index * 0.1}s`;
});