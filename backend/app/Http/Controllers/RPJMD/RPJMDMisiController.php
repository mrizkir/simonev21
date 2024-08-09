<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDVisiModel;
use App\Models\RPJMD\RPJMDMisiModel;

use Ramsey\Uuid\Uuid;

class RPJMDMisiController extends Controller 
{    
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {                
    $this->hasPermissionTo('RPJMD-MISI_BROWSE');
    
    $totalRecords = RPJMDMisiModel::count('RpjmdVisiID');
    
    $data = RPJMDMisiModel::select(\DB::raw('*'));
    
    if($request->filled('offset'))
    {
      $this->validate($request, [              
        'offset'=>'required|numeric',      
      ]);

      $offset = $request->input('offset');
      $data = $data->offset($offset);
    }

    if($request->filled('limit'))
    {
      $this->validate($request, [              
        'limit'=>'required|numeric|gt:0',   
      ]);

      $limit = $request->input('limit');
      $data = $data->limit($limit);
    }

    if($request->filled('sortBy'))
    {
      $sortBy = $request->input('sortBy');
      if(is_array($sortBy))
      {
        foreach ($sortBy as $item)
        {
          $data = $data->orderBy($item['key'], $item['order']);
        }
      }
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Visi berhasil diperoleh'
    ], 200);  
  }
  public function getkodemisi($id)
  {
    $Kd_PrioritasKab = RPJMDMisiModel::where('RpjmdVisiID',$id)->count('Kd_PrioritasKab')+1;
    return response()->json(['success'=>true,'Kd_PrioritasKab'=>$Kd_PrioritasKab],200);
  }  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {        
    $theme = \Auth::user()->theme;
    $daftar_visi = \App\Models\RPJMD\RPJMDVisiModel::select(\DB::raw('"RpjmdVisiID","Nm_RpjmdVisi"'))
                                ->get()
                                ->pluck('Nm_RpjmdVisi','RpjmdVisiID')
                                ->prepend('','')
                                ->toArray();
    
    return view("pages.$theme.rpjmd.rpjmdmisi.create")->with(['page_active'=>'rpjmdmisi',
                                  'daftar_visi'=>$daftar_visi,
                                ]);  
  }
  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-MISI_STORE');

    $this->validate($request, [      
      'RpjmdVisiID'=>'required|exists:tmRPJMDVisi,RpjmdVisiID',      
      'Kd_RpjmdMisi'=>'required',      
      'Nm_RpjmdMisi'=>'required',      
    ]);         

    $visi = RPJMDVisiModel::find($request->input('RpjmdVisiID'));

    $misi = RPJMDMisiModel::create([
      'RpjmdMisiID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $visi->PeriodeRPJMDID,
      'RpjmdVisiID' => $request->input('RpjmdVisiID'),      
      'Kd_RpjmdMisi' => $request->input('Kd_RpjmdMisi'),
      'Nm_RpjmdMisi' => $request->input('Nm_RpjmdMisi'),      
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $misi,                                    
      'message' => 'Data misi berhasil disimpan.'
    ], 200); 	
  }
  
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $theme = \Auth::user()->theme;

    $data = RPJMDMisiModel::findOrFail($id);
    if (!is_null($data) )  
    {
      
      return view("pages.$theme.rpjmd.rpjmdmisi.show")->with(['page_active'=>'rpjmdmisi',
                                  'data'=>$data,
                                ]);
    }        
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $theme = \Auth::user()->theme;
    
    $data = RPJMDMisiModel::findOrFail($id);
    if (!is_null($data) ) 
    {
      $daftar_visi = \App\Models\RPJMD\RPJMDVisiModel::select(\DB::raw('"RpjmdVisiID","Nm_RpjmdVisi"'))
                                ->get()
                                ->pluck('Nm_RpjmdVisi','RpjmdVisiID')
                                ->prepend('','')
                                ->toArray();
      return view("pages.$theme.rpjmd.rpjmdmisi.edit")->with(['page_active'=>'rpjmdmisi',
                                  'data'=>$data,
                                  'daftar_visi'=>$daftar_visi
                                ]);
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
    $rpjmdmisi = RPJMDMisiModel::find($id);
    $RpjmdVisiID=$request->input('RpjmdVisiID');
    $this->validate($request, [
      'Kd_PrioritasKab'=>[new IgnoreIfDataIsEqualValidation('tmPrioritasKab',$rpjmdmisi->Kd_PrioritasKab,['where'=>['RpjmdVisiID','=',$RpjmdVisiID]]),
            'required'
          ],
      'RpjmdVisiID'=>'required',
      'Nm_PrioritasKab'=>'required|min:2'
    ]);
    
    $rpjmdmisi->RpjmdVisiID = $RpjmdVisiID;
    $rpjmdmisi->Kd_PrioritasKab = $request->input('Kd_PrioritasKab');
    $rpjmdmisi->Nm_PrioritasKab = $request->input('Nm_PrioritasKab');
    $rpjmdmisi->Descr = $request->input('Descr');
    $rpjmdmisi->save();

    if ($request->ajax()) 
    {
      return response()->json([
        'success'=>true,
        'message'=>'Data ini telah berhasil diubah.'
      ]);
    }
    else
    {
      return redirect(route('rpjmdmisi.show',['uuid'=>$rpjmdmisi->PrioritasKabID]))->with('success',"Data dengan id ($id) telah berhasil diubah.");
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request,$id)
  {
    $this->hasPermissionTo('RPJMD-MISI_DESTROY');

    $misi = RPJMDMisiModel::find($id);

    if(is_null($misi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    // else if($visi->misi->count('RpjmdMisiID') > 0)
    // {
    //   return Response()->json([
    //     'status' => 0,
    //     'pid' => 'fetchdata',
    //     'message' => ["RPJMD Misi dengan dengan ($id) gagal dihapus karena masih terhubung ke Misi"]
    //   ], 422); 
    // }
    else
    {
      $misi->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Misi dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}