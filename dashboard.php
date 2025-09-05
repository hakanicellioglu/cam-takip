<?php
require_once 'header.php';

// Sipariş verileri örnek olarak boş; gerçek uygulamada veritabanından çekilmeli
$orders = [];

$totalOrders   = count($orders);
$pendingCount  = 0;
$deliveredCount = 0;
$delayedCount  = 0;

foreach ($orders as $o) {
    $status = $o['status'] ?? '';
    switch ($status) {
        case 'Beklemede':
        case 'Bekleyen':
            $pendingCount++;
            break;
        case 'Teslim edildi':
        case 'Teslim Edilen':
            $deliveredCount++;
            break;
        case 'Gecikmiş':
        case 'Geciken':
            $delayedCount++;
            break;
    }
}
?>

<style>
    .dashboard-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .dashboard-card .card-body {
        padding: 2rem;
        text-align: center;
    }

    .dashboard-card .card-title {
        font-weight: 600;
        color: #A65D70;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .dashboard-card .card-text {
        font-weight: 700;
        font-size: 2.5rem;
        margin: 0;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-card.pending .card-text {
        background: linear-gradient(135deg, var(--warning-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-card.delivered .card-text {
        background: linear-gradient(135deg, var(--success-color), var(--primary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-card.delayed .card-text {
        background: linear-gradient(135deg, var(--danger-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-card .icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .dashboard-card.total .icon {
        color: var(--primary-color);
    }

    .dashboard-card.pending .icon {
        color: var(--warning-color);
    }

    .dashboard-card.delivered .icon {
        color: var(--success-color);
    }

    .dashboard-card.delayed .icon {
        color: var(--danger-color);
    }

    .page-title {
        font-weight: 700;
        color: #A65D70;
        margin-bottom: 2rem;
        font-size: 2.5rem;
        position: relative;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .table-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-top: 2rem;
    }

    .table-card .card-header {
        background: linear-gradient(135deg, #A65D70 0%, #D9849B 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
        font-weight: 600;
        font-size: 1.2rem;
    }

    .table-card .card-body {
        padding: 0;
    }

    .table {
        margin: 0;
        font-size: 0.95rem;
    }

    .table thead th {
        background: var(--light-bg);
        border: none;
        padding: 1rem;
        font-weight: 600;
        color: #A65D70;
        border-bottom: 2px solid #D9A3B1;
    }

    .table tbody td {
        padding: 1rem;
        border-color: #D9A3B1;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: rgba(166, 93, 112, 0.05);
    }

    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(166, 93, 112, 0.3);
    }

    .btn-outline-secondary {
        border-color: var(--secondary-color);
        color: var(--secondary-color);
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: var(--secondary-color);
        border-color: var(--secondary-color);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(217, 132, 155, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--secondary-color);
    }

    .empty-state .icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h4 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        margin: 0;
        font-size: 1rem;
    }

    /* Animation for cards */
    .dashboard-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
    .dashboard-card:nth-child(2) { animation-delay: 0.2s; }
    .dashboard-card:nth-child(3) { animation-delay: 0.3s; }
    .dashboard-card:nth-child(4) { animation-delay: 0.4s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-card {
        animation: fadeIn 0.8s ease-out 0.5s both;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
            text-align: center;
        }
        
        .dashboard-card .card-body {
            padding: 1.5rem;
        }
        
        .dashboard-card .card-text {
            font-size: 2rem;
        }
        
        .dashboard-card .icon {
            font-size: 2rem;
        }
    }
</style>

<div class='container main-content'>
    <h1 class='page-title'>
        <i class='fas fa-tachometer-alt me-3'></i>
        Panel Özeti
    </h1>
    
    <div class='row g-4'>
        <div class='col-12 col-sm-6 col-lg-3'>
            <div class='dashboard-card total'>
                <div class='card-body'>
                    <i class='fas fa-chart-bar icon'></i>
                    <h5 class='card-title'>Toplam Sipariş</h5>
                    <p class='card-text'><?php echo htmlspecialchars((string)$totalOrders); ?></p>
                </div>
            </div>
        </div>
        <div class='col-12 col-sm-6 col-lg-3'>
            <div class='dashboard-card pending'>
                <div class='card-body'>
                    <i class='fas fa-clock icon'></i>
                    <h5 class='card-title'>Bekleyen</h5>
                    <p class='card-text'><?php echo htmlspecialchars((string)$pendingCount); ?></p>
                </div>
            </div>
        </div>
        <div class='col-12 col-sm-6 col-lg-3'>
            <div class='dashboard-card delivered'>
                <div class='card-body'>
                    <i class='fas fa-check-circle icon'></i>
                    <h5 class='card-title'>Teslim Edilen</h5>
                    <p class='card-text'><?php echo htmlspecialchars((string)$deliveredCount); ?></p>
                </div>
            </div>
        </div>
        <div class='col-12 col-sm-6 col-lg-3'>
            <div class='dashboard-card delayed'>
                <div class='card-body'>
                    <i class='fas fa-exclamation-triangle icon'></i>
                    <h5 class='card-title'>Geciken</h5>
                    <p class='card-text'><?php echo htmlspecialchars((string)$delayedCount); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class='table-card'>
        <div class='card-header'>
            <i class='fas fa-list me-2'></i>
            Son Siparişler
        </div>
        <div class='card-body'>
            <?php if ($orders): ?>
                <div class='table-responsive'>
                    <table class='table table-hover'>
                        <thead>
                            <tr>
                                <th><i class='fas fa-hashtag me-1'></i> #</th>
                                <th><i class='fas fa-user me-1'></i> Müşteri</th>
                                <th><i class='fas fa-building me-1'></i> Şirket</th>
                                <th><i class='fas fa-ruler me-1'></i> Ölçü</th>
                                <th><i class='fas fa-sort-numeric-up me-1'></i> Adet</th>
                                <th><i class='fas fa-info-circle me-1'></i> Durum</th>
                                <th><i class='fas fa-calendar me-1'></i> Planlanan Teslim</th>
                                <th><i class='fas fa-cogs me-1'></i> İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars((string)$o['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($o['customer']); ?></td>
                                <td><?php echo htmlspecialchars($o['company']); ?></td>
                                <td><?php echo htmlspecialchars($o['size']); ?></td>
                                <td><span class='badge bg-primary'><?php echo htmlspecialchars((string)$o['qty']); ?></span></td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    $statusIcon = 'info-circle';
                                    switch($o['status']) {
                                        case 'Beklemede':
                                        case 'Bekleyen':
                                            $statusClass = 'warning';
                                            $statusIcon = 'clock';
                                            break;
                                        case 'Teslim edildi':
                                        case 'Teslim Edilen':
                                            $statusClass = 'success';
                                            $statusIcon = 'check-circle';
                                            break;
                                        case 'Gecikmiş':
                                        case 'Geciken':
                                            $statusClass = 'danger';
                                            $statusIcon = 'exclamation-triangle';
                                            break;
                                    }
                                    ?>
                                    <span class='badge bg-<?php echo $statusClass; ?>'>
                                        <i class='fas fa-<?php echo $statusIcon; ?> me-1'></i>
                                        <?php echo htmlspecialchars($o['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($o['delivery']); ?></td>
                                <td>
                                    <div class='btn-group' role='group'>
                                        <a href='order_view.php?id=<?php echo urlencode((string)$o['id']); ?>' 
                                           class='btn btn-outline-primary btn-sm'>
                                            <i class='fas fa-eye'></i>
                                        </a>
                                        <a href='order_edit.php?id=<?php echo urlencode((string)$o['id']); ?>' 
                                           class='btn btn-outline-secondary btn-sm'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class='empty-state'>
                    <i class='fas fa-inbox icon'></i>
                    <h4>Henüz sipariş bulunmuyor</h4>
                    <p>İlk siparişinizi oluşturmak için siparişler sayfasını ziyaret edin.</p>
                    <a href='siparisler.php' class='btn btn-primary mt-3'>
                        <i class='fas fa-plus me-2'></i>
                        Yeni Sipariş Oluştur
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class='row g-4 mt-4'>
        <div class='col-12'>
            <div class='dashboard-card'>
                <div class='card-body'>
                    <h5 class='card-title text-start mb-4'>
                        <i class='fas fa-bolt me-2'></i>
                        Hızlı İşlemler
                    </h5>
                    <div class='row g-3'>
                        <div class='col-6 col-md-3'>
                            <a href='siparisler.php' class='btn btn-outline-primary w-100'>
                                <i class='fas fa-plus-circle d-block mb-2' style='font-size: 1.5rem;'></i>
                                Yeni Sipariş
                            </a>
                        </div>
                        <div class='col-6 col-md-3'>
                            <a href='musteriler.php' class='btn btn-outline-success w-100'>
                                <i class='fas fa-user-plus d-block mb-2' style='font-size: 1.5rem;'></i>
                                Yeni Müşteri
                            </a>
                        </div>
                        <div class='col-6 col-md-3'>
                            <a href='urunler.php' class='btn btn-outline-info w-100'>
                                <i class='fas fa-wine-glass d-block mb-2' style='font-size: 1.5rem;'></i>
                                Ürün Ekle
                            </a>
                        </div>
                        <div class='col-6 col-md-3'>
                            <a href='fiyat-listesi.php' class='btn btn-outline-warning w-100'>
                                <i class='fas fa-tags d-block mb-2' style='font-size: 1.5rem;'></i>
                                Fiyat Güncelle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>

<script>
    // Add interactive enhancements
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to dashboard cards
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 20px 45px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.08)';
            });
        });

        // Add smooth scrolling for any anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>

</body>
</html>