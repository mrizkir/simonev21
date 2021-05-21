<?php
$router->get('/', function () use ($router) {
    return 'SIMONEV PERMENDARI 98 API';
});

$router->group(['prefix'=>'v1'], function () use ($router)
{
    //dashboard
    $router->post('/dashboard/front',['uses'=>'DashboardController@indexfront','as'=>'dashboard.indexfront']);

    //dmaster - provinsi
    $router->get('/datamaster/provinsi',['uses'=>'DMaster\ProvinsiController@index','as'=>'provinsi.index']);
    $router->get('/datamaster/provinsi/{id}/kabupaten',['uses'=>'DMaster\ProvinsiController@kabupaten','as'=>'provinsi.kabupaten']);

    //dmaster - kabupaten
    $router->get('/datamaster/kabupaten',['uses'=>'DMaster\KabupatenController@index','as'=>'kabupaten.index']);
    $router->get('/datamaster/kabupaten/{id}/kecamatan',['uses'=>'DMaster\KabupatenController@kecamatan','as'=>'kabupaten.kecamatan']);

    //dmaster - kecamatan
    $router->get('/datamaster/kecamatan',['uses'=>'DMaster\KecamatanController@index','as'=>'kecamatan.index']);
    $router->get('/datamaster/kecamatan/{id}/desa',['uses'=>'DMaster\KecamatanController@desa','as'=>'kecamatan.desa']);

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
    $router->post('/dmaster/kodefikasi/urusan',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\KodefikasiUrusanController@index','as'=>'kodefikasi-urusan.index']);    
    $router->post('/dmaster/kodefikasi/urusan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiUrusanController@store','as'=>'kodefikasi-urusan.store']);
    $router->put('/dmaster/kodefikasi/urusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiUrusanController@update','as'=>'kodefikasi-urusan.update']);
    $router->delete('/dmaster/kodefikasi/urusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiUrusanController@destroy','as'=>'kodefikasi-urusan.destroy']);
    
    //data master - kodefikasi - bidang urusan
    $router->post('/dmaster/kodefikasi/bidangurusan',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\KodefikasiBidangUrusanController@index','as'=>'kodefikasi-bidang-urusan.index']);    
    $router->post('/dmaster/kodefikasi/bidangurusan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiBidangUrusanController@store','as'=>'kodefikasi-bidang-urusan.store']);
    $router->put('/dmaster/kodefikasi/bidangurusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiBidangUrusanController@update','as'=>'kodefikasi-bidang-urusan.update']);
    $router->delete('/dmaster/kodefikasi/bidangurusan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiBidangUrusanController@destroy','as'=>'kodefikasi-bidang-urusan.destroy']);
    
    //data master - kodefikasi - program
    $router->post('/dmaster/kodefikasi/program',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\KodefikasiProgramController@index','as'=>'kodefikasi-program.index']);    
    $router->post('/dmaster/kodefikasi/program/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiProgramController@store','as'=>'kodefikasi-program.store']);
    $router->put('/dmaster/kodefikasi/program/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiProgramController@update','as'=>'kodefikasi-program.update']);
    $router->delete('/dmaster/kodefikasi/program/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiProgramController@destroy','as'=>'kodefikasi-program.destroy']);
    
    //data master - kodefikasi - kegiatan
    $router->post('/dmaster/kodefikasi/kegiatan',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\KodefikasiKegiatanController@index','as'=>'kodefikasi-kegiatan.index']);    
    $router->post('/dmaster/kodefikasi/kegiatan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiKegiatanController@store','as'=>'kodefikasi-kegiatan.store']);
    $router->put('/dmaster/kodefikasi/kegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiKegiatanController@update','as'=>'kodefikasi-kegiatan.update']);
    $router->delete('/dmaster/kodefikasi/kegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiKegiatanController@destroy','as'=>'kodefikasi-kegiatan.destroy']);
    
    //data master - kodefikasi - sub kegiatan
    $router->post('/dmaster/kodefikasi/subkegiatan',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\KodefikasiSubKegiatanController@index','as'=>'kodefikasi-subkegiatan.index']);    
    $router->post('/dmaster/kodefikasi/subkegiatan/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiSubKegiatanController@store','as'=>'kodefikasi-subkegiatan.store']);
    $router->put('/dmaster/kodefikasi/subkegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiSubKegiatanController@update','as'=>'kodefikasi-subkegiatan.update']);
    $router->delete('/dmaster/kodefikasi/subkegiatan/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\KodefikasiSubKegiatanController@destroy','as'=>'kodefikasi-subkegiatan.destroy']);

    //data master - opd
    $router->post('/dmaster/opd',['uses'=>'DMaster\OrganisasiController@index','as'=>'opd.index']);    
    $router->post('/dmaster/opd/loadopd',['middleware'=>['role:superadmin'],'uses'=>'DMaster\OrganisasiController@loadopd','as'=>'opd.loadopd']);    
    $router->post('/dmaster/opd/loadpaguapbdp',['middleware'=>['role:superadmin'],'uses'=>'DMaster\OrganisasiController@loadpaguapbdp','as'=>'opd.loadpaguapbdp']);    
    $router->post('/dmaster/opd/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\OrganisasiController@store','as'=>'opd.store']);    
    $router->put('/dmaster/opd/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\OrganisasiController@update','as'=>'opd.update']);
    $router->get('/dmaster/opd/{id}/unitkerja',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\OrganisasiController@opdunitkerja','as'=>'opd.unitkerja']);
    $router->get('/dmaster/opd/{id}/pejabat',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\OrganisasiController@pejabatopd','as'=>'opd.pejabatopd']);
    $router->delete('/dmaster/opd/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\OrganisasiController@destroy','as'=>'opd.destroy']);
    
    //data master - unit kerja
    $router->post('/dmaster/unitkerja',['uses'=>'DMaster\SubOrganisasiController@index','as'=>'unitkerja.index']);    
    $router->post('/dmaster/unitkerja/loadunitkerja',['middleware'=>['role:superadmin'],'uses'=>'DMaster\SubOrganisasiController@loadunitkerja','as'=>'unitkerja.loadunitkerja']);    
    $router->post('/dmaster/unitkerja/loadpaguapbdp',['middleware'=>['role:superadmin'],'uses'=>'DMaster\SubOrganisasiController@loadpaguapbdp','as'=>'unitkerja.loadpaguapbdp']);    
    $router->post('/dmaster/unitkerja/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\SubOrganisasiController@store','as'=>'unitkerja.store']);    
    $router->put('/dmaster/unitkerja/{id}',['middleware'=>['role:superadmin|bapelitbang|opd|unitkerja'],'uses'=>'DMaster\SubOrganisasiController@update','as'=>'unitkerja.update']);
    $router->delete('/dmaster/unitkerja/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\SubOrganisasiController@destroy','as'=>'unitkerja.destroy']);

    //data master - ASN
    $router->post('/dmaster/asn',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\ASNController@index','as'=>'ASN.index']);    
    $router->post('/dmaster/asn/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\ASNController@store','as'=>'ASN.store']);
    $router->put('/dmaster/asn/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\ASNController@update','as'=>'ASN.update']);
    $router->delete('/dmaster/asn/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\ASNController@destroy','as'=>'ASN.destroy']);    
    
    //data master - Pejabat
    $router->post('/dmaster/pejabat',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'DMaster\PejabatController@index','as'=>'pejabat.index']);    
    $router->post('/dmaster/pejabat/store',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\PejabatController@store','as'=>'pejabat.store']);
    $router->put('/dmaster/pejabat/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\PejabatController@update','as'=>'pejabat.update']);
    $router->delete('/dmaster/pejabat/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'DMaster\PejabatController@destroy','as'=>'pejabat.destroy']);    
    
    //data master - Tahun Anggaran          
    $router->post('/dmaster/ta/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\TAController@store','as'=>'ta.store']);
    $router->put('/dmaster/ta/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\TAController@update','as'=>'ta.update']);
    $router->delete('/dmaster/ta/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'DMaster\TAController@destroy','as'=>'ta.destroy']); 

    //renja murni
    $router->post('/renjamurni',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\RenjaMurniController@index','as'=>'renjamurni.index']);    
    $router->post('/renjamurni/reloadstatistik1',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\RenjaMurniController@reloadstatistik1','as'=>'renjamurni.reloadstatistik1']);    
    $router->post('/renjamurni/reloadstatistik2',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\RenjaMurniController@reloadstatistik2','as'=>'renjamurni.reloadstatistik2']);    
    
    $router->post('/renjamurni/statistik/peringkatopd',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\PeringkatOPDMurniController@index','as'=>'renjamurni-peringkatopdmurni.index']);    
    
    //renja perubahan
    $router->post('/renjaperubahan',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\RenjaPerubahanController@index','as'=>'renjaperubahan.index']);    
    $router->post('/renjaperubahan/reloadstatistik1',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\RenjaPerubahanController@reloadstatistik1','as'=>'renjaperubahan.reloadstatistik1']);    
    $router->post('/renjaperubahan/reloadstatistik2',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\RenjaPerubahanController@reloadstatistik2','as'=>'renjaperubahan.reloadstatistik2']);    

    $router->post('/renjaperubahan/statistik/peringkatopd',['middleware'=>['role:superadmin|bapelitbang|opd|pptk'],'uses'=>'Renja\PeringkatOPDPerubahanController@index','as'=>'renjaperubahan-peringkatopdperubahan.index']);    

    //setting - permissions
    $router->get('/system/setting/permissions',['middleware'=>['role:superadmin|akademik|pmb'],'uses'=>'System\PermissionsController@index','as'=>'permissions.index']);
    $router->post('/system/setting/permissions/store',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@store','as'=>'permissions.store']);
    $router->delete('/system/setting/permissions/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@destroy','as'=>'permissions.destroy']);

    //setting - roles
    $router->get('/system/setting/roles',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@index','as'=>'roles.index']);
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
    $router->get('/system/users/{id}/roles',['uses'=>'System\UsersController@roles','as'=>'users.roles']);

    //setting - users bapelitbang
    $router->get('/system/usersbapelitbang',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@index','as'=>'usersbapelitbang.index']);
    $router->post('/system/usersbapelitbang/store',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@store','as'=>'usersbapelitbang.store']);
    $router->put('/system/usersbapelitbang/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@update','as'=>'usersbapelitbang.update']);
    $router->put('/system/usersbapelitbang/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@update','as'=>'usersbapelitbang.update']);
    $router->delete('/system/usersbapelitbang/{id}',['middleware'=>['role:superadmin|bapelitbang'],'uses'=>'System\UsersBapelitbangController@destroy','as'=>'usersbapelitbang.destroy']);    
    
    //setting - users opd
    $router->post('/system/usersopd',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@index','as'=>'usersopd.index']);
    $router->post('/system/usersopd/store',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@store','as'=>'usersopd.store']);
    $router->put('/system/usersopd/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@update','as'=>'usersopd.update']);
    $router->put('/system/usersopd/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@update','as'=>'usersopd.update']);
    $router->delete('/system/usersopd/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersOPDController@destroy','as'=>'usersopd.destroy']);    

    //setting - users pptk
    $router->get('/system/userspptk',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@index','as'=>'userspptk.index']);
    $router->post('/system/userspptk/store',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@store','as'=>'userspptk.store']);
    $router->put('/system/userspptk/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@update','as'=>'userspptk.update']);
    $router->put('/system/userspptk/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@update','as'=>'userspptk.update']);
    $router->delete('/system/userspptk/{id}',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersPPTKController@destroy','as'=>'userspptk.destroy']);    
    
    //untuk ui admin
    $router->get('/system/setting/uiadmin',['uses'=>'System\UIController@admin','as'=>'ui.admin']);

});