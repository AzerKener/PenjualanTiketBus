# Tasks

- `[x]` **1. Isolasi Akses Sales Berdasarkan Pool**
  - `[x]` Update `Sales\TransaksiController` untuk filter pool_id
  - `[x]` Update `Sales\DashboardController` untuk filter pool_id
  - `[x]` Update fungsi konfirmasi agar divalidasi ke pool_id

- `[x]` **2. Otomatisasi Jadwal Berangkat**
  - `[x]` Buat command `OtomatisBerangkatJadwal`
  - `[x]` Daftarkan command di Task Scheduler (setiap menit)
  - `[x]` Tambahkan notifikasi ke Supir & Kenek saat berangkat otomatis
  - `[x]` Hapus tombol manual berangkat (jika ada) di Supir Dashboard

- `[x]` **3. Sistem Bagasi Standar (Check-in)**
  - `[x]` Tambahkan form input berat aktual bagasi di tampilan Sales konfirmasi
  - `[x]` Buat fungsi kalkulasi biaya bagasi tambahan dan simpan

- `[x]` **4. Pembayaran Cash & Batas Waktu 8 Jam**
  - `[x]` Ubah countdown timer di view `user.sukses` menjadi 8 jam
  - `[x]` Buat command `CancelUnpaidCashOrders` untuk membatalkan pesanan > 8 jam
  - `[x]` Daftarkan command di Task Scheduler

- `[x]` **5. Integrasi Notifikasi Terpadu**
  - `[x]` Kirim notifikasi ke Sales saat ada pesanan cash baru di poolnya
  - `[x]` Kirim notifikasi ke User saat pesanan dikonfirmasi lunas oleh Sales
