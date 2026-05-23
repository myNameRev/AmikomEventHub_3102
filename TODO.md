# TODO - Checkout total bayar ikut berubah

- [x] Update `resources/views/checkout.blade.php` agar tampilan total bayar dan jumlah baris ikut berubah saat `quantity` diubah

- [x] Pastikan nilai yang dipakai untuk update tampilan berasal dari `event->price` (format rupiah konsisten)

- [ ] Test manual: ubah quantity, lihat total ikut berubah, lalu proses pembayaran untuk memastikan tidak ada regresi
- [x] Perbaiki fitur delete partner (implement destroy + hapus bug pada update yang salah tempat)

