<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserUnitKerja;
use App\Models\DMaster\OrganisasiModel;
use Spatie\Permission\Models\Role;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;

class UsersUnitKerjaController extends Controller {         
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{           
		$this->hasPermissionTo('SYSTEM-USERS-UNIT-KERJA_BROWSE');
		
		$this->validate($request, [        
			'TA' => 'required'
		]);    
		$ta = $request->input('TA');

		if ($this->hasRole('unitkerja')) 
		{
			$daftar_unitkerja = UserUnitKerja::select(\DB::raw('
				`SOrgID`									
			'))
			->where('ta', $ta)
			->where('user_id', $this->getUserid())
			->where('locked', 0)
			->get()
			->pluck('SOrgID');

			$data = User::where('default_role','unitkerja')
				->select(\DB::raw('
					users.*,
					"" AS unitkerja
				'))
				->join('usersunitkerja', 'usersunitkerja.user_id', 'users.id')
				->whereIn('SOrgID', $daftar_unitkerja)
				->orderBy('username', 'ASC')
				->get(); 

		}
		else
		{
			$data = User::where('default_role','unitkerja')
				->select(\DB::raw('
					users.*,
					"" AS unitkerja
				'))
				->join('usersunitkerja', 'usersunitkerja.user_id', 'users.id')
				->where('usersunitkerja.ta', $ta)
				->orderBy('username', 'ASC')
				->get();       			
		}           
		$role = Role::findByName('unitkerja');
		
		$data->transform(function ($item, $key) use ($ta) {
			$daftar_unitkerja = UserUnitKerja::select(\DB::raw('
				`SOrgID`,
				kode_sub_organisasi,
				`Nm_Sub_Organisasi`,
				locked
			'))
			->where('ta', $ta)
			->where('user_id', $item->id)
			->get();
							
			$item->unitkerja = $daftar_unitkerja;
			return $item;
		});

		return Response()->json([
			'status' => 1,
			'pid' => 'fetchdata',
			'role' => $role,
			'users' => $data,
			'message' => 'Fetch data users UNIT KERJA berhasil diperoleh'
		], 200);  
	}    
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->hasPermissionTo('SYSTEM-USERS-UNIT-KERJA_STORE');

		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|string|email|unique:users',
			'ta' => 'required',
			'nomor_hp' => 'required|string|unique:users',
			'username' => 'required|string|unique:users',
			'password' => 'required',            
			'org_id' => 'required',
			'sorg_id' => 'required',
		]);
		$daftar_unitkerja=json_decode($request->input('sorg_id'), true);
		if (count($daftar_unitkerja) > 0)
    {
			$user = \DB::transaction(function () use ($request) {
				$now = \Carbon\Carbon::now()->toDateTimeString();        
				$user=User::create([
					'id'=>Uuid::uuid4()->toString(),
					'name' => $request->input('name'),
					'email' => $request->input('email'),
					'ta' => $request->input('ta'),
					'nomor_hp' => $request->input('nomor_hp'),
					'username'=> $request->input('username'),
					'password'=>Hash::make($request->input('password')),                        
					'theme' => 'default',
					'default_role' => 'unitkerja',            
					'foto'=> 'storages/images/users/no_photo.png',
					'created_at' => $now, 
					'updated_at' => $now
				]);            
				$role='unitkerja';   
				$user->assignRole($role);               
				
				$permission=Role::findByName('unitkerja')->permissions;
				$permissions = $permission->pluck('name');
				$user->givePermissionTo($permissions);

				$user_id = $user->id;
				$org_id = $request->input('org_id');
				$daftar_unitkerja=json_decode($request->input('sorg_id'), true);
				$organisasi = OrganisasiModel::find($org_id);
				foreach($daftar_unitkerja as $v)
				{
					$uuid=Uuid::uuid4()->toString();
					$sql = "
						INSERT INTO usersunitkerja ( 
							id,  
							user_id, 
							`OrgID`,
							`SOrgID`,

							kode_organisasi,
							`Nm_Organisasi`,
							`Alias_Organisasi`,

							kode_sub_organisasi,
							`Nm_Sub_Organisasi`,
							`Alias_Sub_Organisasi`,

							ta,
							created_at, 
							updated_at
						) 
						SELECT
							'$uuid',
							'$user_id',                    
							`tmOrg`.`OrgID`,
							`tmSOrg`.`SOrgID`,
							
							`tmOrg`.kode_organisasi,
							`tmOrg`.`Nm_Organisasi`,
							`tmOrg`.`Alias_Organisasi`,

							`tmSOrg`.kode_sub_organisasi,
							`tmSOrg`.`Nm_Sub_Organisasi`,
							`tmSOrg`.`Alias_Sub_Organisasi`,

							`tmSOrg`.`TA`,                        
							NOW() AS created_at,
							NOW() AS updated_at
						FROM `tmSOrg`
						JOIN `tmOrg` ON `tmOrg`.`OrgID`=`tmSOrg`.`OrgID`
						WHERE 
							`SOrgID`='$v' 
					";

					\DB::statement($sql); 
				}

				\App\Models\System\ActivityLog::log($request,[
					'object' => $this->guard()->user(), 
					'object_id' => $this->guard()->user()->id, 
					'user_id' => $this->getUserid(), 
					'message' => 'Menambah user Unit Kerja ('.$user->username.') berhasil'
				]);

				return $user;
			});

			return Response()->json([
				'status' => 1,
				'pid' => 'store',
				'user' => $user,                                    
				'message' => 'Data user UNIT KERJA berhasil disimpan.'
			], 200); 
		}
		else
		{
			return Response()->json([
        'status' => 0,
        'pid' => 'store',                                           
        'message' => 'Data user UNIT KERJA gagal disimpan karena Jumlah Unit Kerja 0.'
      ], 422);
		}
	}
	/**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function salin(Request $request)
  {       
    $this->hasPermissionTo('SYSTEM-USERS-UNIT-KERJA_STORE');
    
    $this->validate($request, [            
      'tahun_asal' => 'required|numeric',
      'tahun_tujuan' => 'required|numeric|gt:tahun_asal',
    ]);

    $tahun_asal = $request->input('tahun_asal');
    $tahun_tujuan = $request->input('tahun_tujuan');

    if (($tahun_tujuan - $tahun_asal) > 1)
    {
      $tahun_dif = $tahun_tujuan - 1;
      return Response()->json([
        'status' => 0,
        'pid' => 'store',
        'message'=>"Salin relasi user ke UNIT KERJA dari tahun anggaran $tahun_asal gagal. Harus dari tahun $tahun_dif."
      ], 422);
    }
    else
    {
      \DB::beginTransaction();

      \DB::table('usersunitkerja')
      ->where('TA', $tahun_tujuan)		
      ->delete();

      $str_insert = '
        INSERT INTO `usersunitkerja` (
          id, 
          user_id, 
          OrgID,
          SOrgID,
          kode_organisasi,
          Nm_Organisasi,
          Alias_Organisasi,
          kode_sub_organisasi,
          Nm_Sub_Organisasi,
          Alias_Sub_Organisasi,
          ta,
          locked,
          created_at,
          updated_at
        )		
        SELECT
          uuid() AS id,        
          user_id, 
          t2.OrgID,
          t2.SOrgID,
          t3.kode_organisasi,
          t3.Nm_Organisasi,
          t3.Alias_Organisasi,
          t2.kode_sub_organisasi,
          t2.Nm_Sub_Organisasi,
          t2.Alias_Sub_Organisasi,
          '.$tahun_tujuan.' AS `ta`,
          0 AS locked,								
          NOW() AS created_at,
          NOW() AS updated_at
        FROM usersunitkerja t1
        JOIN `tmSOrg` t2 ON t1.`SOrgID`=t2.`SOrgID_Src`
        JOIN `tmOrg` t3 ON t2.`OrgID`=t3.`OrgID`
        WHERE t1.`ta`='.$tahun_asal.'      
      ';    
    
      \DB::statement($str_insert);     
    
      \DB::commit();

      return Response()->json([
        'status' => 1,
        'pid' => 'store',            
        'message'=>"Salin relasi user ke UNIT KERJA dari tahun anggaran $tahun_asal berhasil."
      ], 200);
    }
  }
	/**
	 * digunakan untuk mendapatkan informasi detail user dengan role program studi
	 */
	public function show(Request $request, $id)
	{
		$this->hasPermissionTo('SYSTEM-USERS-UNIT-KERJA_SHOW');

		$user = User::find($id);
		if (is_null($user))
		{
			return Response()->json([
				'status' => 0,
				'pid' => 'update',                
				'message'=>["User ID ($id) gagal diperoleh"]
			], 422); 
		}
		else
		{
			return Response()->json([
				'status' => 1,
				'pid' => 'fetchdata',
				'user' => $user,  
				'role_unitkerja' => $user->hasRole('unitkerja'),    
				'message' => 'Data user '.$user->username.' berhasil diperoleh.'
			], 200); 
		}

	}
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$this->hasPermissionTo('SYSTEM-USERS-UNIT-KERJA_UPDATE');

		$user = User::find($id);
		if (is_null($user))
		{
			return Response()->json([
				'status' => 0,
				'pid' => 'update',                
				'message'=>["User ID ($id) gagal diupdate"]
			], 422); 
		}
		else
		{
			$this->validate($request, [
				'username'=>[
								'required',
								'unique:users,username,'.$user->id
							],           
				'name' => 'required',            
				'email' => 'required|string|email|unique:users,email,'.$user->id,
				'nomor_hp' => 'required|string|unique:users,nomor_hp,'.$user->id,   
				'org_id' => 'required',           
			]); 

			$daftar_unitkerja=json_decode($request->input('sorg_id'), true);
			if (count($daftar_unitkerja) > 0)
			{
				$user = \DB::transaction(function () use ($request,$user) {
					$user->name = $request->input('name');
					$user->email = $request->input('email');
					$user->nomor_hp = $request->input('nomor_hp');
					$user->username = $request->input('username');        
					if (!empty(trim($request->input('password')))) {
						$user->password = Hash::make($request->input('password'));
					}    
					$user->updated_at = \Carbon\Carbon::now()->toDateTimeString();
					$user->save();

					$user_id = $user->id;
					$org_id = $request->input('org_id');
					\DB::table('usersunitkerja')->where('user_id', $user_id)->delete();
					$daftar_unitkerja=json_decode($request->input('sorg_id'),true);
					$organisasi = OrganisasiModel::find($org_id);
					foreach($daftar_unitkerja as $v)
					{
						$uuid=Uuid::uuid4()->toString();
						$sql = "
							INSERT INTO usersunitkerja ( 
								id,  
								user_id, 
								`OrgID`,
								`SOrgID`,

								kode_organisasi,
								`Nm_Organisasi`,
								`Alias_Organisasi`,

								kode_sub_organisasi,
								`Nm_Sub_Organisasi`,
								`Alias_Sub_Organisasi`,

								ta,
								created_at, 
								updated_at
							) 
							SELECT
								'$uuid',
								'$user_id',                    
								`tmOrg`.`OrgID`,
								`tmSOrg`.`SOrgID`,
								
								`tmOrg`.kode_organisasi,
								`tmOrg`.`Nm_Organisasi`,
								`tmOrg`.`Alias_Organisasi`,

								`tmSOrg`.kode_sub_organisasi,
								`tmSOrg`.`Nm_Sub_Organisasi`,
								`tmSOrg`.`Alias_Sub_Organisasi`,

								`tmSOrg`.`TA`,                        
								NOW() AS created_at,
								NOW() AS updated_at
							FROM `tmSOrg`
							JOIN `tmOrg` ON `tmOrg`.`OrgID`=`tmSOrg`.`OrgID`
							WHERE 
								`SOrgID`='$v' 
						";

						\DB::statement($sql); 
					}

					\App\Models\System\ActivityLog::log($request,[
						'object' => $this->guard()->user(), 
						'object_id' => $this->guard()->user()->id, 
						'user_id' => $this->getUserid(), 
						'message' => 'Mengubah data user UNIT KERJA ('.$user->username.') berhasil'
					]);
					return $user;
				});

				return Response()->json([
					'status' => 1,
					'pid' => 'update',
					'user' => $user,      
					'message' => 'Data user UNIT KERJA '.$user->username.' berhasil diubah.'
				], 200); 
			}
			else
			{
				return Response()->json([
					'status' => 0,
					'pid' => 'store',                                           
					'message' => 'Data user UNIT KERJA gagal disimpan karena Jumlah Unit Kerja 0.'
				], 422);
			}
		}
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id)
	{ 
		$this->hasPermissionTo('SYSTEM-USERS-UNIT-KERJA_DESTROY');

		$user = User::where('isdeleted','1')
					->find($id); 
		
		if (is_null($user))
		{
			return Response()->json([
				'status' => 0,
				'pid' => 'destroy',                
				'message'=>["User ID ($id) gagal dihapus"]
			], 422); 
		}
		else
		{
			$username = $user->username;
			$user->delete();

			\App\Models\System\ActivityLog::log($request,[
				'object' => $this->guard()->user(), 
				'object_id' => $this->guard()->user()->id, 
				'user_id' => $this->getUserid(), 
				'message' => 'Menghapus user UNIT KERJA ('.$username.') berhasil'
			]);
		
			return Response()->json([
				'status' => 1,
				'pid' => 'destroy',                
				'message'=>"User UNIT KERJA ($username) berhasil dihapus"
			], 200);         
		}
				  
	}
}