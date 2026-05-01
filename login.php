<?php
require_once 'config.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password == $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit();
            }
        }
        $error = "❌ Nom d'utilisateur ou mot de passe incorrect";
        $stmt->close();
        $conn->close();
    } else {
        $error = "⚠️ Veuillez remplir tous les champs";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Energy Calculator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            /* 🎨 Dégradé officiel de l'application */
            background: linear-gradient(135deg, #1abc9c 0%, #3498db 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* ✨ Particules décoratives flottantes */
        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            animation: float-particle 15s infinite ease-in-out;
        }

        .particle:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { top: 20%; right: 15%; animation-delay: 2s; }
        .particle:nth-child(3) { bottom: 15%; left: 20%; animation-delay: 4s; }
        .particle:nth-child(4) { bottom: 25%; right: 10%; animation-delay: 1s; }
        .particle:nth-child(5) { top: 40%; left: 5%; animation-delay: 3s; }
        .particle:nth-child(6) { top: 60%; right: 20%; animation-delay: 5s; }

        @keyframes float-particle {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.4; }
            50% { transform: translateY(-30px) scale(1.2); opacity: 0.8; }
        }

        /* Overlay subtil pour profondeur */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 20%, rgba(255,255,255,0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 70% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-container {
            width: 420px;
            padding: 50px 45px;
            border-radius: 28px;
            background: #ffffff;
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.8s ease;
            position: relative;
            z-index: 10;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 🎨 LOGO PROFESSIONNEL ANIMÉ */
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #ecf0f1;
        }

        .logo-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
        }

        .logo-icon {
            width: 100%;
            height: 100%;
            /* Dégradé cohérent avec le background */
            background: linear-gradient(135deg, #1abc9c, #16a085);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 50px rgba(26, 188, 156, 0.4);
            animation: logo-float 3.5s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        /* Effet de brillance premium */
        .logo-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.5) 50%,
                transparent 70%
            );
            transform: rotate(45deg) translateX(-150%);
            animation: shine 4.5s ease-in-out infinite;
        }

        @keyframes shine {
            0%, 100% { transform: rotate(45deg) translateX(-150%); }
            50% { transform: rotate(45deg) translateX(150%); }
        }

        @keyframes logo-float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1.5deg); }
        }

        .logo-icon i {
            font-size: 44px;
            color: white;
            position: relative;
            z-index: 2;
            animation: leaf-sway 2.5s ease-in-out infinite;
        }

        @keyframes leaf-sway {
            0%, 100% { transform: rotate(-2deg); }
            50% { transform: rotate(2deg); }
        }

        /* Anneau décoratif subtil */
        .logo-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 125px;
            height: 125px;
            border: 2px solid rgba(26, 188, 156, 0.25);
            border-radius: 50%;
            animation: pulse-ring 3s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.25; }
            50% { transform: translate(-50%, -50%) scale(1.08); opacity: 0.4; }
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .logo-subtitle {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: 400;
        }

        .form-group { margin-bottom: 26px; }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #34495e;
        }

        .input-wrapper { position: relative; }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            font-size: 17px;
            transition: color 0.3s ease;
            z-index: 2;
        }

        .form-group input {
            width: 100%;
            padding: 15px 18px 15px 52px;
            border-radius: 14px;
            border: 2px solid #ecf0f1;
            background: #f8f9fa;
            font-size: 15px;
            color: #2c3e50;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            background: #ffffff;
            border-color: #1abc9c;
            box-shadow: 0 0 0 5px rgba(26, 188, 156, 0.15);
        }

        .form-group input:focus + i,
        .input-wrapper:focus-within i {
            color: #1abc9c;
        }

        .form-group input::placeholder { color: #bdc3c7; }

        .btn-login {
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            /* Dégradé cohérent avec le background */
            background: linear-gradient(135deg, #1abc9c, #16a085);
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.35s ease;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(26, 188, 156, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transition: left 0.6s ease;
        }

        .btn-login:hover::before { left: 100%; }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(26, 188, 156, 0.45);
        }
        .btn-login:active { transform: translateY(-1px); }

        .error-message {
            background: linear-gradient(135deg, #ffeaea, #fdd);
            color: #c0392b;
            padding: 15px 22px;
            border-radius: 14px;
            margin-bottom: 28px;
            font-size: 14px;
            text-align: center;
            animation: shake 0.5s ease;
            border-left: 4px solid #e74c3c;
            font-weight: 500;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .footer-note {
            text-align: center;
            margin-top: 35px;
            font-size: 12px;
            color: #95a5a6;
        }

        .footer-note i { color: #1abc9c; margin-right: 5px; }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                width: 92%;
                padding: 40px 30px;
            }
            .logo-icon { width: 85px; height: 85px; }
            .logo-icon i { font-size: 38px; }
            .logo-text { font-size: 24px; }
        }
    </style>
</head>
<body>
    <!-- ✨ Particules décoratives -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-container">
        
        <!-- 🎨 LOGO SECTION ANIMÉE -->
        <div class="logo-section">
            <div class="logo-wrapper">
                <div class="logo-ring"></div>
                <div class="logo-icon">
                    <i class="fas fa-leaf"></i>
                </div>
            </div>
            <div class="logo-text">Energy Calculator</div>
            <div class="logo-subtitle">TP SIA 2026 • M1-IL</div>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" 
                        placeholder="Entrez votre identifiant" required 
                        autocomplete="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <i class="fas fa-user"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-key"></i> Mot de passe</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" 
                        placeholder="••••••••" required autocomplete="current-password">
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>

        <div class="footer-note">
            <i class="fas fa-shield-alt"></i> Application sécurisée • Département d'Informatique
        </div>

    </div>
</body>
</html>