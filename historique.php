<?php
require_once 'config.php';
requireLogin();

$conn = getDbConnection();
$user_id = getCurrentUserId();

// Suppression sécurisée
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM historique WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    header("Location: historique.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM historique WHERE user_id = ? ORDER BY date_calcul DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$data = [];
$colors = [];

// Couleurs par classe pour le graphique
$classColors = [
    'A' => '#2ecc71',
    'B' => '#27ae60', 
    'C' => '#f1c40f',
    'D' => '#e67e22',
    'E' => '#d35400',
    'F' => '#e74c3c',
    'G' => '#8e0000'
];

while($row = $result->fetch_assoc()){
    $labels[] = $row['nom_batiment'];
    $data[] = $row['consommation'];
    $colors[] = $classColors[$row['classe']] ?? '#95a5a6';
}
$result->data_seek(0);

// Statistiques
$totalCalculs = $result->num_rows;
$totalConsommation = 0;
$result->data_seek(0);
while($row = $result->fetch_assoc()) {
    $totalConsommation += $row['consommation'];
}
$result->data_seek(0);
$moyenneConso = $totalCalculs > 0 ? round($totalConsommation / $totalCalculs, 1) : 0;

// Meilleure classe
$best = 'A';
$result->data_seek(0);
$orderClass = ['A'=>1,'B'=>2,'C'=>3,'D'=>4,'E'=>5,'F'=>6,'G'=>7];
while($row = $result->fetch_assoc()) {
    if($orderClass[$row['classe']] < $orderClass[$best]) $best = $row['classe'];
}
$result->data_seek(0);

// Distribution des classes
$classCount = ['A'=>0,'B'=>0,'C'=>0,'D'=>0,'E'=>0,'F'=>0,'G'=>0];
$result->data_seek(0);
while($row = $result->fetch_assoc()) {
    $classCount[$row['classe']]++;
}
$result->data_seek(0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique - Energy Calculator</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1abc9c 0%, #3498db 100%);
            padding: 24px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Glassmorphism Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding: 16px 28px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 80px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 500;
            color: white;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .logout-btn {
            background: rgba(231, 76, 60, 0.9);
            color: white;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .logout-btn:hover {
            background: #e74c3c;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(231,76,60,0.3);
        }

        /* Title Section */
        .title-section {
            margin-bottom: 28px;
        }

        .title-section h1 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 14px;
            letter-spacing: -0.5px;
        }

        .title-section h1 i {
            background: rgba(255,255,255,0.2);
            padding: 14px;
            border-radius: 20px;
            font-size: 1.3rem;
        }

        .breadcrumb {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            margin-top: 8px;
            margin-left: 70px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.25);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .stat-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
            letter-spacing: -1px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            margin-top: 6px;
            font-weight: 500;
        }

        /* Class Distribution */
        .class-distribution {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 28px;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .dist-title {
            color: white;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .dist-bars {
            display: flex;
            gap: 8px;
            height: 8px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .dist-bar {
            height: 100%;
            transition: width 0.5s ease;
        }

        .dist-labels {
            display: flex;
            justify-content: space-around;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.6);
        }

        /* Search */
        .search-wrapper {
            margin-bottom: 24px;
            position: relative;
        }

        .search-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(0,0,0,0.4);
            z-index: 1;
        }

        .search-wrapper input {
            width: 100%;
            padding: 14px 20px 14px 48px;
            border-radius: 60px;
            border: none;
            font-size: 14px;
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-family: 'Inter', sans-serif;
        }

        .search-wrapper input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(46,204,113,0.3);
            transform: translateY(-1px);
        }

        /* Table Container */
        .table-wrapper {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 4px;
            margin-bottom: 28px;
            border: 1px solid rgba(255,255,255,0.15);
            overflow: hidden;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 16px 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: rgba(0,0,0,0.2);
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        td {
            padding: 16px;
            text-align: center;
            color: white;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        tr:hover td {
            background: rgba(255,255,255,0.08);
        }

        /* Badges */
        .badge {
            padding: 6px 16px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .badge.A { background: #2ecc71; color: white; }
        .badge.B { background: #27ae60; color: white; }
        .badge.C { background: #f1c40f; color: #1a1a2e; }
        .badge.D { background: #e67e22; color: white; }
        .badge.E { background: #d35400; color: white; }
        .badge.F { background: #e74c3c; color: white; }
        .badge.G { background: #8e0000; color: white; }

        /* Delete Button */
        .delete-btn {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            display: inline-block;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .delete-btn:hover {
            color: #ff6b6b;
            background: rgba(231,76,60,0.2);
            transform: scale(1.05);
        }

        /* Chart Container */
        .chart-card {
            background: rgba(255,255,255,0.95);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .chart-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a2a3a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .legend {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            color: #555;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 4px;
        }

        canvas {
            max-height: 380px;
        }

        /* Button */
        .btn-new {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            padding: 14px 32px;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.25);
        }

        .btn-new:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: rgba(255,255,255,0.8);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 1000px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 16px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            th, td {
                font-size: 0.7rem;
                padding: 10px 8px;
            }
            .header {
                padding: 12px 20px;
            }
            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 550px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .title-section h1 {
                font-size: 1.5rem;
            }
            .breadcrumb {
                margin-left: 0;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.4);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.6);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr(htmlspecialchars(getCurrentUser()), 0, 1)) ?>
                </div>
                <span><?= htmlspecialchars(getCurrentUser()) ?></span>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>

        <!-- Title -->
        <div class="title-section">
            <h1>
                <i class="fas fa-chart-line"></i>
                Historique des calculs
            </h1>
            <div class="breadcrumb">
                <i class="fas fa-home"></i> Dashboard / Historique
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-value"><?= $totalCalculs ?></div>
                <div class="stat-label">Calculs effectués</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-simple"></i>
                </div>
                <div class="stat-value"><?= $moyenneConso ?></div>
                <div class="stat-label">Consommation moyenne (kWh/m²/an)</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value" style="color: #f1c40f;"><?= $best ?></div>
                <div class="stat-label">Meilleure classe</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-value"><?= $totalCalculs > 0 ? round(($classCount[$best]/$totalCalculs)*100) : 0 ?>%</div>
                <div class="stat-label">Performance classe <?= $best ?></div>
            </div>
        </div>

        <!-- Class Distribution -->
        <?php if($totalCalculs > 0): ?>
        <div class="class-distribution">
            <div class="dist-title">
                <i class="fas fa-chart-simple"></i>
                Distribution des classes énergétiques
            </div>
            <div class="dist-bars">
                <?php foreach(['A','B','C','D','E','F','G'] as $c): 
                    $width = $totalCalculs > 0 ? ($classCount[$c]/$totalCalculs)*100 : 0;
                ?>
                <div class="dist-bar" style="width: <?= $width ?>%; background: <?= $classColors[$c] ?>;"></div>
                <?php endforeach; ?>
            </div>
            <div class="dist-labels">
                <span>A</span><span>B</span><span>C</span><span>D</span><span>E</span><span>F</span><span>G</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Search -->
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="search" placeholder="Rechercher un bâtiment...">
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <div class="table-container">
                <?php if($totalCalculs > 0): ?>
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-building"></i> Bâtiment</th>
                            <th><i class="fas fa-arrows-alt"></i> Surface</th>
                            <th><i class="fas fa-bolt"></i> Consommation</th>
                            <th><i class="fas fa-chart-line"></i> Classe</th>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['nom_batiment']) ?></strong></td>
                            <td><?= number_format($row['surface_batiment'], 0) ?> m²</td>
                            <td><?= number_format($row['consommation'], 1) ?> kWh/m²/an</td>
                            <td><span class="badge <?= $row['classe'] ?>"><?= $row['classe'] ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['date_calcul'])) ?></td>
                            <td>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Supprimer ce calcul ?')" class="delete-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                             </td>
                         </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-database"></i>
                    <p>Aucun calcul enregistré</p>
                    <p style="font-size: 0.8rem; margin-top: 8px;">Effectuez un calcul sur la page d'accueil</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chart -->
        <?php if($totalCalculs > 0): ?>
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-chart-bar" style="color: #2ecc71;"></i>
                    Graphique des consommations énergétiques
                </div>
                <div class="legend">
                    <div class="legend-item"><div class="legend-color" style="background: #2ecc71;"></div><span>A</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #27ae60;"></div><span>B</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #f1c40f;"></div><span>C</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #e67e22;"></div><span>D</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #d35400;"></div><span>E</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #e74c3c;"></div><span>F</span></div>
                    <div class="legend-item"><div class="legend-color" style="background: #8e0000;"></div><span>G</span></div>
                </div>
            </div>
            <canvas id="chart"></canvas>
        </div>
        <?php endif; ?>

        <!-- Button -->
        <div style="text-align: center;">
            <a href="index.php" class="btn-new">
                <i class="fas fa-plus-circle"></i> Nouveau calcul
            </a>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById("search");
        if(searchInput) {
            searchInput.addEventListener("keyup", function() {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll("#dataTable tbody tr");
                rows.forEach(row => {
                    let text = row.cells[0].innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
        }
        
        // Chart with class colors
        <?php if($totalCalculs > 0): ?>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Consommation (kWh/m²/an)',
                    data: <?= json_encode($data) ?>,
                    backgroundColor: <?= json_encode($colors) ?>,
                    borderColor: <?= json_encode($colors) ?>,
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.65,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.85)',
                        titleColor: '#fff',
                        bodyColor: '#ddd',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                return `Consommation: ${context.parsed.y.toFixed(1)} kWh/m²/an`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Consommation (kWh/m²/an)',
                            color: '#666',
                            font: { weight: '600', size: 11 }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(0);
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bâtiments',
                            color: '#666',
                            font: { weight: '600', size: 11 }
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 35,
                            font: { size: 10 }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>