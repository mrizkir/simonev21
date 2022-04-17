<?php
$router->get('/', function () use ($router) {
	return 'SIMONEV PERMENDARI 98 API';
});

$router->group(['prefix'=>'v1'], function () use ($router)
{
	//dashboard
	$router->post('/dashboard/front',['uses'=>'DashboardController@indexfront','as'=>'dashboard.indexfront']);
	$router->post('/dashboard/pelaporanopd',['uses'=>'Statistik\PelaporanOPDController@front','as'=>'pelaporanopd.front']);
	$router->post('/dashboard/laporanrealisasimurni',['uses'=>'Statistik\LaporanRealisasiMurniController@front','as'=>'laporanrealisasimurni.front']);
	$router->post('/dashboard/laporanrealisasiperubahan',['uses'=>'Statistik\LaporanRealisasiPerubahanController@front','as'=>'laporanrealisasiperubahan.front']);

	//dmaster
	$router->post('/dmaster',['uses'=>'DMaster\DMasterController@index','as'=>'dmaster.index']);

	//dmaster - provinsi
	$router->get('/dmaster/provinsi',['uses'=>'DMaster\ProvinsiController@index','as'=>'provinsi.index']);
	$router->get('/dmaster/provinsi/{id}/kabupaten',['uses'=>'DMaster\ProvinsiController@kabupaten','as'=>'provinsi.kabupaten']);

	//dmaster - kabupaten
	$router->get('/dmaster/kabupaten',['uses'=>'DMaster\KabupatenController@index','as'=>'kabupaten.index']);
	$router->get('/dmaster/kabupaten/{id}/kecamatan',['uses'=>'DMaster\KabupatenController@kecamatan','as'=>'kabupaten.kecamatan']);

	//dmaster - kecamatan
	$router->get('/dmaster/kecamatan',['uses'=>'DMaster\KecamatanController@index','as'=>'kecamatan.index']);
	$router->get('/dmaster/kecamatan/{id}/desa',['uses'=>'DMaster\KecamatanController@desa','as'=>'kecamatan.desa']);

	//dmaster - tahun anggaran
	$router->get('/dmaster/ta',['uses'=>'DMaster\TAController@index','as'=>'ta.index']);

	//untuk uifront
	$router->get('/system/setting/uifront',['uses'=>'System\UIController@frontend','as'=>'uifront.frontend']);

	//auth login
	$router->post('/auth/login',['uses'=>'AuthController@login','as'=>'auth.login']);
});

$router->group(['prefix'=>'v1','middleware'=>'auth:api'], function () use ($router)
{
	//authentication
	$router->post('/auth/logout',['uses'=>'AuthController@logout','as'=>'auth.logout']);
	$router->get('/auth/refresh',['uses'=>'AuthController@refresh','as'=>'auth.refresh']);
	$router->get('/auth/me',['uses'=>'AuthController@me','as'=>'auth.me']);

	//data master - kodefikasi - urusan
	$router->post('/dmaster/kodefikasi/urusan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiUrusanController@index','as'=>'kodefikasi-urusan.index']);
	$router->post('/dmaster/kodefikasi/urusan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiUrusanController@store','as'=>'kodefikasi-urusan.store']);
	$router->post('/dmaster/kodefikasi/urusan/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiUrusanController@salin','as'=>'kodefikasi-urusan.salin']);
	$router->put('/dmaster/kodefikasi/urusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiUrusanController@update','as'=>'kodefikasi-urusan.update']);
	$router->delete('/dmaster/kodefikasi/urusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiUrusanController@destroy','as'=>'kodefikasi-urusan.destroy']);

	//data master - kodefikasi - bidang urusan
	$router->post('/dmaster/kodefikasi/bidangurusan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiBidangUrusanController@index','as'=>'kodefikasi-bidang-urusan.index']);
	$router->post('/dmaster/kodefikasi/bidangurusan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiBidangUrusanController@store','as'=>'kodefikasi-bidang-urusan.store']);
	$router->post('/dmaster/kodefikasi/bidangurusan/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiBidangUrusanController@salin','as'=>'kodefikasi-bidang-urusan.salin']);
	$router->post('/dmaster/kodefikasi/bidangurusan/{id}/program',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiBidangUrusanController@program','as'=>'kodefikasi-bidang-urusan.program']);
	$router->put('/dmaster/kodefikasi/bidangurusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiBidangUrusanController@update','as'=>'kodefikasi-bidang-urusan.update']);
	$router->delete('/dmaster/kodefikasi/bidangurusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiBidangUrusanController@destroy','as'=>'kodefikasi-bidang-urusan.destroy']);

	//data master - kodefikasi - program
	$router->post('/dmaster/kodefikasi/program',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiProgramController@index','as'=>'kodefikasi-program.index']);
	$router->post('/dmaster/kodefikasi/program/rka',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiProgramController@rka','as'=>'kodefikasi-program.rka']);
	$router->post('/dmaster/kodefikasi/program/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiProgramController@store','as'=>'kodefikasi-program.store']);
	$router->put('/dmaster/kodefikasi/program/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiProgramController@update','as'=>'kodefikasi-program.update']);
	$router->get('/dmaster/kodefikasi/program/{id}/kegiatan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiProgramController@kegiatan','as'=>'kodefikasi-program.kegiatan']);
	$router->delete('/dmaster/kodefikasi/program/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiProgramController@destroy','as'=>'kodefikasi-program.destroy']);

	//data master - kodefikasi - kegiatan
	$router->post('/dmaster/kodefikasi/kegiatan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiKegiatanController@index','as'=>'kodefikasi-kegiatan.index']);
	$router->post('/dmaster/kodefikasi/kegiatan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiKegiatanController@store','as'=>'kodefikasi-kegiatan.store']);
	$router->post('/dmaster/kodefikasi/kegiatan/{id}/subkegiatanrka',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiKegiatanController@subkegiatanrka','as'=>'kodefikasi-kegiatan.subkegiatanrka']);
	$router->put('/dmaster/kodefikasi/kegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiKegiatanController@update','as'=>'kodefikasi-kegiatan.update']);
	$router->delete('/dmaster/kodefikasi/kegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiKegiatanController@destroy','as'=>'kodefikasi-kegiatan.destroy']);

	//data master - kodefikasi - sub kegiatan
	$router->post('/dmaster/kodefikasi/subkegiatan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\KodefikasiSubKegiatanController@index','as'=>'kodefikasi-subkegiatan.index']);
	$router->post('/dmaster/kodefikasi/subkegiatan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiSubKegiatanController@store','as'=>'kodefikasi-subkegiatan.store']);
	$router->put('/dmaster/kodefikasi/subkegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiSubKegiatanController@update','as'=>'kodefikasi-subkegiatan.update']);
	$router->delete('/dmaster/kodefikasi/subkegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiSubKegiatanController@destroy','as'=>'kodefikasi-subkegiatan.destroy']);

	//data master - rekening - akun
	$router->post('/dmaster/rekening/akun',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningAkunController@index','as'=>'rekening.akun.index']);
	$router->post('/dmaster/rekening/akun/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningAkunController@store','as'=>'rekening.akun.store']);
	$router->post('/dmaster/rekening/akun/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningAkunController@salin','as'=>'rekening.akun.salin']);
	$router->get('/dmaster/rekening/akun/{id}/kelompok',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningAkunController@kelompok','as'=>'rekening.akun.kelompok']);
	$router->put('/dmaster/rekening/akun/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningAkunController@update','as'=>'rekening.akun.update']);
	$router->delete('/dmaster/rekening/akun/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningAkunController@destroy','as'=>'rekening.akun.destroy']);

	//data master - rekening - kelompok
	$router->post('/dmaster/rekening/kelompok',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningKelompokController@index','as'=>'rekening.kelompok.index']);
	$router->post('/dmaster/rekening/kelompok/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningKelompokController@store','as'=>'rekening.kelompok.store']);
	$router->post('/dmaster/rekening/kelompok/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningKelompokController@salin','as'=>'rekening.kelompok.salin']);
	$router->get('/dmaster/rekening/kelompok/{id}/jenis',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningKelompokController@jenis','as'=>'rekening.kelompok.jenis']);
	$router->put('/dmaster/rekening/kelompok/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningKelompokController@update','as'=>'rekening.kelompok.update']);
	$router->delete('/dmaster/rekening/kelompok/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningKelompokController@destroy','as'=>'rekening.kelompok.destroy']);

	//data master - rekening - jenis
	$router->post('/dmaster/rekening/jenis',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningJenisController@index','as'=>'rekening.jenis.index']);
	$router->post('/dmaster/rekening/jenis/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningJenisController@store','as'=>'rekening.jenis.store']);
	$router->post('/dmaster/rekening/jenis/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningJenisController@salin','as'=>'rekening.jenis.salin']);
	$router->get('/dmaster/rekening/jenis/{id}/objekrka',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningJenisController@objekrka','as'=>'rekening.jenis.objekrka']);
	$router->put('/dmaster/rekening/jenis/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningJenisController@update','as'=>'rekening.jenis.update']);
	$router->delete('/dmaster/rekening/jenis/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningJenisController@destroy','as'=>'rekening.jenis.destroy']);

	//data master - rekening - objek
	$router->post('/dmaster/rekening/objek',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningObjekController@index','as'=>'rekening.objek.index']);
	$router->post('/dmaster/rekening/objek/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningObjekController@store','as'=>'rekening.objek.store']);
	$router->post('/dmaster/rekening/objek/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningObjekController@salin','as'=>'rekening.objek.salin']);
	$router->get('/dmaster/rekening/objek/{id}/rincianobjekrka',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningObjekController@rincianobjekrka','as'=>'rekening.objek.rincianobjekrka']);
	$router->put('/dmaster/rekening/objek/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningObjekController@update','as'=>'rekening.objek.update']);
	$router->delete('/dmaster/rekening/objek/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningObjekController@destroy','as'=>'rekening.objek.destroy']);

	//data master - rekening - rincian objek
	$router->post('/dmaster/rekening/rincianobjek',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningRincianObjekController@index','as'=>'rekening.rincianobjek.index']);
	$router->post('/dmaster/rekening/rincianobjek/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningRincianObjekController@store','as'=>'rekening.rincianobjek.store']);
	$router->post('/dmaster/rekening/rincianobjek/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningRincianObjekController@salin','as'=>'rekening.rincianobjek.salin']);
	$router->get('/dmaster/rekening/rincianobjek/{id}/subrincianobjekrka',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningRincianObjekController@subrincianobjekrka','as'=>'rekening.rincianobjek.subrincianobjekrka']);
	$router->put('/dmaster/rekening/rincianobjek/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningRincianObjekController@update','as'=>'rekening.rincianobjek.update']);
	$router->delete('/dmaster/rekening/rincianobjek/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningRincianObjekController@destroy','as'=>'rekening.rincianobjek.destroy']);

	//data master - rekening - sub rincian objek
	$router->post('/dmaster/rekening/subrincianobjek',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\RekeningSubRincianObjekController@index','as'=>'rekening.subrincianobjek.index']);
	$router->post('/dmaster/rekening/subrincianobjek/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningSubRincianObjekController@store','as'=>'rekening.subrincianobjek.store']);
	$router->post('/dmaster/rekening/subrincianobjek/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningSubRincianObjekController@salin','as'=>'rekening.subrincianobjek.salin']);
	$router->put('/dmaster/rekening/subrincianobjek/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningSubRincianObjekController@update','as'=>'rekening.subrincianobjek.update']);
	$router->delete('/dmaster/rekening/subrincianobjek/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\RekeningSubRincianObjekController@destroy','as'=>'rekening.subrincianobjek.destroy']);

	//data master - opd
	$router->post('/dmaster/opd',['uses'=>'DMaster\OrganisasiController@index','as'=>'opd.index']);
	$router->post('/dmaster/opd/loadopd',['middleware'=>['role:superadmin'],'uses'=>'DMaster\OrganisasiController@loadopd','as'=>'opd.loadopd']);
	$router->post('/dmaster/opd/loadpaguapbdp',['middleware'=>['role:superadmin'],'uses'=>'DMaster\OrganisasiController@loadpaguapbdp','as'=>'opd.loadpaguapbdp']);
	$router->post('/dmaster/opd/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\OrganisasiController@store','as'=>'opd.store']);
	$router->post('/dmaster/opd/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\OrganisasiController@salin','as'=>'opd.salin']);
	$router->put('/dmaster/opd/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\OrganisasiController@update','as'=>'opd.update']);
	$router->get('/dmaster/opd/{id}/unitkerja',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'DMaster\OrganisasiController@opdunitkerja','as'=>'opd.unitkerja']);
	$router->get('/dmaster/opd/{id}/pejabat',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\OrganisasiController@pejabatopd','as'=>'opd.pejabatopd']);
	$router->delete('/dmaster/opd/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\OrganisasiController@destroy','as'=>'opd.destroy']);

	//data master - unit kerja
	$router->post('/dmaster/unitkerja',['uses'=>'DMaster\SubOrganisasiController@index','as'=>'unitkerja.index']);
	$router->post('/dmaster/unitkerja/loadunitkerja',['middleware'=>['role:superadmin'],'uses'=>'DMaster\SubOrganisasiController@loadunitkerja','as'=>'unitkerja.loadunitkerja']);
	$router->post('/dmaster/unitkerja/loadpaguapbdp',['middleware'=>['role:superadmin'],'uses'=>'DMaster\SubOrganisasiController@loadpaguapbdp','as'=>'unitkerja.loadpaguapbdp']);
	$router->post('/dmaster/unitkerja/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\SubOrganisasiController@store','as'=>'unitkerja.store']);
	$router->post('/dmaster/unitkerja/salin',['middleware'=>['role:superadmin|bapelitbang|unitkerja'],'uses'=>'DMaster\SubOrganisasiController@salin','as'=>'unitkerja.salin']);
	$router->put('/dmaster/unitkerja/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'DMaster\SubOrganisasiController@update','as'=>'unitkerja.update']);
	$router->delete('/dmaster/unitkerja/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\SubOrganisasiController@destroy','as'=>'unitkerja.destroy']);

	//data master - ASN
	$router->post('/dmaster/asn',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\ASNController@index','as'=>'ASN.index']);
	$router->post('/dmaster/asn/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\ASNController@store','as'=>'ASN.store']);
	$router->post('/dmaster/asn/salin',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\ASNController@salin','as'=>'ASN.salin']);
	$router->put('/dmaster/asn/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\ASNController@update','as'=>'ASN.update']);
	$router->delete('/dmaster/asn/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\ASNController@destroy','as'=>'ASN.destroy']);

	//data master - Pejabat
	$router->post('/dmaster/pejabat',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\PejabatController@index','as'=>'pejabat.index']);
	$router->post('/dmaster/pejabat/store',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\PejabatController@store','as'=>'pejabat.store']);
	$router->put('/dmaster/pejabat/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\PejabatController@update','as'=>'pejabat.update']);
	$router->delete('/dmaster/pejabat/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\PejabatController@destroy','as'=>'pejabat.destroy']);

	//data master - Sumber Dana
	$router->post('/dmaster/sumberdana',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\SumberDanaController@index','as'=>'sumberdana.index']);
	$router->post('/dmaster/sumberdana/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\SumberDanaController@store','as'=>'sumberdana.store']);
	$router->put('/dmaster/sumberdana/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\SumberDanaController@update','as'=>'sumberdana.update']);
	$router->delete('/dmaster/sumberdana/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\SumberDanaController@destroy','as'=>'sumberdana.destroy']);

	//data master - Tahun Anggaran
	$router->post('/dmaster/ta/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\TAController@store','as'=>'ta.store']);
	$router->put('/dmaster/ta/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\TAController@update','as'=>'ta.update']);
	$router->delete('/dmaster/ta/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\TAController@destroy','as'=>'ta.destroy']);

	//data master - kegiatan - jenis pelaksanaan
	$router->post('/dmaster/jenispelaksanaan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\JenisPelaksanaanController@index','as'=>'jenispelaksanaan.index']);
	$router->post('/dmaster/jenispelaksanaan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\JenisPelaksanaanController@store','as'=>'jenispelaksanaan.store']);
	$router->put('/dmaster/jenispelaksanaan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\JenisPelaksanaanController@update','as'=>'jenispelaksanaan.update']);
	$router->delete('/dmaster/jenispelaksanaan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\JenisPelaksanaanController@destroy','as'=>'jenispelaksanaan.destroy']);

	//data master - kegiatan - jenis pembangunan
	$router->post('/dmaster/jenispembangunan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'DMaster\JenisPembangunanController@index','as'=>'jenispembangunan.index']);
	$router->post('/dmaster/jenispembangunan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\JenisPembangunanController@store','as'=>'jenispembangunan.store']);
	$router->put('/dmaster/jenispembangunan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\JenisPembangunanController@update','as'=>'jenispembangunan.update']);
	$router->delete('/dmaster/jenispembangunan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\JenisPembangunanController@destroy','as'=>'jenispembangunan.destroy']);

	//renja murni
	$router->post('/renjamurni',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RenjaMurniController@index','as'=>'renjamurni.index']);
	$router->post('/renjamurni/reloadstatistik1',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RenjaMurniController@reloadstatistik1','as'=>'renjamurni.reloadstatistik1']);
	$router->post('/renjamurni/reloadstatistik2',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RenjaMurniController@reloadstatistik2','as'=>'renjamurni.reloadstatistik2']);

	//renja - statistik - peringkat opd
	$router->post('/renjamurni/statistik/peringkatopd',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\PeringkatOPDMurniController@index','as'=>'renjamurni-peringkatopdmurni.index']);

	//renja - data mentah murni
	$router->post('/renja/datamentahmurni',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'Renja\DataMentahMurniController@index','as'=>'v1.datamentahmurni.index']);
	$router->post('/renja/datamentahmurni/copyrka',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'Renja\DataMentahMurniController@copyrka','as'=>'v1.datamentahmurni.copyrka']);

	//renja - rka murni
	$router->post('/renja/rkamurni',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk|dewan|tapd'],'uses'=>'Renja\RKAMurniController@index','as'=>'rkamurni.index']);
	$router->get('/renja/rkamurni/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@show','as'=>'rkamurni.show']);
	$router->post('/renja/rkamurni/storekegiatan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@storekegiatan','as'=>'rkamurni.storekegiatan']);
	$router->put('/renja/rkamurni/updatekegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@updatekegiatan','as'=>'rkamurni.updatekegiatan']);
	//reset data akan mengeset ulang data kegiata meliputi, jumlah pagu, realisasi, dan lain2
	$router->put('/renja/rkamurni/resetdatakegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@resetdatakegiatan','as'=>'rkamurni.resetdatakegiatan']);
	$router->post('/renja/rkamurni/storeuraian',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@storeuraian','as'=>'rkamurni.storeuraian']);
	$router->put('/renja/rkamurni/updateuraian/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@updateuraian','as'=>'rkamurni.updateuraian']);
	$router->put('/renja/rkamurni/updatedetailuraian/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@updatedetailuraian','as'=>'rkamurni.updatedetailuraian']);
	$router->post('/renja/rkamurni/rencanatarget',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@rencanatarget','as'=>'rkamurni.rencanatarget']);
	$router->post('/renja/rkamurni/savetargetfisik',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@savetargetfisik','as'=>'rkamurni.savetargetfisik']);
	$router->put('/renja/rkamurni/updatetargetfisik',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@updatetargetfisik','as'=>'rkamurni.updatetargetfisik']);
	$router->get('/renja/rkamurni/bulanrealisasi/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@bulanrealisasi','as'=>'rkamurni.bulanrealisasi']);
	$router->post('/renja/rkamurni/savetargetanggarankas',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@savetargetanggarankas','as'=>'rkamurni.savetargetanggarankas']);
	$router->put('/renja/rkamurni/updatetargetanggarankas',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@updatetargetanggarankas','as'=>'rkamurni.updatetargetanggarankas']);
	$router->post('/renja/rkamurni/realisasi',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@realisasi','as'=>'rkamurni.realisasi']);
	$router->post('/renja/rkamurni/saverealisasi',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@saverealisasi','as'=>'rkamurni.saverealisasi']);
	$router->put('/renja/rkamurni/updaterealisasi/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@updaterealisasi','as'=>'rkamurni.updaterealisasi']);
	$router->delete('/renja/rkamurni/deleterealisasi/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@destroy','as'=>'rkamurni.deleterealisasi']);
	$router->post('/renja/rkamurni/loaddatakegiatanfirsttime',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|'],'uses'=>'Renja\RKAMurniController@loaddatakegiatanFirsttime','as'=>'rkamurni.loaddatakegiatanfirsttime']);
	$router->post('/renja/rkamurni/loaddatauraianfirsttime',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|'],'uses'=>'Renja\RKAMurniController@loaddatauraianFirsttime','as'=>'rkamurni.loaddatauraianfirsttime']);
	$router->delete('/renja/rkamurni/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAMurniController@destroy','as'=>'rkamurni.deleterka']);

	//renja - target kinerja murni
	$router->post('/renja/targetkinerjamurni',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk|dewan|tapd'],'uses'=>'Renja\TargetKinerjaMurniController@index','as'=>'targetkinerjaM.index']);
	//id  = RKARIncID
	$router->get('/renja/targetkinerjamurni/{id}/uraian',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk|dewan|tapd'],'uses'=>'Renja\TargetKinerjaMurniController@targetkinerjauraian','as'=>'targetkinerjaM.targetkinerjauraian']);
	$router->put('/renja/targetkinerjamurni/updatetargetanggarankas',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\TargetKinerjaMurniController@updatetargetanggarankas','as'=>'targetkinerjaM.updatetargetanggarankas']);
	$router->put('/renja/targetkinerjamurni/updatetargetfisik',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\TargetKinerjaMurniController@updatetargetfisik','as'=>'targetkinerjaM.updatetargetfisik']);
	$router->delete('/renja/targetkinerjamurni/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\TargetKinerjaMurniController@destroy','as'=>'targetkinerjaM.destroy']);

	//renja - pelaporan opd
	$router->post('/renja/pelaporanopdmurni',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'Renja\PelaporanOPDMurniController@index','as'=>'pelaporanopd.index']);
	//id  = OrgID
	$router->get('/renja/pelaporanopdmurni/bulanpelaporan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'Renja\PelaporanOPDMurniController@bulanpelaporan','as'=>'pelaporanopd.bulanpelaporan']);

	//renja - report - form a
	$router->post('/renjamurni/report/forma',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormAMurniController@index','as'=>'formamurni.index']);
	$router->post('/renjamurni/report/forma/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormAMurniController@printtoexcel','as'=>'formamurni.printtoexcel']);    

	//renja - report - form b unit kerja
	$router->post('/renjamurni/report/formbunitkerja',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaMurniController@index','as'=>'formbunitkerjamurni.index']);
	$router->post('/renjamurni/report/formbunitkerja/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaMurniController@printtoexcel','as'=>'formbunitkerjamurni.printtoexcel']);

	//report - form b opd murni
	$router->post('/renjamurni/report/formbopd',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBOPDMurniController@index','as'=>'formbopdmurni.index']);    
	$router->post('/renjamurni/report/formbopd/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBOPDMurniController@printtoexcel','as'=>'formbopdmurni.printtoexcel']);    
	
	//report - form b unit kerja murni
	$router->post('/renjamurni/report/formbunitkerjamurni',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaMurniController@index','as'=>'formbunitkerjamurni.index']);    
	$router->post('/renjamurni/report/formbunitkerjamurni/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaMurniController@printtoexcel','as'=>'formbunitkerjamurni.printtoexcel']);    
	
	//renja perubahan
	$router->post('/renjaperubahan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RenjaPerubahanController@index','as'=>'renjaperubahan.index']);
	$router->post('/renjaperubahan/reloadstatistik1',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RenjaPerubahanController@reloadstatistik1','as'=>'renjaperubahan.reloadstatistik1']);
	$router->post('/renjaperubahan/reloadstatistik2',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RenjaPerubahanController@reloadstatistik2','as'=>'renjaperubahan.reloadstatistik2']);

	$router->post('/renjaperubahan/statistik/peringkatopd',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\PeringkatOPDPerubahanController@index','as'=>'renjaperubahan-peringkatopdperubahan.index']);
	
	//renja - data mentah perubahan
	$router->post('/renja/datamentahperubahan',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'Renja\DataMentahPerubahanController@index','as'=>'v1.datamentahperubahan.index']);
	$router->post('/renja/datamentahperubahan/copyrka',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'Renja\DataMentahPerubahanController@copyrka','as'=>'v1.datamentahperubahan.copyrka']);

	//renja - rka perubahan
	$router->post('/renja/rkaperubahan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk|dewan|tapd'],'uses'=>'Renja\RKAPerubahanController@index','as'=>'rkaperubahan.index']);
	$router->get('/renja/rkaperubahan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@show','as'=>'rkaperubahan.show']);
	$router->post('/renja/rkaperubahan/storekegiatan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@storekegiatan','as'=>'rkaperubahan.storekegiatan']);
	$router->put('/renja/rkaperubahan/updatekegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@updatekegiatan','as'=>'rkaperubahan.updatekegiatan']);
	//reset data akan mengeset ulang data kegiata meliputi, jumlah pagu, realisasi, dan lain2
	$router->put('/renja/rkaperubahan/resetdatakegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@resetdatakegiatan','as'=>'rkaperubahan.resetdatakegiatan']);
	$router->post('/renja/rkaperubahan/storeuraian',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@storeuraian','as'=>'rkaperubahan.storeuraian']);
	$router->put('/renja/rkaperubahan/updateuraian/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@updateuraian','as'=>'rkaperubahan.updateuraian']);
	$router->put('/renja/rkaperubahan/updatedetailuraian/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@updatedetailuraian','as'=>'rkaperubahan.updatedetailuraian']);
	$router->post('/renja/rkaperubahan/rencanatarget',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@rencanatarget','as'=>'rkaperubahan.rencanatarget']);
	$router->post('/renja/rkaperubahan/savetargetfisik',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@savetargetfisik','as'=>'rkaperubahan.savetargetfisik']);
	$router->put('/renja/rkaperubahan/updatetargetfisik',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@updatetargetfisik','as'=>'rkaperubahan.updatetargetfisik']);
	$router->get('/renja/rkaperubahan/bulanrealisasi/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@bulanrealisasi','as'=>'rkaperubahan.bulanrealisasi']);
	$router->post('/renja/rkaperubahan/savetargetanggarankas',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@savetargetanggarankas','as'=>'rkaperubahan.savetargetanggarankas']);
	$router->put('/renja/rkaperubahan/updatetargetanggarankas',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@updatetargetanggarankas','as'=>'rkaperubahan.updatetargetanggarankas']);
	$router->post('/renja/rkaperubahan/realisasi',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@realisasi','as'=>'rkaperubahan.realisasi']);
	$router->post('/renja/rkaperubahan/saverealisasi',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@saverealisasi','as'=>'rkaperubahan.saverealisasi']);
	$router->put('/renja/rkaperubahan/updaterealisasi/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@updaterealisasi','as'=>'rkaperubahan.updaterealisasi']);
	$router->delete('/renja/rkaperubahan/deleterealisasi/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@destroy','as'=>'rkaperubahan.deleterealisasi']);
	$router->post('/renja/rkaperubahan/loaddatakegiatanfirsttime',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|'],'uses'=>'Renja\RKAPerubahanController@loaddatakegiatanFirsttime','as'=>'rkaperubahan.loaddatakegiatanfirsttime']);
	$router->post('/renja/rkaperubahan/loaddatauraianfirsttime',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|'],'uses'=>'Renja\RKAPerubahanController@loaddatauraianFirsttime','as'=>'rkaperubahan.loaddatauraianfirsttime']);
	$router->delete('/renja/rkaperubahan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\RKAPerubahanController@destroy','as'=>'rkaperubahan.deleterka']);

	//renja perubahan - report - form a
	$router->post('/renjaperubahan/report/forma',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormAPerubahanController@index','as'=>'formaperubahan.index']);
	$router->post('/renjaperubahan/report/forma/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormAPerubahanController@printtoexcel','as'=>'formaperubahan.printtoexcel']);    

	//renja perubahan - report - form b unit kerja
	$router->post('/renjaperubahan/report/formbunitkerja',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaPerubahanController@index','as'=>'formbunitkerjaperubahan.index']);
	$router->post('/renjaperubahan/report/formbunitkerja/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaPerubahanController@printtoexcel','as'=>'formbunitkerjaperubahan.printtoexcel']);

	//renja perubahan - form b opd perubahan
	$router->post('/renjaperubahan/report/formbopd',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBOPDPerubahanController@index','as'=>'formbopdperubahan.index']);    
	$router->post('/renjaperubahan/report/formbopd/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBOPDPerubahanController@printtoexcel','as'=>'formbopdperubahan.printtoexcel']);    
	
	//renja perubahan - form b unit kerja perubahan
	$router->post('/renjaperubahan/report/formbunitkerja',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaPerubahanController@index','as'=>'formbunitkerja.index']);    
	$router->post('/renjaperubahan/report/formbunitkerja/printtoexcel',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\FormBUnitKerjaPerubahanController@printtoexcel','as'=>'formbunitkerja.printtoexcel']);    
	
	//renja - target kinerja perubahan
	$router->post('/renja/targetkinerjaperubahan',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk|dewan|tapd'],'uses'=>'Renja\TargetKinerjaPerubahanController@index','as'=>'targetkinerjaP.index']);
	//id  = RKARIncID
	$router->get('/renja/targetkinerjaperubahan/{id}/uraian',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk|dewan|tapd'],'uses'=>'Renja\TargetKinerjaPerubahanController@targetkinerjauraian','as'=>'targetkinerjaP.targetkinerjauraian']);
	$router->put('/renja/targetkinerjaperubahan/updatetargetanggarankas',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\TargetKinerjaPerubahanController@updatetargetanggarankas','as'=>'targetkinerjaP.updatetargetanggarankas']);
	$router->put('/renja/targetkinerjaperubahan/updatetargetfisik',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\TargetKinerjaPerubahanController@updatetargetfisik','as'=>'targetkinerjaP.updatetargetfisik']);
	$router->delete('/renja/targetkinerjaperubahan/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja|pptk'],'uses'=>'Renja\TargetKinerjaPerubahanController@destroy','as'=>'targetkinerjaP.destroy']);

	//setting - permissions
	$router->get('/system/setting/permissions',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\PermissionsController@index','as'=>'permissions.index']);
	$router->post('/system/setting/permissions/store',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@store','as'=>'permissions.store']);
	$router->delete('/system/setting/permissions/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@destroy','as'=>'permissions.destroy']);

	//setting - roles
	$router->get('/system/setting/roles',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\RolesController@index','as'=>'roles.index']);
	$router->post('/system/setting/roles/store',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@store','as'=>'roles.store']);
	$router->post('/system/setting/roles/storerolepermissions',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@storerolepermissions','as'=>'roles.storerolepermissions']);
	$router->post('/system/setting/roles/revokerolepermissions',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@revokerolepermissions','as'=>'users.revokerolepermissions']);
	$router->put('/system/setting/roles/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@update','as'=>'roles.update']);
	$router->delete('/system/setting/roles/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@destroy','as'=>'roles.destroy']);
	$router->get('/system/setting/roles/{id}/permission',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@rolepermissions','as'=>'roles.permission']);
	$router->get('/system/setting/rolesbyname/{id}/permission',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@rolepermissionsbyname','as'=>'roles.permissionbyname']);

	//setting - variables
	$router->get('/system/setting/variables',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@index','as'=>'variables.index']);
	$router->get('/system/setting/variables/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@show','as'=>'variables.show']);
	$router->put('/system/setting/variables',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@update','as'=>'variables.update']);
	$router->post('/system/setting/variables/clear',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@clear','as'=>'variables.clear']);

	//setting - users
	$router->get('/system/users',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@index','as'=>'users.index']);
	$router->post('/system/users/store',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@store','as'=>'users.store']);
	$router->put('/system/users/updatepassword/{id}',['uses'=>'System\UsersController@updatepassword','as'=>'users.updatepassword']);
	$router->post('/system/users/uploadfoto/{id}',['uses'=>'System\UsersController@uploadfoto','as'=>'users.uploadfoto']);
	$router->post('/system/users/resetfoto/{id}',['uses'=>'System\UsersController@resetfoto','as'=>'users.resetfoto']);
	$router->post('/system/users/syncallpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@syncallpermissions','as'=>'users.syncallpermissions']);
	$router->post('/system/users/storeuserpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@storeuserpermissions','as'=>'users.storeuserpermissions']);
	$router->post('/system/users/revokeuserpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@revokeuserpermissions','as'=>'users.revokeuserpermissions']);
	$router->put('/system/users/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@update','as'=>'users.update']);
	$router->get('/system/users/{id}',['uses'=>'System\UsersController@show','as'=>'users.show']);
	$router->delete('/system/users/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@destroy','as'=>'users.destroy']);
	//lokasi method userpermission ada di file UserController
	$router->get('/system/users/{id}/permission',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@userpermissions','as'=>'users.permission']);
	$router->get('/system/users/{id}/mypermission',['uses'=>'System\UsersController@mypermission','as'=>'users.mypermission']);
	$router->get('/system/users/{id}/opd',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersController@usersopd','as'=>'users.opd']);
	$router->get('/system/users/{id}/unitkerja',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'System\UsersController@usersunitkerja','as'=>'users.unitkerja']);
	$router->get('/system/users/{id}/roles',['uses'=>'System\UsersController@roles','as'=>'users.roles']);

	//setting - users bapelitbang
	$router->get('/system/usersbapelitbang',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@index','as'=>'usersbapelitbang.index']);
	$router->post('/system/usersbapelitbang/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@store','as'=>'usersbapelitbang.store']);
	$router->put('/system/usersbapelitbang/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@update','as'=>'usersbapelitbang.update']);    
	$router->delete('/system/usersbapelitbang/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@destroy','as'=>'usersbapelitbang.destroy']);

	//setting - users opd
	$router->post('/system/usersopd',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@index','as'=>'usersopd.index']);
	$router->post('/system/usersopd/salin',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@salin','as'=>'usersopd.salin']);
	$router->post('/system/usersopd/store',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@store','as'=>'usersopd.store']);
	$router->put('/system/usersopd/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@update','as'=>'usersopd.update']);    
	$router->delete('/system/usersopd/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@destroy','as'=>'usersopd.destroy']);
	
	//setting - users unit kerja
	$router->post('/system/usersunitkerja',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'System\UsersUnitKerjaController@index','as'=>'usersunitkerja.index']);	
	$router->post('/system/usersunitkerja/store',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'System\UsersUnitKerjaController@store','as'=>'usersunitkerja.store']);
	$router->put('/system/usersunitkerja/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'System\UsersUnitKerjaController@update','as'=>'usersunitkerja.update']);    
	$router->delete('/system/usersunitkerja/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'System\UsersUnitKerjaController@destroy','as'=>'usersunitkerja.destroy']);

	//setting - users pptk
	$router->get('/system/userspptk',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@index','as'=>'userspptk.index']);
	$router->post('/system/userspptk/store',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@store','as'=>'userspptk.store']);
	$router->put('/system/userspptk/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@update','as'=>'userspptk.update']);    
	$router->delete('/system/userspptk/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@destroy','as'=>'userspptk.destroy']);

	//setting - users dewan
	$router->get('/system/usersdewan',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersDewanController@index','as'=>'usersdewan.index']);
	$router->post('/system/usersdewan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersDewanController@store','as'=>'usersdewan.store']);
	$router->put('/system/usersdewan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersDewanController@update','as'=>'usersdewan.update']);    
	$router->delete('/system/usersdewan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersDewanController@destroy','as'=>'usersdewan.destroy']);

	//setting - users tapd
	$router->get('/system/userstapd',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersTAPDController@index','as'=>'userstapd.index']);
	$router->post('/system/userstapd/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersTAPDController@store','as'=>'userstapd.store']);
	$router->put('/system/userstapd/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersTAPDController@update','as'=>'userstapd.update']);    
	$router->delete('/system/userstapd/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersTAPDController@destroy','as'=>'userstapd.destroy']);

	//untuk ui admin
	$router->get('/system/setting/uiadmin',['uses'=>'System\UIController@admin','as'=>'ui.admin']);

});
