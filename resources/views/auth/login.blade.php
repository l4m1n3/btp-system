<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --light-bg: #f8f9fa;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
        }
        
        .login-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--card-shadow);
            background-color: white;
        }
        
        .login-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            border-bottom: none;
        }
        
        .card-header h3 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .card-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .card-body {
            padding: 2.5rem;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 10px;
            border: 1.5px solid #e1e5eb;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .input-group-text {
            background-color: transparent;
            border-right: none;
        }
        
        .password-toggle {
            cursor: pointer;
            background-color: transparent;
            border-left: none;
        }
        
        .btn-login {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background-color: #e1e5eb;
        }
        
        .divider-text {
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 1.5rem;
        }
        
        .social-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: 1.5px solid #e1e5eb;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .social-btn:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .social-btn.google {
            color: #db4437;
        }
        
        .social-btn.facebook {
            color: #4267B2;
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        
        .links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .links a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
        
        .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            border-left: 4px solid #198754;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .logo-icon {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .password-container {
            position: relative;
        }
        
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 5;
        }
        
        .footer {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem;
            }
            
            .social-login {
                flex-direction: column;
            }
            
            .links {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-hard-hat"></i> <!-- Avec une grue -->
                </div>
                <h2 class="mb-2">BTP-Pro </h2>
               <p class="company-subtitle">Gestion de chantiers & Dépenses</p>
            </div>
            
            <div class="login-card">
                <div class="card-header">
                    <h3><i class="fas fa-sign-in-alt me-2"></i>Connexion</h3>
                    <p>Entrez vos identifiants pour accéder à votre espace</p>
                </div>
                
                <div class="card-body">
                    <!-- Messages d'alerte -->
                    <div id="alertContainer"></div>
                    
                    <!-- Formulaire de connexion -->
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label">Adresse Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email"
                                       placeholder="votre@email.com"
                                       required>
                            </div>
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="password-container">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password"
                                           placeholder="Votre mot de passe"
                                           required>
                                    <button type="button" class="password-toggle-btn" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback" id="passwordError"></div>
                        </div>
                        
                        <div class="mb-4 form-check d-flex justify-content-between">
                            <div>
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Se souvenir de moi</label>
                            </div>
                            <a href="#" id="forgotPassword">Mot de passe oublié ?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-login w-100 mb-4">
                            <span >Se connecter</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                        </button>
                        
                    </form>
                </div>
            </div>
             
            <div class="footer">
                <p>&copy; {{ date('Y') }} CFSIS Globale services. Tous droits réservés.</p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Références aux éléments DOM
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const alertContainer = document.getElementById('alertContainer');
            const forgotPasswordLink = document.getElementById('forgotPassword');
            const registerLink = document.getElementById('registerLink');
            
            // Basculer la visibilité du mot de passe
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Simuler la soumission du formulaire
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validation des champs
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                let isValid = true;
                
                // Réinitialiser les erreurs
                document.getElementById('emailError').textContent = '';
                document.getElementById('email').classList.remove('is-invalid');
                document.getElementById('passwordError').textContent = '';
                document.getElementById('password').classList.remove('is-invalid');
                
                // Validation email
                if (!email) {
                    document.getElementById('emailError').textContent = 'L\'adresse email est requise';
                    document.getElementById('email').classList.add('is-invalid');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    document.getElementById('emailError').textContent = 'Veuillez entrer une adresse email valide';
                    document.getElementById('email').classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validation mot de passe
                if (!password) {
                    document.getElementById('passwordError').textContent = 'Le mot de passe est requis';
                    document.getElementById('password').classList.add('is-invalid');
                    isValid = false;
                } else if (password.length < 6) {
                    document.getElementById('passwordError').textContent = 'Le mot de passe doit contenir au moins 6 caractères';
                    document.getElementById('password').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) return;
                
                // Afficher l'indicateur de chargement
                submitBtn.disabled = true;
                submitText.textContent = 'Connexion en cours...';
                submitSpinner.classList.remove('d-none');
                
                // Simuler une requête serveur (à remplacer par une vraie requête AJAX)
                setTimeout(() => {
                    // Simulation de réponse du serveur
                    const success = Math.random() > 0.2; // 80% de chance de succès
                    
                    if (success) {
                        showAlert('Connexion réussie ! Redirection en cours...', 'success');
                        
                        // Redirection simulée
                        setTimeout(() => {
                            window.location.href = 'dashboard.html';
                        }, 1500);
                    } else {
                        showAlert('Email ou mot de passe incorrect. Veuillez réessayer.', 'danger');
                        
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitText.textContent = 'Se connecter';
                        submitSpinner.classList.add('d-none');
                    }
                }, 1500);
            });
            
            // Fonction pour afficher les alertes
            function showAlert(message, type) {
                alertContainer.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                // Supprimer automatiquement l'alerte après 5 secondes
                setTimeout(() => {
                    const alert = document.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
            
            // Fonction de validation d'email
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            // Gestionnaire pour "Mot de passe oublié"
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                const email = document.getElementById('email').value;
                
                if (!email) {
                    showAlert('Veuillez entrer votre adresse email pour réinitialiser votre mot de passe.', 'info');
                    document.getElementById('email').focus();
                    return;
                }
                
                showAlert(`Un email de réinitialisation a été envoyé à ${email} (simulation).`, 'info');
            });
            
            // Gestionnaire pour "S'inscrire"
            registerLink.addEventListener('click', function(e) {
                e.preventDefault();
                showAlert('Fonctionnalité d\'inscription non implémentée dans cette démo.', 'info');
            });
            
            // Gestionnaires pour les boutons de connexion sociale
            document.querySelectorAll('.social-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const provider = this.classList.contains('google') ? 'Google' : 'Facebook';
                    showAlert(`Connexion avec ${provider} non implémentée dans cette démo.`, 'info');
                });
            });
            
            // Animation d'entrée pour la carte
            const card = document.querySelector('.login-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>