<?php
$nom = $_GET['nom'] ?? 'Inconnu';
$surface = $_GET['surface'] ?? 0;
$dep = $_GET['dep'] ?? 0;
$conso = $_GET['conso'] ?? 0;
$classe = $_GET['classe'] ?? 'G';

$colors = ['A'=>'#2ecc71', 'B'=>'#27ae60', 'C'=>'#f1c40f', 'D'=>'#e67e22', 'E'=>'#d35400', 'F'=>'#e74c3c', 'G'=>'#8e0000'];
$color = $colors[$classe] ?? '#8e0000';

if ($conso < 70) $percent = 5;
else if ($conso <= 110) $percent = 20;
else if ($conso <= 180) $percent = 35;
else if ($conso <= 250) $percent = 50;
else if ($conso <= 330) $percent = 65;
else if ($conso <= 420) $percent = 80;
else $percent = 95;

$messages = [
    'A' => '🔥 Excellent rendement énergétique !',
    'B' => '👍 Très bonne performance',
    'C' => '📊 Performance correcte',
    'D' => '⚠️ Consommation moyenne',
    'E' => '🔧 Peut être amélioré',
    'F' => '🚨 Consommation élevée',
    'G' => '❌ Très mauvaise performance'
];
$message = $messages[$classe] ?? 'Classification standard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat - Energy Calculator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1abc9c, #3498db);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .card {
            max-width: 450px;
            width: 100%;
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            background: rgba(255,255,255,0.15);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            color: white;
            text-align: center;
            animation: fadeIn 1s ease;
        }
        h2 { margin-bottom: 20px; }
        .info { font-size: 14px; opacity: 0.9; margin-top: 15px; }
        .value { font-size: 22px; font-weight: bold; margin-top: 5px; }
        .classe {
            font-size: 60px;
            font-weight: bold;
            margin: 20px 0;
            padding: 15px;
            border-radius: 15px;
            background: <?= $color ?>;
        }
        .bar {
            height: 12px;
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
        }
        .progress {
            height: 100%;
            background: linear-gradient(90deg, #2ecc71, #27ae60, #f1c40f, #e67e22, #d35400, #e74c3c, #8e0000);
            width: 0;
            transition: width 1s ease;
            border-radius: 10px;
        }
        .labels {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-top: 5px;
        }
        .message {
            margin: 20px 0;
            padding: 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            font-style: italic;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 25px;
            background: #2ecc71;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn:hover { background: #27ae60; transform: scale(1.05); }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2><i class="fas fa-chart-line"></i> Résultat du calcul</h2>
        
        <div class="info">🏢 Nom du bâtiment</div>
        <div class="value"><?= htmlspecialchars($nom) ?></div>
        
        <div class="info">📐 Surface</div>
        <div class="value"><?= number_format($surface, 0) ?> m²</div>
        
        <div class="info">💨 Déperdition thermique</div>
        <div class="value"><?= number_format($dep, 0) ?> W/°C</div>
        
        <div class="info">⚡ Consommation énergétique</div>
        <div class="value"><?= number_format($conso, 1) ?> kWh/m²/an</div>
        
        <div class="info">🏅 Classe énergétique</div>
        <div class="classe"><?= $classe ?></div>
        
        <div class="bar">
            <div class="progress" id="progressBar"></div>
        </div>
        <div class="labels">
            <span>A</span><span>B</span><span>C</span><span>D</span><span>E</span><span>F</span><span>G</span>
        </div>
        
        <div class="message">
            <i class="fas fa-info-circle"></i> <?= $message ?>
        </div>
        
        <a href="index.php" class="btn">
            <i class="fas fa-arrow-left"></i> Nouveau calcul
        </a>
        <a href="historique.php" class="btn" style="background: #3498db; margin-left: 10px;">
            <i class="fas fa-history"></i> Historique
        </a>
    </div>
    
    <script>
        window.onload = () => {
            setTimeout(() => {
                document.getElementById("progressBar").style.width = "<?= $percent ?>%";
            }, 100);
        };
    </script>
</body>
</html>