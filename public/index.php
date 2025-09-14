<?php
declare(strict_types=1);

// Basic configuration
$minDevices = 1;
$maxDevices = 20;
$allowedTypes = ['iphone', 'android'];

$modelsByType = [
	'iphone' => ['iPhone 13', 'iPhone 14', 'iPhone 15 Pro', 'iPhone SE (3rd gen)'],
	'android' => ['Samsung Galaxy S23', 'Google Pixel 8', 'Xiaomi 13', 'OnePlus 11'],
];

$platformsByType = [
	'iphone' => ['iOS 16', 'iOS 17', 'iOS 18'],
	'android' => ['Android 12', 'Android 13', 'Android 14'],
];

$allowedApps = ['Facebook','Messenger','Zalo','TikTok','YouTube','Chrome','Gmail','WhatsApp','Telegram','Viber'];
$appAbbr = [
	'Facebook' => 'FB',
	'Messenger' => 'MS',
	'Zalo' => 'ZA',
	'TikTok' => 'TT',
	'YouTube' => 'YT',
	'Chrome' => 'CH',
	'Gmail' => 'GM',
	'WhatsApp' => 'WA',
	'Telegram' => 'TG',
	'Viber' => 'VI',
];

$allowedFeatures = ['Camera','GPS','NFC','5G','Dual SIM','Bluetooth','Wi‑Fi','Hotspot'];

// Read form input
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$quantity = 1;
$deviceType = 'iphone';
$selectedModel = '';
$selectedPlatform = '';
$selectedApps = [];
$selectedFeatures = [];
$errors = [];

if ($method === 'POST') {
	$quantityRaw = $_POST['quantity'] ?? '';
	$deviceTypeRaw = $_POST['device_type'] ?? '';
	$modelRaw = $_POST['model'] ?? '';
	$platformRaw = $_POST['platform'] ?? '';
	$appsRaw = $_POST['apps'] ?? [];
	$featuresRaw = $_POST['features'] ?? [];

	// Type
	if ($deviceTypeRaw !== '' && in_array($deviceTypeRaw, $allowedTypes, true)) {
		$deviceType = $deviceTypeRaw;
	} else {
		$errors[] = 'Vui lòng chọn loại điện thoại hợp lệ.';
	}

	// Quantity
	if ($quantityRaw === '' || !is_numeric($quantityRaw)) {
		$errors[] = 'Số lượng phải là một số.';
	} else {
		$quantity = (int) $quantityRaw;
		if ($quantity < $minDevices || $quantity > $maxDevices) {
			$errors[] = 'Số lượng phải từ ' . $minDevices . ' đến ' . $maxDevices . '.';
		}
	}

	// Model
	if ($modelRaw !== '') {
		$selectedModel = $modelRaw;
		if (!in_array($selectedModel, $modelsByType[$deviceType] ?? [], true)) {
			$errors[] = 'Mẫu máy không hợp lệ cho loại đã chọn.';
		}
	} else {
		$errors[] = 'Vui lòng chọn mẫu máy.';
	}

	// Platform
	if ($platformRaw !== '') {
		$selectedPlatform = $platformRaw;
		if (!in_array($selectedPlatform, $platformsByType[$deviceType] ?? [], true)) {
			$errors[] = 'Nền tảng không hợp lệ cho loại đã chọn.';
		}
	} else {
		$errors[] = 'Vui lòng chọn nền tảng hệ điều hành.';
	}

	// Apps (software)
	if (is_array($appsRaw)) {
		foreach ($appsRaw as $app) {
			if (in_array($app, $allowedApps, true)) {
				$selectedApps[] = $app;
			}
		}
		// Optional: limit number of icons displayed
		$selectedApps = array_values(array_unique($selectedApps));
		if (count($selectedApps) > 12) {
			$selectedApps = array_slice($selectedApps, 0, 12);
		}
	}

	// Features
	if (is_array($featuresRaw)) {
		foreach ($featuresRaw as $feat) {
			if (in_array($feat, $allowedFeatures, true)) {
				$selectedFeatures[] = $feat;
			}
		}
		$selectedFeatures = array_values(array_unique($selectedFeatures));
	}
}

// Helpers
function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function app_label(string $name, array $abbr): string {
	return $abbr[$name] ?? strtoupper(substr($name, 0, 2));
}

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
					<label for="model">Mẫu máy</label>
					<select id="model" name="model" required>
						<?php foreach (($modelsByType[$deviceType] ?? []) as $m): ?>
							<option value="<?= h($m) ?>" <?= $selectedModel === $m ? 'selected' : '' ?>><?= h($m) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form-row">
					<label for="platform">Nền tảng (OS)</label>
					<select id="platform" name="platform" required>
						<?php foreach (($platformsByType[$deviceType] ?? []) as $p): ?>
							<option value="<?= h($p) ?>" <?= $selectedPlatform === $p ? 'selected' : '' ?>><?= h($p) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form-row">
					<label for="apps">Phần mềm (giữ Ctrl/Cmd để chọn nhiều)</label>
					<select id="apps" name="apps[]" multiple size="6">
						<?php foreach ($allowedApps as $app): ?>
							<option value="<?= h($app) ?>" <?= in_array($app, $selectedApps, true) ? 'selected' : '' ?>><?= h($app) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form-row">
					<label>Tính năng</label>
					<div class="feature-list">
						<?php foreach ($allowedFeatures as $feat): ?>
							<label class="cb">
								<input type="checkbox" name="features[]" value="<?= h($feat) ?>" <?= in_array($feat, $selectedFeatures, true) ? 'checked' : '' ?>>
								<span><?= h($feat) ?></span>
							</label>
						<?php endforeach; ?>
					</div>
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
							<div class="badges">
								<span class="badge model" title="Mẫu máy"><?= h($selectedModel ?: ucfirst($deviceType)) ?></span>
								<span class="badge os" title="Hệ điều hành"><?= h($selectedPlatform ?: ($deviceType === 'iphone' ? 'iOS' : 'Android')) ?></span>
							</div>
							<div class="device <?= $deviceType ?>">
								<div class="screen">
									<div class="status-bar">
										<span class="signal"></span>
										<span class="time"><?= date('H:i') ?></span>
										<span class="battery"></span>
									</div>
									<div class="app-grid">
										<?php if (!empty($selectedApps)): ?>
											<?php foreach ($selectedApps as $app): ?>
												<span class="app" title="<?= h($app) ?>" data-label="<?= h(app_label($app, $appAbbr)) ?>"></span>
											<?php endforeach; ?>
											<?php for ($a = count($selectedApps); $a < 9; $a++): ?>
												<span class="app" data-label=""></span>
											<?php endfor; ?>
										<?php else: ?>
											<?php for ($a = 0; $a < 9; $a++): ?>
												<span class="app" data-label=""></span>
											<?php endfor; ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="label">#<?= $i ?> <?= h(ucfirst($deviceType)) ?></div>
							<?php if (!empty($selectedFeatures)): ?>
								<div class="badges features">
									<?php foreach ($selectedFeatures as $feat): ?>
										<span class="badge feature"><?= h($feat) ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endfor; ?>
				</div>
			<?php else: ?>
				<p class="muted">Chọn loại, mẫu máy, nền tảng, phần mềm và tính năng. Sau đó bấm "Tạo điện thoại" để xem dàn thiết bị.</p>
			<?php endif; ?>
		</section>
	</main>

	<footer class="site-footer">
		<p>Made with PHP • <?= date('Y') ?></p>
	</footer>

	<script>
		const modelsByType = <?= json_encode($modelsByType, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
		const platformsByType = <?= json_encode($platformsByType, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
		const deviceTypeEl = document.getElementById('device_type');
		const modelEl = document.getElementById('model');
		const platformEl = document.getElementById('platform');

		function refill(select, options) {
			const current = select.value;
			select.innerHTML = '';
			options.forEach(v => {
				const opt = document.createElement('option');
				opt.value = v;
				opt.textContent = v;
				select.appendChild(opt);
			});
			// Try keep previous value if still valid
			const values = options.map(String);
			if (values.includes(current)) {
				select.value = current;
			}
		}

		deviceTypeEl.addEventListener('change', () => {
			const type = deviceTypeEl.value;
			refill(modelEl, modelsByType[type] || []);
			refill(platformEl, platformsByType[type] || []);
		});
	</script>
</body>
</html>