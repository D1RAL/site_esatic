document.addEventListener("DOMContentLoaded", function () {
    let isLogin = true; // Mode connexion par défaut
    let userRole = ""; // Rôle sélectionné

    // Sélection des éléments HTML
    const roleSelection = document.getElementById("roleSelection");
    const roleSelect = document.getElementById("roleSelect");
    const confirmRoleBtn = document.getElementById("confirmRoleBtn");
    const formContainer = document.getElementById("formContainer");
    const formTitle = document.getElementById("formTitle");
    const registerFields = document.getElementById("registerFields");
    const confirmPasswordField = document.getElementById("confirmPasswordField");
    const submitBtnText = document.getElementById("submitBtnText");
    const toggleModeBtn = document.getElementById("toggleMode");
    const authForm = document.getElementById("authForm");

    // Validation du choix du rôle et affichage du formulaire
    confirmRoleBtn.addEventListener("click", function () {
        userRole = roleSelect.value;
        if (userRole) {
            roleSelection.style.display = "none"; // Masquer la sélection du rôle
            formContainer.style.display = "block"; // Afficher le formulaire
            updateForm(); // Mettre à jour le formulaire en fonction du rôle
        }
    });

    // Gestion du basculement entre Connexion et Inscription

    toggleModeBtn.addEventListener("click", function () {
        isLogin = !isLogin;
        updateForm();
    })
    document.getElementById("confirmRoleBtn").addEventListener("click", function() {
        let roleSelection = document.getElementById("roleSelect").value;
        let roleContainer = document.getElementById("roleSelection");
        let formContainer = document.getElementById("formContainer");
        let registerFields = document.getElementById("registerFields");
        let confirmPasswordField = document.getElementById("confirmPasswordField");
        let submitBtnText = document.getElementById("submitBtnText");
    
        if (roleSelection) {
            roleContainer.style.display = "none"; // Masquer le choix du rôle
            formContainer.style.display = "block"; // Afficher le formulaire
            registerFields.style.display = "block"; // Afficher les champs Nom & Prénom
            confirmPasswordField.style.display = "block"; // Afficher le champ de confirmation du mot de passe
            submitBtnText.textContent = "Créer un compte"; // Modifier le texte du bouton
        }
    });
    ;

    // Fonction de mise à jour du formulaire en fonction du rôle et du mode
    function updateForm() {
        const roleText = userRole.charAt(0).toUpperCase() + userRole.slice(1);

        formTitle.textContent = `Inscription ${roleText}`;
        submitBtnText.textContent = isLogin ? "Se connecter" : "S'inscrire";
        toggleModeBtn.textContent = isLogin ? "Créer un compte" : "Déjà inscrit ? Se connecter";

        // Afficher/Masquer les champs d'inscription
        registerFields.style.display = isLogin ? "none" : "block";
        confirmPasswordField.style.display = isLogin ? "none" : "block";

        // Modifier l'action du formulaire (connexion.php ou inscription.php)
        authForm.action = isLogin ? "connexion.php" : "connexion.php";

        // Modifier dynamiquement les champs en fonction du rôle
        updateFieldsByRole(userRole, isLogin);
    }

    // Fonction pour modifier les champs en fonction du rôle
    function updateFieldsByRole(role, isLogin) {
        registerFields.innerHTML = ""; // Réinitialiser les champs
        let additionalFields = "";

        if (!isLogin) {
            additionalFields += `
                <div class="input-group">
                    <input type="text" name="nom" class="input-field" placeholder="Nom">
                </div>
                <div class="input-group">
                    <input type="text" name="prenom" class="input-field" placeholder="Prénom">
                </div>
            `;
        }

        if (role === "etudiant") {
            additionalFields += `
                <div class="input-group">
                    <input type="text" name="filiere" class="input-field" placeholder="Filière">
                </div>
                <div class="input-group">
                    <input type="text" name="niveau" class="input-field" placeholder="Niveau">
                </div>
            `;
        } else if (role === "professeur") {
            additionalFields += `
                <div class="input-group">
                    <input type="text" name="specialite" class="input-field" placeholder="Spécialité">
                </div>
            `;
        } else if (role === "administration") {
            additionalFields += `
                <div class="input-group">
                    <input type="text" name="fonction" class="input-field" placeholder="Fonction">
                </div>
            `;
        }

        // Injecter les nouveaux champs
        registerFields.innerHTML = additionalFields;
    }
});
