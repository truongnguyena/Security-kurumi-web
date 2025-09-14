# Cloud Phone (PHP)

Ứng dụng PHP cho phép chọn loại thiết bị (iPhone/Android), số lượng 1–20, mẫu máy, nền tảng (OS), phần mềm theo danh mục, tính năng; hỗ trợ mô phỏng UI hoặc nhúng iframe (emulator/cloud OS thật).

## Chạy local

Yêu cầu PHP 8+.

```bash
php -S 0.0.0.0:8000 -t public
```

Mở: http://localhost:8000

## Tính năng chính

- Chọn loại máy, mẫu, OS; chọn app theo danh mục (có Genesis Plus GX, Cloud Phone Web)
- Chế độ hiển thị: Mô phỏng UI hoặc Iframe (Emulator/Cloud OS)
- Nút chọn nhanh app theo danh mục; giới hạn tối đa hiển thị 12 app
- Tính năng: Camera, GPS, NFC, 5G, Dual SIM, Bluetooth, Wi‑Fi, Hotspot

## Tùy chọn nâng cao

- Tiền tố tên máy: đặt tiền tố cho nhãn thiết bị (ví dụ: "Máy A-")
- Số cột lưới: chọn số cột cố định (1–6) hoặc tự động
- Cấu hình (JSON):
  - Sao chép cấu hình: copy JSON vào clipboard
  - Tải xuống JSON: xuất file `cloud-phone-config.json`
  - Tải cấu hình: dán JSON vào ô và bấm Tải để nạp vào form (sau đó bấm "Tạo điện thoại")
- Iframe controls: nút "Tải lại iframes" để refresh tất cả phiên emulator/cloud

## Iframe presets (ví dụ)

- Genesis Plus GX: tự điền `https://gx.example/room/{i}`
- Cloud Phone Web: tự điền `https://cloudphone.example/device/{i}?token=YOUR_TOKEN`

Lưu ý: nhà cung cấp phải cho phép nhúng (CSP/X-Frame-Options). Có thể cần token/đăng nhập.

## Cấu trúc

- `public/index.php` – Trang chính: form, validation, danh mục app, preset, nâng cao, render
- `public/styles.css` – Giao diện, badge, danh mục, iframe, textarea JSON

## Ghi chú

- Validate số lượng 1–20
- Mẫu máy và OS lọc theo loại
- Tối đa 12 app hiển thị
- Nếu URL iframe không có `{i}`, mọi thiết bị dùng cùng URL