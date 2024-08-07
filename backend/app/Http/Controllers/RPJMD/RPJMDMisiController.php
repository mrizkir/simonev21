<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDMisiModel;

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
      'PeriodeRPJMDID'=>'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',
      'Kd_RpjmdMisi'=>'required',      
      'Nm_RpjmdVisi'=>'required',      
    ]);     
    
    $misi = RPJMDMisiModel::create([
      'RpjmdVisiID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),
      'RpjmdVisiID' => $request->input('RpjmdVisiID'),      
      'Kd_RpjmdMisi' => $request->input('Kd_RpjmdMisi'),
      'Nm_RpjmdVisi' => $request->input('Nm_RpjmdVisi'),      
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $rpjmdvisi,                                    
      'message' => 'Data visi berhasil disimpan.'
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
    $theme = \Auth::user()->theme;
    
    $rpjmdmisi = RPJMDMisiModel::find($id);
    $result=$rpjmdmisi->delete();
    if ($request->ajax()) 
    {
      $currentpage=$this->getCurrentPageInsideSession('rpjmdmisi'); 
      $data=$this->populateData($currentpage);
      if ($currentpage > $data->lastPage())
      {            
        $data = $this->populateData($data->lastPage());
      }
      $datatable = view("pages.$theme.rpjmd.rpjmdmisi.datatable")->with(['page_active'=>'rpjmdmisi',
                              'search'=>$this->getControllerStateSession('rpjmdmisi','search'),
                              'numberRecordPerPage'=>$this->getControllerStateSession('global_controller','numberRecordPerPage'),                                                                    
                              'column_order'=>$this->getControllerStateSession('rpjmdmisi.orderby','column_name'),
                              'direction'=>$this->getControllerStateSession('rpjmdmisi.orderby','order'),
                              'data'=>$data])->render();      
      
      return response()->json(['success'=>true,'datatable'=>$datatable],200); 
    }
    else
    {
      return redirect(route('rpjmdmisi.index'))->with('success',"Data ini dengan ($id) telah berhasil dihapus.");
    }        
  }
}