# Cloud Phone (PHP)

Ứng dụng PHP cho phép chọn loại thiết bị (iPhone/Android), số lượng 1–20, mẫu máy, nền tảng (OS), phần mềm theo danh mục, tính năng; hỗ trợ mô phỏng UI hoặc nhúng iframe (emulator/cloud OS thật).

## Chạy local

Yêu cầu PHP 8+.

```bash
php -S 0.0.0.0:8000 -t public
```

Mở: http://localhost:8000

## Danh mục phần mềm (App Catalog)

- Hệ thống: Điện thoại, Tin nhắn, Danh bạ, Cài đặt, Trình duyệt, Camera
- Giải trí: Thư viện ảnh/video, Nhạc, Video Player, Genesis Plus GX
- Tiện ích: Ghi chú, Máy tính, Đồng hồ, Lịch, Thời tiết, File Manager
- Kết nối: Email, Chat App, Maps
- Mở rộng: App Store, Cloud Drive, AI Assistant, Ví điện tử, Cloud Phone Web

Form cho phép multi-select và có nút chọn nhanh theo danh mục.

## Chế độ hiển thị

- Mô phỏng (mock): lưới icon với nhãn viết tắt.
- Iframe (Emulator/Cloud OS thật): điền "Iframe URL template" (hỗ trợ `{i}` = chỉ số máy).
  - Presets (ví dụ): Genesis Plus GX, Cloud Phone Web. Chọn preset sẽ tự điền URL mẫu:
    - GX: `https://gx.example/room/{i}`
    - Cloud Phone Web: `https://cloudphone.example/device/{i}?token=YOUR_TOKEN`
  - Lưu ý: Nhà cung cấp phải cho phép nhúng (CSP/X-Frame-Options). Một số quyền (camera/mic/clipboard/fullscreen) đã cấp trong thuộc tính `allow` của iframe.

## Cấu trúc

- `public/index.php` – Form, validation, danh mục app, preset iframe, render
- `public/styles.css` – Giao diện, badge, danh mục và style iframe

## Ghi chú

- Validate số lượng 1–20
- Mẫu máy và OS lọc theo loại
- Tối đa 12 app hiển thị
- Nếu URL iframe không có `{i}`, mọi thiết bị dùng cùng URL