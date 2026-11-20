# Spesifikasi Penyelesaian Modul DAK — SIMONEV v21

Dokumen ini adalah brief kerja untuk melengkapi modul **DAK (Dana Alokasi Khusus)** yang saat ini baru berupa kerangka menu tanpa isi. Ditulis untuk dikerjakan oleh Cursor. Backend: `backend/`, Frontend: `frontend/`.

## 1. Kondisi saat ini (sudah diverifikasi di kode)

- Backend hanya punya **3 endpoint** untuk DAK, semuanya di [`app/Http/Controllers/DAK/DAKMurniController.php`](backend/app/Http/Controllers/DAK/DAKMurniController.php):
  `index`, `reloadstatistik1`, `reloadstatistik2` — dipetakan di [`routes/api.php:534-536`](backend/routes/api.php:534).
- `index` sebenarnya **tidak memfilter DAK sama sekali** — ia mengagregasi *seluruh* `trRKARinc` dikelompokkan per `SumberDanaID` (semua sumber dana: APBD, DAK, DAU, dst), lalu ditampilkan sebagai chart "Progres Realisasi Fisik/Keuangan" di [`DAKMurni.vue`](frontend/src/views/pages/admin/dakmurni/DAKMurni.vue). Ini cuma dashboard ringkasan, bukan modul DAK yang sebenarnya.
- Frontend sidebar [`DAKMurniLayout.vue`](frontend/src/views/layouts/DAKMurniLayout.vue) mendefinisikan 8 menu (Data Mentah, Target Kinerja, RKA Murni, Pelaporan OPD, Form A, Form B OPD, Form B Unit Kerja, Peringkat OPD) tapi **7 dari 8 rute-nya tidak terdaftar** di [`router/index.js`](frontend/src/router/index.js) — hanya `/dakmurni` (dashboard) yang ada. Klik menu lain akan jatuh ke halaman 404.
- **Permission untuk 4 dari 8 menu bahkan tidak pernah didefinisikan** di [`database/seeders/PermissionsTableSeeder.php`](backend/database/seeders/PermissionsTableSeeder.php:64): hanya `DAK-GROUP`, `DAK-RKA-MURNI`, `DAK-RKA-PERUBAHAN`, `DAK-PELAPORAN-OPD` yang ada. Menu "Form A", "Form B OPD", "Form B Unit Kerja", "Peringkat OPD" pakai permission key (`DAK-FORM-A-MURNI_BROWSE`, `DAK-FORM-B-MURNI_BROWSE`, `DAK-STATISTIK-PERINGKAT-OPD_BROWSE`) yang tidak ada di seeder — jadi `CAN_ACCESS()` akan selalu `false` dan menu itu **tidak akan pernah muncul untuk user manapun**, bahkan sebelum dicoba diklik.
- Tidak ada varian "DAK Perubahan" sama sekali (berbeda dengan Renja yang selalu Murni+Perubahan).

## 2. Keputusan arsitektur — baca ini sebelum mulai coding

`trRKA` dan `trRKARinc` sudah punya kolom `SumberDanaID` (dibuktikan dipakai luas di [`RKAMurniController.php`](backend/app/Http/Controllers/Renja/RKAMurniController.php)), dan `tmJenisSumberDana` sudah punya kategori `DAK FISIK` (id 2) dan `DAK NON FISIK` (id 3) — lihat [`JenisSumberDanaTableSeeder.php`](backend/database/seeders/JenisSumberDanaTableSeeder.php:29).

**Artinya: data DAK sudah diinput lewat form RKA Murni/Perubahan yang biasa (OPD input kegiatan, pilih SumberDana = DAK Fisik/Non Fisik).** Modul DAK **tidak boleh** jadi form input CRUD terpisah (itu akan menduplikasi entry dan bikin data OPD tidak sinkron dengan RKA aslinya). Yang benar: modul DAK adalah **lapisan pelaporan/monitoring read-only** yang memfilter data RKA yang sudah ada berdasarkan `SumberDanaID` yang termasuk kategori DAK.

Pola ini sudah ada presedennya di codebase: [`RKPD\RKPDMurniController`](backend/app/Http/Controllers/RKPD/RKPDMurniController.php) juga cuma punya `index` (agregasi/rebuild), tanpa store/update/destroy — karena RKPD juga sekadar "lensa pelapor" di atas data RKA, bukan tempat entry primer. DAK harus mengikuti pola yang sama, bukan pola `RKAMurniController` (~20 endpoint CRUD).

Jadi untuk tiap menu DAK, cari controller Renja Murni yang setara, lalu **tambahkan filter** `WHERE SumberDanaID IN (id DAK Fisik, id DAK Non Fisik)` — bukan bikin CRUD baru dari nol.

## 3. Yang perlu dikonfirmasi ke Product Owner sebelum implementasi

- [ ] Apakah "DAK" di modul ini = gabungan **DAK Fisik + DAK Non Fisik**, atau harus bisa difilter terpisah per jenis?
- [ ] Apakah DAK memang tidak butuh varian "Perubahan" (APBD-P)? Kalau DAK juknis-nya bisa direvisi di tengah tahun, kemungkinan tetap butuh.
- [ ] "Data Mentah" untuk DAK — apakah perlu alur import SIPD terpisah, atau cukup filter dari Data Mentah Renja yang sudah ada?

## 4. Checklist Backend

Referensi pola: `Renja\{Controller}Murni` → equivalent read-only filtered version untuk DAK.

- [ ] Tambahkan constant/config untuk id `Id_Jenis_SumberDana` yang termasuk DAK (2 = Fisik, 3 = Non Fisik) — jangan hardcode angka mentah di banyak tempat, taruh di satu Helper (mis. `Helper::DAK_JENIS_SUMBERDANA_IDS`).
- [ ] **Data Mentah DAK**: endpoint baru `POST /dak/datamentahmurni` — reuse query [`DataMentahMurniController@index`](backend/app/Http/Controllers/Renja/DataMentahMurniController.php) + filter DAK.
- [ ] **Target Kinerja DAK**: endpoint baru `POST /dak/targetkinerjamurni` — reuse [`TargetKinerjaMurniController@index`](backend/app/Http/Controllers/Renja/TargetKinerjaMurniController.php) (sudah join `SumberDanaID`, tinggal tambah where clause) + `targetkinerjauraian`.
- [ ] **RKA DAK (read-only)**: endpoint baru `POST /dak/rkamurni` — reuse query index [`RKAMurniController@index`](backend/app/Http/Controllers/Renja/RKAMurniController.php) + filter DAK. **Tidak perlu** store/update/destroy — user tetap edit lewat menu RKA Renja biasa.
- [ ] **Pelaporan OPD DAK**: endpoint `POST /dak/pelaporanopdmurni` — reuse [`PelaporanOPDMurniController`](backend/app/Http/Controllers/Renja/PelaporanOPDMurniController.php) + filter DAK. Konfirmasi dulu ke PO apakah OPD perlu submit laporan DAK terpisah dari laporan Renja biasa (kalau ya, ini butuh store juga, bukan cuma index).
- [ ] **Form A DAK**: endpoint `POST /dak/report/forma` + `printtoexcel` — reuse [`FormAMurniController`](backend/app/Http/Controllers/Renja/FormAMurniController.php) + filter DAK.
- [ ] **Form B OPD/Unit Kerja DAK**: endpoint `POST /dak/report/formbopd`, `/dak/report/formbunitkerja` (+ `chart`, `printtoexcel`) — reuse [`FormBOPDMurniController`](backend/app/Http/Controllers/Renja/FormBOPDMurniController.php) / [`FormBUnitKerjaMurniController`](backend/app/Http/Controllers/Renja/FormBUnitKerjaMurniController.php) + filter DAK.
- [ ] **Peringkat OPD DAK**: endpoint `POST /dak/statistik/peringkatopd` — reuse [`PeringkatOPDMurniController`](backend/app/Http/Controllers/Renja/PeringkatOPDMurniController.php) + filter DAK.
- [ ] Tambahkan permission key yang hilang ke [`PermissionsTableSeeder.php`](backend/database/seeders/PermissionsTableSeeder.php): `DAK-DATA-MENTAH-MURNI`, `DAK-TARGET-KINERJA-MURNI`, `DAK-FORM-A-MURNI`, `DAK-FORM-B-MURNI`, `DAK-STATISTIK-PERINGKAT-OPD` (masing-masing dengan suffix `_BROWSE` dsb sesuai konvensi permission yang sudah ada), lalu jalankan seeder & assign ke role yang relevan.
- [ ] Semua route baru didaftarkan di `routes/api.php` dengan prefix `/dak/...` mengikuti pola middleware role yang sama dengan Renja Murni (`superadmin|bapelitbang|opd|unitkerja|pptk`).

## 5. Checklist Frontend

- [ ] Buat 8 halaman `.vue` baru di `frontend/src/views/pages/admin/dakmurni/` (nama file mengikuti pola yang sudah ada untuk Renja Murni, mis. `DataMentahMurni.vue`, `TargetKinerjaMurni.vue`, `RKAMurni.vue`, dst), masing-masing **read-only** (tabel tanpa tombol create/edit/delete kecuali untuk Pelaporan OPD, tergantung jawaban PO di §3).
- [ ] Daftarkan 7 rute yang hilang di `router/index.js`: `/dakmurni/datamentah`, `/dakmurni/targetkinerja`, `/dakmurni/rka`, `/dakmurni/pelaporanopd`, `/dakmurni/report/forma`, `/dakmurni/report/formbopd`, `/dakmurni/report/formbunitkerja`, `/dakmurni/statistik/peringkatopd`.
- [ ] Setelah permission baru ditambahkan di backend (§4), pastikan key yang dipakai di [`DAKMurniLayout.vue`](frontend/src/views/layouts/DAKMurniLayout.vue) (`DAK-FORM-A-MURNI_BROWSE`, dll) cocok persis dengan yang di-seed.
- [ ] Tambahkan tombol export Excel yang manggil endpoint `printtoexcel` masing-masing (ikuti pola tombol export yang sudah benar di [`EvaluasiRealisasiMurniTW.vue`](frontend/src/views/pages/admin/renjamurni/EvaluasiRealisasiMurniTW.vue:126) — **jangan** ikuti pola yang salah di [`DashboardRKPDMurni.vue:43`](frontend/src/views/pages/admin/rkpdmurni/DashboardRKPDMurni.vue:43) yang manggil method yang tidak ada).

## 6. Definition of done

- Semua 8 menu sidebar DAK Murni bisa diklik tanpa 404 dan menampilkan data yang benar-benar terfilter DAK (verifikasi: total pagu di halaman DAK harus lebih kecil dari total pagu Renja Murni, dan cocok dengan `SUM(PaguUraian1) WHERE Id_Jenis_SumberDana IN (2,3)`).
- User dengan role OPD hanya melihat DAK milik OPD-nya sendiri (pola `getUserOrgID()` yang sudah dipakai di controller lain).
- Tidak ada endpoint DAK yang menduplikasi kemampuan create/update RKA (itu tetap lewat menu Renja Murni/Perubahan).
