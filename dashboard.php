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
<div class='container py-4'>
    <h1 class='mb-4'>Panel</h1>
    <div class='row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3'>
        <div class='col'>
            <div class='card text-center'>
                <div class='card-body'>
                    <h5 class='card-title'>Toplam Sipariş</h5>
                    <p class='card-text fs-4'><?php echo htmlspecialchars((string)$totalOrders); ?></p>
                </div>
            </div>
        </div>
        <div class='col'>
            <div class='card text-center'>
                <div class='card-body'>
                    <h5 class='card-title'>Bekleyen</h5>
                    <p class='card-text fs-4'><?php echo htmlspecialchars((string)$pendingCount); ?></p>
                </div>
            </div>
        </div>
        <div class='col'>
            <div class='card text-center'>
                <div class='card-body'>
                    <h5 class='card-title'>Teslim Edilen</h5>
                    <p class='card-text fs-4'><?php echo htmlspecialchars((string)$deliveredCount); ?></p>
                </div>
            </div>
        </div>
        <div class='col'>
            <div class='card text-center'>
                <div class='card-body'>
                    <h5 class='card-title'>Geciken</h5>
                    <p class='card-text fs-4'><?php echo htmlspecialchars((string)$delayedCount); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h2 class='mt-5'>Son Siparişler</h2>
    <div class='table-responsive'>
    <table class='table table-striped table-sm'>
        <thead>
            <tr>
                <th>#</th>
                <th>Müşteri</th>
                <th>Şirket</th>
                <th>Ölçü</th>
                <th>Adet</th>
                <th>Durum</th>
                <th>Planlanan Teslim</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orders): ?>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)$o['id']); ?></td>
                    <td><?php echo htmlspecialchars($o['customer']); ?></td>
                    <td><?php echo htmlspecialchars($o['company']); ?></td>
                    <td><?php echo htmlspecialchars($o['size']); ?></td>
                    <td><?php echo htmlspecialchars((string)$o['qty']); ?></td>
                    <td><?php echo htmlspecialchars($o['status']); ?></td>
                    <td><?php echo htmlspecialchars($o['delivery']); ?></td>
                    <td>
                        <a href='order_view.php?id=<?php echo urlencode((string)$o['id']); ?>' class='btn btn-sm btn-outline-primary me-1'><i class='fa fa-eye'></i> Görüntüle</a>
                        <a href='order_edit.php?id=<?php echo urlencode((string)$o['id']); ?>' class='btn btn-sm btn-outline-secondary'><i class='fa fa-edit'></i> Düzenle</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan='8' class='text-center'>Kayıt bulunamadı</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>