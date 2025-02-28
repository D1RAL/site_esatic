document.addEventListener("DOMContentLoaded", function () { 
    const roleSelection = document.getElementById("roleSelection");
    const roleSelect = document.getElementById("roleSelect");
    const confirmRoleBtn = document.getElementById("confirmRoleBtn");
    const formContainer = document.getElementById("formContainer");
    const formTitle = document.getElementById("formTitle");
    const registerFields = document.getElementById("registerFields");
    const confirmPasswordField = document.getElementById("confirmPasswordField");
    const submitBtnText = document.getElementById("submitBtnText");
    const authForm = document.getElementById("authForm");

    confirmRoleBtn.addEventListener("click", function () {
        let userRole = roleSelect.value;
        if (userRole) {
            roleSelection.style.display = "none"; // Masquer la sélection du rôle
            formContainer.style.display = "block"; // Afficher le formulaire
            
            // Mettre à jour le titre et le bouton du formulaire
            formTitle.textContent = `Inscription ${userRole.charAt(0).toUpperCase() + userRole.slice(1)}`;
            submitBtnText.textContent = "Valider";
            
            // Afficher les champs nécessaires pour l'inscription
            registerFields.style.display = "block"; 
            confirmPasswordField.style.display = "block"; 

            // Modifier l'action du formulaire pour pointer vers l'inscription
            authForm.action = "traitement_inscription.php"; 

            // Ajouter des champs supplémentaires en fonction du rôle
            updateFieldsByRole(userRole);
        }
    });

    function updateFieldsByRole(role) {
        registerFields.innerHTML = ""; // Réinitialiser les champs supplémentaires
        let additionalFields = "";

        if (role === "etudiant") {
            additionalFields += `
                <div class="input-group">
                    <input type="text" name="nom" class="input-field" placeholder="Nom">
                </div>

                <div class="input-group">
                    <input type="text" name="Prenom" class="input-field" placeholder="Prenom">
                </div>

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
                        <svg class="lucide-user" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <input type="text" name="nom" class="input-field" placeholder="Nom">
                    </div>
                    <div class="input-group">
                        <svg class="lucide-user" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <input type="text" name="prenom" class="input-field" placeholder="Prénom">
                    </div>
                </div>
                <div class="input-group">
                    <input type="email" name="email" class="input-field" placeholder="Email">
                </div>
                
            `;
        }

        // Injecter les nouveaux champs
        registerFields.innerHTML = additionalFields;
    }
});
