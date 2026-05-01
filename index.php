<?php
require_once 'config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title>Energy Calculator - Diagnostic Énergétique</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        background: linear-gradient(135deg, #1abc9c 0%, #3498db 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .container {
        width: 100%;
        max-width: 580px;
        padding: 30px;
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.2), 0 0 0 1px rgba(255,255,255,0.2);
        color: white;
        animation: fadeIn 0.6s ease;
    }

    @media (max-width: 640px) {
        .container {
            max-width: 95%;
            padding: 20px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 18px;
        }
    }

    /* Header */
    .app-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .logo-icon {
        font-size: 2.5rem;
        background: rgba(255,255,255,0.2);
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }

    h2 {
        font-size: 1.8rem;
        font-weight: 600;
        letter-spacing: -0.5px;
        margin-bottom: 5px;
    }

    .subtitle {
        font-size: 0.8rem;
        opacity: 0.85;
        font-weight: 400;
    }

    /* User Bar */
    .user-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding: 8px 12px;
        background: rgba(255,255,255,0.12);
        border-radius: 60px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .user-info i {
        font-size: 1rem;
    }

    .logout-btn {
        background: rgba(231, 76, 60, 0.85);
        color: white;
        padding: 5px 16px;
        border-radius: 40px;
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .logout-btn:hover {
        background: #e74c3c;
        transform: translateY(-1px);
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .form-label i {
        width: 22px;
        font-size: 0.9rem;
    }

    .label-text {
        flex: 1;
    }

    .coeff-badge {
        background: rgba(255,255,255,0.2);
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-family: monospace;
    }

    input, select {
        width: 100%;
        padding: 12px 14px;
        border-radius: 14px;
        border: none;
        background: rgba(255,255,255,0.95);
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        color: #2c3e50;
    }

    input:focus, select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.4);
        transform: translateY(-1px);
        background: white;
    }

    input::placeholder {
        color: rgba(0,0,0,0.35);
        font-weight: 400;
        font-size: 12px;
    }

    /* Double champ (select + input) */
    .double-input {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .double-input select {
        width: 45%;
        background: rgba(255,255,255,0.95);
    }

    .double-input input {
        width: 55%;
    }

    /* Button */
    .calculate-btn {
        width: 100%;
        padding: 14px;
        border-radius: 60px;
        background: #2ecc71;
        color: white;
        border: none;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .calculate-btn:hover {
        background: #27ae60;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    /* History Link */
    .history-link {
        margin-top: 22px;
        text-align: center;
    }

    .history-link a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        border-radius: 40px;
        background: rgba(255,255,255,0.12);
        transition: all 0.2s ease;
    }

    .history-link a:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-1px);
    }

    /* Separator */
    .separator {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        margin: 20px 0 5px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Info tip */
    .info-tip {
        font-size: 0.7rem;
        text-align: center;
        margin-top: 15px;
        opacity: 0.7;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="container">
    
    <div class="user-bar">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars(getCurrentUser()) ?></span>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>

    <div class="app-header">
        <div class="logo-icon">
            <i class="fas fa-leaf"></i>
        </div>
        <h2>Energy Calculator</h2>
        <div class="subtitle">Calcul de performance énergétique</div>
    </div>

    <form onsubmit="return calculer()">

        <!-- Nom bâtiment -->
        <div class="form-group">
            <div class="form-label">
                <i class="fas fa-building"></i>
                <span class="label-text">Nom du bâtiment</span>
            </div>
            <input type="text" id="nomBatiment" placeholder="Ex: Maison A, Immeuble B..." required>
        </div>

        <!-- Surface -->
        <div class="form-group">
            <div class="form-label">
                <i class="fas fa-arrows-alt"></i>
                <span class="label-text">Surface habitable</span>
                <span class="coeff-badge">m²</span>
            </div>
            <input type="number" id="surfaceBatiment" placeholder="Surface totale en m²" min="1" required>
        </div>

        <!-- Mur -->
        <div class="form-group">
            <div class="form-label">
                <i class="fas fa-wall"></i>
                <span class="label-text">Murs extérieurs</span>
                <span class="coeff-badge">U (W/m²·K)</span>
            </div>
            <div class="double-input">
                <select id="typeMur">
                    <option value="0.6">🏠 Mur isolé (0.60)</option>
                    <option value="1.8">🏚️ Mur sans isolation (1.80)</option>
                </select>
                <input type="number" id="surfaceMur" placeholder="Surface (m²)" min="1" required>
            </div>
        </div>

        <!-- Plancher -->
        <div class="form-group">
            <div class="form-label">
                <i class="fas fa-layer-group"></i>
                <span class="label-text">Plancher bas</span>
                <span class="coeff-badge">U (W/m²·K)</span>
            </div>
            <div class="double-input">
                <select id="typePlancher">
                    <option value="0.5">🪵 Plancher bois (0.50)</option>
                    <option value="1.2">🧱 Plancher béton (1.20)</option>
                </select>
                <input type="number" id="surfacePlancher" placeholder="Surface (m²)" min="1" required>
            </div>
        </div>

        <!-- Ouvrant -->
        <div class="form-group">
            <div class="form-label">
                <i class="fas fa-door-open"></i>
                <span class="label-text">Menuiseries</span>
                <span class="coeff-badge">U (W/m²·K)</span>
            </div>
            <div class="double-input">
                <select id="typeOuvrant">
                    <option value="2.5">🪟 Double vitrage (2.50)</option>
                    <option value="4.5">🪞 Simple vitrage (4.50)</option>
                </select>
                <input type="number" id="surfaceOuvrant" placeholder="Surface (m²)" min="1" required>
            </div>
        </div>

        <!-- Toiture -->
        <div class="form-group">
            <div class="form-label">
                <i class="fas fa-roof"></i>
                <span class="label-text">Toiture</span>
                <span class="coeff-badge">U (W/m²·K)</span>
            </div>
            <div class="double-input">
                <select id="typeToiture">
                    <option value="0.4">❄️ Toiture isolée (0.40)</option>
                    <option value="2.5">🌡️ Toiture normale (2.50)</option>
                </select>
                <input type="number" id="surfaceToiture" placeholder="Surface (m²)" min="1" required>
            </div>
        </div>

        <button type="submit" class="calculate-btn">
            <i class="fas fa-calculator"></i> Calculer
        </button>

    </form>

    <div class="separator"></div>

    <div class="history-link">
        <a href="historique.php">
            <i class="fas fa-chart-line"></i> Voir l'historique
        </a>
    </div>

    <div class="info-tip">
        <i class="fas fa-info-circle"></i> Plus le coefficient est bas, meilleure est l'isolation
    </div>
</div>

<script>
function calculer() {
    let nom = document.getElementById("nomBatiment").value;
    let surfaceBat = parseFloat(document.getElementById("surfaceBatiment").value);

    let mur = parseFloat(document.getElementById("surfaceMur").value);
    let plancher = parseFloat(document.getElementById("surfacePlancher").value);
    let ouvrant = parseFloat(document.getElementById("surfaceOuvrant").value);
    let toiture = parseFloat(document.getElementById("surfaceToiture").value);

    if(nom == ""){
        alert("⚠️ Veuillez saisir le nom du bâtiment");
        return false;
    }

    if(isNaN(surfaceBat) || surfaceBat <= 0){
        alert("⚠️ Veuillez entrer une surface valide");
        return false;
    }

    if(isNaN(mur) || isNaN(plancher) || isNaN(ouvrant) || isNaN(toiture)){
        alert("⚠️ Tous les champs doivent être remplis");
        return false;
    }

    let coefMur = parseFloat(document.getElementById("typeMur").value);
    let coefPlancher = parseFloat(document.getElementById("typePlancher").value);
    let coefOuvrant = parseFloat(document.getElementById("typeOuvrant").value);
    let coefToiture = parseFloat(document.getElementById("typeToiture").value);

    let depMur = mur * coefMur;
    let depPlancher = plancher * coefPlancher;
    let depOuvrant = ouvrant * coefOuvrant;
    let depToiture = toiture * coefToiture;

    let total = depMur + depPlancher + depOuvrant + depToiture;
    
    // Formule calibrée
    let consommation = (total * 85) / surfaceBat;

    let classe = "";
    if (consommation < 70) classe = "A";
    else if (consommation <= 110) classe = "B";
    else if (consommation <= 180) classe = "C";
    else if (consommation <= 250) classe = "D";
    else if (consommation <= 330) classe = "E";
    else if (consommation <= 420) classe = "F";
    else classe = "G";

    alert("📊 RÉSULTAT DU CALCUL\n\n" +
          "🏢 Bâtiment: " + nom + "\n" +
          "📐 Surface: " + surfaceBat + " m²\n" +
          "💨 Déperdition: " + total.toFixed(1) + " W/°C\n" +
          "⚡ Consommation: " + consommation.toFixed(1) + " kWh/m²/an\n" +
          "🏅 Classe: " + classe);

    fetch("insert.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `nom=${encodeURIComponent(nom)}&surface=${surfaceBat}&deperdition=${total}&consommation=${consommation}&classe=${classe}`
    });

    window.location.href = 
    "result.php?nom=" + encodeURIComponent(nom) +
    "&surface=" + surfaceBat +
    "&dep=" + total +
    "&conso=" + consommation +
    "&classe=" + classe;

    return false;
}
</script>

</body>
</html>