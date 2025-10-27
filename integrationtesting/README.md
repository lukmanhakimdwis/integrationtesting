
# Laravel 12 Integration Testing Lab — PHPUnit + UI + CRUD
Overlay proyek mini untuk praktikum **Integration Testing** di Laravel 12 dengan **UI Bootstrap** dan **CRUD** penuh untuk Produk dan Orders.

Fitur
- CRUD Produk (index, create, edit, delete)
- CRUD Orders (index, create, edit* terbatas, delete)
- Endpoint `POST /orders` untuk pemesanan (form & JSON)
- Seeder produk sampel
- Uji integrasi PHPUnit: `Http::fake` & `Mockery` + form flow
- Logika stok: Bayar -> stok berkurang; Hapus order paid -> stok dipulihkan

Catatan:
- Edit Order dibatasi: hanya `pending` yang boleh ubah `qty` dan `product_id`.
  Perubahan ke `paid` akan memanggil gateway melalui `PaymentService` dan mengurangi stok.

Cara pakai
1. Buat proyek baru Laravel 12: `composer create-project laravel/laravel integ-lab-ui-crud`
2. Ekstrak ZIP ini lalu timpa ke root proyek
3. `composer require --dev phpunit/phpunit:^10.5 mockery/mockery:^1.6`
4. `php artisan migrate --seed`
5. Jalankan server: `php artisan serve` lalu buka http://localhost:8000
6. Jalankan uji: `vendor/bin/phpunit`
