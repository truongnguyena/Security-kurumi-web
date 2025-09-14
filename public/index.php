<?php
declare(strict_types=1);

// Basic configuration
$minDevices = 1;
$maxDevices = 20;
$allowedTypes = ['iphone', 'android'];

// Read form input
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$quantity = 1;
$deviceType = 'iphone';
$errors = [];

if ($method === 'POST') {
	$quantityRaw = $_POST['quantity'] ?? '';
	$deviceTypeRaw = $_POST['device_type'] ?? '';

	if ($deviceTypeRaw !== '' && in_array($deviceTypeRaw, $allowedTypes, true)) {
		$deviceType = $deviceTypeRaw;
	} else {
		$errors[] = 'Vui lòng chọn loại điện thoại hợp lệ.';
	}

	if ($quantityRaw === '' || !is_numeric($quantityRaw)) {
		$errors[] = 'Số lượng phải là một số.';
	} else {
		$quantity = (int) $quantityRaw;
		if ($quantity < $minDevices || $quantity > $maxDevices) {
			$errors[] = 'Số lượng phải từ ' . $minDevices . ' đến ' . $maxDevices . '.';
		}
	}
}

// Helper
function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

?><!doctype html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cloud Phone (iPhone / Android)</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="./styles.css">
</head>
<body>
	<header class="site-header">
		<h1>Cloud Phone</h1>
		<p class="subtitle">Tạo dàn điện thoại iPhone hoặc Android (tối thiểu 1, tối đa 20)</p>
	</header>

	<main class="container">
		<section class="panel">
			<h2 class="panel-title">Cấu hình</h2>
			<?php if (!empty($errors)): ?>
				<div class="alert alert-error">
					<ul>
						<?php foreach ($errors as $err): ?>
							<li><?= h($err) ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form method="post" class="config-form" novalidate>
				<div class="form-row">
					<label for="device_type">Loại điện thoại</label>
					<select id="device_type" name="device_type" required>
						<option value="iphone" <?= $deviceType === 'iphone' ? 'selected' : '' ?>>iPhone</option>
						<option value="android" <?= $deviceType === 'android' ? 'selected' : '' ?>>Android</option>
					</select>
				</div>

				<div class="form-row">
					<label for="quantity">Số lượng</label>
					<input type="number" id="quantity" name="quantity" min="<?= $minDevices ?>" max="<?= $maxDevices ?>" step="1" value="<?= (int) $quantity ?>" required>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn primary">Tạo điện thoại</button>
				</div>
			</form>
		</section>

		<section class="panel">
			<h2 class="panel-title">Kết quả</h2>
			<?php if ($method === 'POST' && empty($errors)): ?>
				<p class="result-meta">Đang hiển thị <?= (int) $quantity ?> thiết bị: <strong><?= h(ucfirst($deviceType)) ?></strong></p>
				<div class="phone-grid">
					<?php for ($i = 1; $i <= $quantity; $i++): ?>
						<div class="phone-card">
							<div class="device <?= $deviceType ?>">
								<div class="screen">
									<div class="status-bar">
										<span class="signal"></span>
										<span class="time"><?= date('H:i') ?></span>
										<span class="battery"></span>
									</div>
									<div class="app-grid">
										<?php for ($a = 0; $a < 12; $a++): ?>
											<span class="app"></span>
										<?php endfor; ?>
									</div>
								</div>
							</div>
							<div class="label">#<?= $i ?> <?= h(ucfirst($deviceType)) ?></div>
						</div>
					<?php endfor; ?>
				</div>
			<?php else: ?>
				<p class="muted">Chọn loại và số lượng, sau đó bấm "Tạo điện thoại" để xem dàn thiết bị.</p>
			<?php endif; ?>
		</section>
	</main>

	<footer class="site-footer">
		<p>Made with PHP • <?= date('Y') ?></p>
	</footer>
</body>
</html>