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

// App catalog by category (Vietnamese)
$appCatalog = [
	'Hệ thống' => ['Điện thoại','Tin nhắn','Danh bạ','Cài đặt','Trình duyệt','Camera'],
	'Giải trí' => ['Thư viện ảnh/video','Nhạc','Video Player','Genesis Plus GX'],
	'Tiện ích' => ['Ghi chú','Máy tính','Đồng hồ','Lịch','Thời tiết','File Manager'],
	'Kết nối' => ['Email','Chat App','Maps'],
	'Mở rộng' => ['App Store','Cloud Drive','AI Assistant','Ví điện tử','Cloud Phone Web'],
];

// Flatten all apps
$allApps = [];
foreach ($appCatalog as $categoryName => $apps) {
	foreach ($apps as $appName) {
		$allApps[] = $appName;
	}
}

// Abbreviations for app icons
$appAbbr = [
	'Điện thoại' => 'TEL',
	'Tin nhắn' => 'SMS',
	'Danh bạ' => 'CN',
	'Cài đặt' => 'SET',
	'Trình duyệt' => 'WEB',
	'Camera' => 'CAM',
	'Thư viện ảnh/video' => 'GL',
	'Nhạc' => 'MU',
	'Video Player' => 'VP',
	'Ghi chú' => 'NT',
	'Máy tính' => 'CAL',
	'Đồng hồ' => 'CLK',
	'Lịch' => 'CALR',
	'Thời tiết' => 'WX',
	'File Manager' => 'FM',
	'Email' => 'EM',
	'Chat App' => 'CHAT',
	'Maps' => 'MAP',
	'App Store' => 'APP',
	'Cloud Drive' => 'CD',
	'AI Assistant' => 'AI',
	'Ví điện tử' => 'WLT',
	'Genesis Plus GX' => 'GX',
	'Cloud Phone Web' => 'CPW',
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

$displayModes = ['mock', 'iframe'];

// Read form input
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$quantity = 1;
$deviceType = 'iphone';
$selectedModel = '';
$selectedPlatform = '';
$selectedApps = [];
$selectedFeatures = [];
$displayMode = 'mock';
$iframeUrlTemplate = '';
$iframePreset = 'none';
$namePrefix = '';
$gridColumns = 'auto'; // 'auto' or 1..6
$errors = [];
$warnings = [];

if ($method === 'POST') {
	$quantityRaw = $_POST['quantity'] ?? '';
	$deviceTypeRaw = $_POST['device_type'] ?? '';
	$modelRaw = $_POST['model'] ?? '';
	$platformRaw = $_POST['platform'] ?? '';
	$appsRaw = $_POST['apps'] ?? [];
	$featuresRaw = $_POST['features'] ?? [];
	$displayModeRaw = $_POST['display_mode'] ?? 'mock';
	$iframeUrlTemplateRaw = trim((string)($_POST['iframe_url_template'] ?? ''));
	$iframePresetRaw = $_POST['iframe_preset'] ?? 'none';
	$namePrefixRaw = trim((string)($_POST['name_prefix'] ?? ''));
	$gridColumnsRaw = $_POST['grid_columns'] ?? 'auto';

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

	// Display mode
	if (in_array($displayModeRaw, $displayModes, true)) {
		$displayMode = $displayModeRaw;
	} else {
		$errors[] = 'Chế độ hiển thị không hợp lệ.';
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
			if (in_array($app, $allApps, true)) {
				$selectedApps[] = $app;
			}
		}
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

	// Iframe preset
	$iframePreset = in_array($iframePresetRaw, ['none','genesis_plus_gx','cloud_phone_web'], true) ? $iframePresetRaw : 'none';

	// Iframe URL validation when displayMode is iframe
	if ($displayMode === 'iframe') {
		$iframeUrlTemplate = $iframeUrlTemplateRaw;
		if ($iframeUrlTemplate === '') {
			$errors[] = 'Vui lòng nhập URL template cho iframe.';
		} else {
			if (!preg_match('/^https?:\/\//i', $iframeUrlTemplate)) {
				$errors[] = 'URL iframe phải bắt đầu bằng http:// hoặc https://';
			}
			if (strpos($iframeUrlTemplate, '{i}') === false) {
				$warnings[] = 'URL không chứa {i}; tất cả thiết bị sẽ dùng cùng một URL.';
			}
		}
	}

	// Advanced: name prefix
	if ($namePrefixRaw !== '') {
		if (mb_strlen($namePrefixRaw) > 32) {
			$namePrefix = mb_substr($namePrefixRaw, 0, 32);
			$warnings[] = 'Tiền tố tên máy đã được cắt bớt đến 32 ký tự.';
		} else {
			$namePrefix = $namePrefixRaw;
		}
	}

	// Advanced: grid columns
	if ($gridColumnsRaw === 'auto') {
		$gridColumns = 'auto';
	} elseif (is_numeric($gridColumnsRaw)) {
		$gc = (int)$gridColumnsRaw;
		$gridColumns = ($gc >= 1 && $gc <= 6) ? (string)$gc : 'auto';
	} else {
		$gridColumns = 'auto';
	}
}

// Helpers
function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function app_label(string $name, array $abbr): string {
	return $abbr[$name] ?? strtoupper(substr($name, 0, 2));
}
function build_iframe_src(string $template, int $index): string {
	return str_replace('{i}', (string)$index, $template);
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
			<?php if (!empty($warnings)): ?>
				<div class="alert" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.18);">
					<ul>
						<?php foreach ($warnings as $w): ?>
							<li><?= h($w) ?></li>
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
					<label for="display_mode">Chế độ hiển thị</label>
					<select id="display_mode" name="display_mode" required>
						<option value="mock" <?= $displayMode === 'mock' ? 'selected' : '' ?>>Mô phỏng (giả lập UI)</option>
						<option value="iframe" <?= $displayMode === 'iframe' ? 'selected' : '' ?>>Iframe (Emulator/Cloud OS thật)</option>
					</select>
				</div>

				<div class="form-row iframe-only <?= $displayMode === 'iframe' ? '' : 'hidden' ?>">
					<label for="iframe_preset">Nhà cung cấp (preset)</label>
					<select id="iframe_preset" name="iframe_preset">
						<option value="none" <?= $iframePreset === 'none' ? 'selected' : '' ?>>Tự nhập</option>
						<option value="genesis_plus_gx" <?= $iframePreset === 'genesis_plus_gx' ? 'selected' : '' ?>>Genesis Plus GX (ví dụ)</option>
						<option value="cloud_phone_web" <?= $iframePreset === 'cloud_phone_web' ? 'selected' : '' ?>>Cloud Phone Web (ví dụ)</option>
					</select>
				</div>

				<div class="form-row iframe-only <?= $displayMode === 'iframe' ? '' : 'hidden' ?>">
					<label for="iframe_url_template">Iframe URL template (dùng {i} cho chỉ số máy)</label>
					<input type="text" id="iframe_url_template" name="iframe_url_template" placeholder="https://provider.example/sessions/{i}?token=..." value="<?= h($iframeUrlTemplate) ?>">
					<small class="help">Ví dụ GX: https://gx.example/room/{i} • Cloud Phone Web: https://cloudphone.example/device/{i}?token=...</small>
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
					<select id="apps" name="apps[]" multiple size="10">
						<?php foreach ($appCatalog as $cat => $apps): ?>
							<optgroup label="<?= h($cat) ?>">
								<?php foreach ($apps as $app): ?>
									<option value="<?= h($app) ?>" data-cat="<?= h($cat) ?>" <?= in_array($app, $selectedApps, true) ? 'selected' : '' ?>><?= h($app) ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php endforeach; ?>
					</select>
					<div class="category-actions">
						<div class="row">
							<button type="button" class="btn small" data-action="select-all">Chọn tất cả</button>
							<button type="button" class="btn small" data-action="clear-all">Bỏ chọn</button>
						</div>
						<div class="row wrap">
							<?php foreach (array_keys($appCatalog) as $cat): ?>
								<button type="button" class="btn pill" data-cat="<?= h($cat) ?>"><?= h($cat) ?></button>
							<?php endforeach; ?>
						</div>
					</div>
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

				<!-- Advanced options -->
				<div class="form-row">
					<label for="name_prefix">Tiền tố tên máy (ví dụ: Máy A-)</label>
					<input type="text" id="name_prefix" name="name_prefix" maxlength="32" value="<?= h($namePrefix) ?>">
				</div>

				<div class="form-row">
					<label for="grid_columns">Số cột lưới</label>
					<select id="grid_columns" name="grid_columns">
						<option value="auto" <?= $gridColumns === 'auto' ? 'selected' : '' ?>>Tự động</option>
						<?php for ($c = 1; $c <= 6; $c++): ?>
							<option value="<?= $c ?>" <?= (string)$gridColumns === (string)$c ? 'selected' : '' ?>><?= $c ?></option>
						<?php endfor; ?>
					</select>
				</div>

				<!-- Config JSON -->
				<div class="form-row">
					<label for="config_json">Cấu hình (JSON) – xuất/nhập nhanh</label>
					<textarea id="config_json" class="json" placeholder="Dán JSON ở đây để tải cấu hình hoặc bấm Sao chép/Tải xuống để xuất"></textarea>
					<div class="row">
						<button type="button" class="btn small" id="copy_config">Sao chép cấu hình</button>
						<button type="button" class="btn small" id="download_config">Tải xuống JSON</button>
						<button type="button" class="btn small" id="load_config">Tải cấu hình</button>
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn primary">Tạo điện thoại</button>
				</div>
			</form>
		</section>

		<section class="panel">
			<h2 class="panel-title">Kết quả</h2>
			<?php if ($method === 'POST' && empty($errors)): ?>
				<p class="result-meta">Đang hiển thị <?= (int) $quantity ?> thiết bị: <strong><?= h(ucfirst($deviceType)) ?></strong> • Chế độ: <strong><?= h($displayMode) ?></strong></p>
				<?php if ($displayMode === 'iframe'): ?>
					<div class="form-actions" style="margin-bottom:12px; justify-content:center;">
						<button type="button" class="btn small" id="reload_iframes">Tải lại iframes</button>
					</div>
				<?php endif; ?>
				<div class="phone-grid"<?= ($gridColumns !== 'auto') ? ' style="grid-template-columns: repeat(' . (int)$gridColumns . ', minmax(180px, 1fr));"' : '' ?>>
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
									<?php if ($displayMode === 'iframe' && $iframeUrlTemplate !== ''): ?>
										<div class="live-iframe">
											<iframe src="<?= h(build_iframe_src($iframeUrlTemplate, $i)) ?>" loading="lazy" allow="autoplay; clipboard-read; clipboard-write; fullscreen; geolocation; microphone; camera" sandbox="allow-scripts allow-same-origin allow-forms allow-popups"></iframe>
										</div>
									<?php else: ?>
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
									<?php endif; ?>
								</div>
							</div>
							<div class="label"><?php if ($namePrefix !== ''): ?><?= h($namePrefix) ?><?= $i ?><?php else: ?>#<?= $i ?> <?= h(ucfirst($deviceType)) ?><?php endif; ?></div>
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
				<p class="muted">Chọn loại, mẫu máy, nền tảng, phần mềm theo danh mục và tính năng. Có thể chọn chế độ Iframe để nhúng Emulator/Cloud OS thật. Sau đó bấm "Tạo điện thoại".</p>
			<?php endif; ?>
		</section>

		<section class="panel">
			<h2 class="panel-title">Hướng dẫn sử dụng (Tiếng Việt)</h2>
			<div class="help-steps">
				<ol>
					<li>Chọn <strong>Loại điện thoại</strong> (iPhone/Android). Mẫu máy và nền tảng sẽ lọc theo loại.</li>
					<li>Chọn <strong>Chế độ hiển thị</strong>:
						<ul>
							<li><strong>Mô phỏng</strong>: hiện lưới icon ứng dụng.</li>
							<li><strong>Iframe</strong>: dán <em>Iframe URL template</em> từ nhà cung cấp (có thể dùng {i}).</li>
						</ul>
					</li>
					<li>Chọn <strong>Phần mềm</strong> theo danh mục; có thể dùng nút chọn nhanh.</li>
					<li>Chọn <strong>Tính năng</strong> cần hiển thị.</li>
					<li>Tùy chọn <strong>Tiền tố tên máy</strong> và <strong>Số cột lưới</strong> trong mục nâng cao.</li>
					<li>Dùng mục <strong>Cấu hình (JSON)</strong> để lưu/tải nhanh cấu hình.</li>
					<li>Nhập <strong>Số lượng</strong> (1–20) và bấm <strong>Tạo điện thoại</strong>.</li>
				</ol>
				<p class="muted">Lưu ý: Iframe phụ thuộc cấu hình CSP/X-Frame-Options của nhà cung cấp, có thể không nhúng được nếu bị chặn.</p>
			</div>
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
		const displayModeEl = document.getElementById('display_mode');
		const iframeRow = document.querySelectorAll('.iframe-only');
		const iframePresetEl = document.getElementById('iframe_preset');
		const iframeUrlEl = document.getElementById('iframe_url_template');
		const appsSelect = document.getElementById('apps');
		const namePrefixEl = document.getElementById('name_prefix');
		const gridColumnsEl = document.getElementById('grid_columns');
		const configJsonEl = document.getElementById('config_json');
		const btnCopyConfig = document.getElementById('copy_config');
		const btnDownloadConfig = document.getElementById('download_config');
		const btnLoadConfig = document.getElementById('load_config');
		const btnReloadIframes = document.getElementById('reload_iframes');
		const presetTemplates = {
			'genesis_plus_gx': 'https://gx.example/room/{i}',
			'cloud_phone_web': 'https://cloudphone.example/device/{i}?token=YOUR_TOKEN',
		};

		function refill(select, options) {
			const current = select.value;
			select.innerHTML = '';
			options.forEach(v => {
				const opt = document.createElement('option');
				opt.value = v;
				opt.textContent = v;
				select.appendChild(opt);
			});
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

		displayModeEl.addEventListener('change', () => {
			const show = displayModeEl.value === 'iframe';
			iframeRow.forEach(el => { el.classList.toggle('hidden', !show); });
		});

		iframePresetEl && iframePresetEl.addEventListener('change', () => {
			const v = iframePresetEl.value;
			if (presetTemplates[v]) {
				iframeUrlEl.value = presetTemplates[v];
			}
		});

		// Category quick selects
		document.querySelectorAll('.category-actions .btn.pill').forEach(btn => {
			btn.addEventListener('click', () => {
				const cat = btn.getAttribute('data-cat');
				[...appsSelect.options].forEach(o => {
					if (o.getAttribute('data-cat') === cat) {
						o.selected = true;
					}
				});
				appsSelect.dispatchEvent(new Event('change', {bubbles:true}));
			});
		});

		document.querySelector('.category-actions [data-action="select-all"]').addEventListener('click', () => {
			[...appsSelect.options].forEach(o => o.selected = true);
			appsSelect.dispatchEvent(new Event('change', {bubbles:true}));
		});
		document.querySelector('.category-actions [data-action="clear-all"]').addEventListener('click', () => {
			[...appsSelect.options].forEach(o => o.selected = false);
			appsSelect.dispatchEvent(new Event('change', {bubbles:true}));
		});

		function getConfigFromForm() {
			return {
				device_type: deviceTypeEl.value,
				model: modelEl.value,
				platform: platformEl.value,
				apps: [...appsSelect.options].filter(o => o.selected).map(o => o.value),
				features: [...document.querySelectorAll('input[name="features[]"]:checked')].map(i => i.value),
				quantity: Number(document.getElementById('quantity').value || '1'),
				display_mode: displayModeEl.value,
				iframe_preset: document.getElementById('iframe_preset').value,
				iframe_url_template: iframeUrlEl.value,
				name_prefix: namePrefixEl.value,
				grid_columns: gridColumnsEl.value,
			};
		}

		function applyConfigToForm(cfg) {
			if (!cfg || typeof cfg !== 'object') return;
			if (cfg.device_type) deviceTypeEl.value = cfg.device_type;
			deviceTypeEl.dispatchEvent(new Event('change'));
			if (cfg.model) modelEl.value = cfg.model;
			if (cfg.platform) platformEl.value = cfg.platform;
			if (Array.isArray(cfg.apps)) {
				[...appsSelect.options].forEach(o => { o.selected = cfg.apps.includes(o.value); });
			}
			if (Array.isArray(cfg.features)) {
				[...document.querySelectorAll('input[name="features[]"]')].forEach(i => {
					i.checked = cfg.features.includes(i.value);
				});
			}
			if (cfg.quantity) document.getElementById('quantity').value = String(cfg.quantity);
			if (cfg.display_mode) displayModeEl.value = cfg.display_mode;
			displayModeEl.dispatchEvent(new Event('change'));
			if (cfg.iframe_preset) document.getElementById('iframe_preset').value = cfg.iframe_preset;
			if (cfg.iframe_url_template) iframeUrlEl.value = cfg.iframe_url_template;
			if (typeof cfg.name_prefix === 'string') namePrefixEl.value = cfg.name_prefix;
			if (cfg.grid_columns) gridColumnsEl.value = cfg.grid_columns;
		}

		btnCopyConfig.addEventListener('click', async () => {
			const text = JSON.stringify(getConfigFromForm(), null, 2);
			configJsonEl.value = text;
			try {
				await navigator.clipboard.writeText(text);
				alert('Đã sao chép cấu hình vào clipboard.');
			} catch (e) {
				alert('Không thể sao chép tự động. Hãy copy thủ công trong ô JSON.');
			}
		});

		btnDownloadConfig.addEventListener('click', () => {
			const blob = new Blob([JSON.stringify(getConfigFromForm(), null, 2)], {type: 'application/json'});
			const a = document.createElement('a');
			a.href = URL.createObjectURL(blob);
			a.download = 'cloud-phone-config.json';
			a.click();
			URL.revokeObjectURL(a.href);
		});

		btnLoadConfig.addEventListener('click', () => {
			try {
				const cfg = JSON.parse(configJsonEl.value || '{}');
				applyConfigToForm(cfg);
				alert('Đã tải cấu hình vào form. Hãy bấm "Tạo điện thoại" để áp dụng.');
			} catch (e) {
				alert('JSON không hợp lệ.');
			}
		});

		btnReloadIframes && btnReloadIframes.addEventListener('click', () => {
			document.querySelectorAll('.live-iframe iframe').forEach((ifr) => {
				const src = ifr.getAttribute('src');
				ifr.setAttribute('src', src);
			});
		});
	</script>
</body>
</html>