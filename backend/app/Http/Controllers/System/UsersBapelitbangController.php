<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;

class UsersBapelitbangController extends Controller {         
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {           
        $this->hasPermissionTo('SYSTEM-USERS-BAPELITBANG_BROWSE');
        $data = User::where('default_role', 'bapelitbang')
                    ->orderBy('username', 'ASC')
                    ->get();       
                    
        $role = Role::findByName('bapelitbang');
        return Response()->json([
                                'status' => 1,
                                'pid' => 'fetchdata',
                                'role' => $role,
                                'users' => $data,
                                'message' => 'Fetch data users BAPPELITBANG berhasil diperoleh'
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
        $this->hasPermissionTo('SYSTEM-USERS-BAPELITBANG_STORE');
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'nomor_hp' => 'required|string|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required',
        ]);
        $user = \DB::transaction(function () use ($request) {
            $now = \Carbon\Carbon::now()->toDateTimeString();        
            $user=User::create([
                'id'=>Uuid::uuid4()->toString(),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'nomor_hp' => $request->input('nomor_hp'),
                'username'=> $request->input('username'),
                'password'=>Hash::make($request->input('password')),                        
                'theme' => 'default',
                'default_role' => 'bapelitbang',            
                'foto'=> 'storages/images/users/no_photo.png',
                'created_at' => $now, 
                'updated_at' => $now
            ]);            
            $role='bapelitbang';   
            $user->assignRole($role);               
            
            $permission=Role::findByName('bapelitbang')->permissions;
            $permissions = $permission->pluck('name');
            $user->givePermissionTo($permissions);

            $daftar_roles=json_decode($request->input('role_id'),true);
            foreach($daftar_roles as $v)
            {
                if ($v=='opd' || $v=='pptk' )
                {
                    $user->assignRole($v);               
                    $permission=Role::findByName($v)->permissions;
                    $permissions = $permission->pluck('name');
                    $user->givePermissionTo($permissions);
                }
            }

            \App\Models\System\ActivityLog::log($request,[
                                            'object' => $this->guard()->user(), 
                                            'object_id' => $this->guard()->user()->id, 
                                            'user_id' => $this->getUserid(), 
                                            'message' => 'Menambah user BAPPELITBANG('.$user->username.') berhasil'
                                        ]);

            return $user;
        });

        return Response()->json([
                                    'status' => 1,
                                    'pid' => 'store',
                                    'user' => $user,                                    
                                    'message' => 'Data user BAPPELITBANG berhasil disimpan.'
                                ], 200); 

    }
    /**
     * digunakan untuk mendapatkan informasi detail user dengan role program studi
     */
    public function show(Request $request, $id)
    {
        $this->hasPermissionTo('SYSTEM-USERS-BAPELITBANG_SHOW');

        $user = User::find($id);
        if (is_null($user))
        {
            return Response()->json([
                                    'status' => 0,
                                    'pid' => 'update',                
                                    'message' => ["User ID ($id) gagal diperoleh"]
                                ], 422); 
        }
        else
        {
            return Response()->json([
                                    'status' => 1,
                                    'pid' => 'fetchdata',
                                    'user' => $user,  
                                    'role_bapelitbang' => $user->hasRole('bapelitbang'),    
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
        $this->hasPermissionTo('SYSTEM-USERS-BAPELITBANG_UPDATE');

        $user = User::find($id);
        if (is_null($user))
        {
            return Response()->json([
                                    'status' => 0,
                                    'pid' => 'update',                
                                    'message' => ["User ID ($id) gagal diupdate"]
                                ], 422); 
        }
        else
        {
            $this->validate($request, [
                                        'username' => [
                                                        'required',
                                                        'unique:users,username,'.$user->id
                                                    ],           
                                        'name' => 'required',            
                                        'email' => 'required|string|email|unique:users,email,'.$user->id,
                                        'nomor_hp' => 'required|string|unique:users,nomor_hp,'.$user->id,                                                    
                                    ]); 
            $user = \DB::transaction(function () use ($request, $user) {
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->nomor_hp = $request->input('nomor_hp');
                $user->username = $request->input('username');        
                if (!empty(trim($request->input('password')))) {
                    $user->password = Hash::make($request->input('password'));
                }    
                $user->updated_at = \Carbon\Carbon::now()->toDateTimeString();
                $user->save();

                \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $this->guard()->user(), 
                                                            'object_id' => $this->guard()->user()->id, 
                                                            'user_id' => $this->getUserid(), 
                                                            'message' => 'Mengubah data user BAPPELITBANG ('.$user->username.') berhasil'
                                                        ]);
                return $user;
            });

            return Response()->json([
                                    'status' => 1,
                                    'pid' => 'update',
                                    'user' => $user,      
                                    'message' => 'Data user BAPPELITBANG '.$user->username.' berhasil diubah.'
                                ], 200); 
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
        $this->hasPermissionTo('SYSTEM-USERS-BAPELITBANG_DESTROY');

        $user = User::where('isdeleted', '1')
                    ->find($id); 
        
        if (is_null($user))
        {
            return Response()->json([
                                    'status' => 0,
                                    'pid' => 'destroy',                
                                    'message' => ["User ID ($id) gagal dihapus"]
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
                                                                'message' => 'Menghapus user BAPPELITBANG ('.$username.') berhasil'
                                                            ]);
        
            return Response()->json([
                                        'status' => 1,
                                        'pid' => 'destroy',                
                                        'message'=>"User BAPPELITBANG ($username) berhasil dihapus"
                                    ], 200);         
        }
                  
    }
}