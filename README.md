# Cloud Phone (PHP)

Ứng dụng PHP đơn giản cho phép chọn loại thiết bị (iPhone/Android), số lượng 1–20, mẫu máy, nền tảng (OS), phần mềm và tính năng; hỗ trợ chế độ mô phỏng UI hoặc nhúng iframe (emulator/cloud OS thật).

## Chạy local

Yêu cầu PHP 8+.

```bash
php -S 0.0.0.0:8000 -t public
```

Mở trình duyệt: http://localhost:8000

## Chế độ hiển thị

- Mô phỏng (mock): hiển thị lưới icon giả lập.
- Iframe (Emulator/Cloud OS thật): nhúng URL từ nhà cung cấp emulator/cloud phone.
  - Nhập "Iframe URL template". Có thể dùng `{i}` để thay thế chỉ số thiết bị (1..N).
  - Ví dụ: `https://provider.example/sessions/{i}?token=YOUR_TOKEN`
  - Trình duyệt và nhà cung cấp phải cho phép nhúng iframe (X-Frame-Options/Content-Security-Policy phù hợp).
  - Một số tính năng (camera/mic/clipboard/fullscreen) cần quyền allow trong iframe; đã bật sẵn qua `allow` và `sandbox`.

## Cấu trúc

- `public/index.php` – Trang chính với form, validation và render thiết bị/iframe
- `public/styles.css` – Giao diện, phù hiệu (badge), và style iframe trong màn hình thiết bị

## Ghi chú

- Validate số lượng trong khoảng 1–20
- Loại hợp lệ: `iphone`, `android`
- Mẫu máy và OS tự động lọc theo loại
- App hiển thị tối đa 12 (mặc định 9 ô ở màn đầu)
- Iframe yêu cầu URL bắt đầu bằng http/https; nếu thiếu `{i}` thì mọi thiết bị dùng cùng URL