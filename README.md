# Cloud Phone (PHP)

Ứng dụng PHP đơn giản cho phép chọn loại thiết bị (iPhone/Android) và số lượng từ 1 đến 20 để hiển thị một lưới điện thoại mô phỏng.

## Chạy local

Yêu cầu PHP 8+.

```bash
php -S 0.0.0.0:8000 -t public
```

Sau đó mở trình duyệt: http://localhost:8000

## Cấu trúc

- `public/index.php` – Trang chính với form và render thiết bị
- `public/styles.css` – Giao diện và style thiết bị

## Ghi chú

- Validate số lượng trong khoảng 1–20
- Loại thiết bị hợp lệ: `iphone`, `android`