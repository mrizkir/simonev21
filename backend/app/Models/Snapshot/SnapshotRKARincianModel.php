<?php

namespace App\Models\Snapshot;

use Illuminate\Database\Eloquent\Model;

class SnapshotRKARincianModel extends Model 
{
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'trSnapshotRKARinc';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RKARincID',
    'RKAID',
    'SIPDID',
    'JenisPelaksanaanID',
    'SumberDanaID',
    'JenisPembangunanID',            
    'kode_uraian1',            
    'kode_uraian2',            
    'NamaUraian1',            
    'NamaUraian2',            
    'volume1',
    'volume2',
    'satuan1',            
    'satuan2',            
    'harga_satuan1',
    'harga_satuan2',
    'PaguUraian1',
    'PaguUraian2',
    'idlok',
    'ket_lok',
    'rw',
    'rt',
    'nama_perusahaan',
    'alamat_perusahaan',
    'no_telepon',
    'nama_direktur',
    'npwp',
    'no_kontrak',
    'tgl_kontrak',
    'tgl_mulai_pelaksanaan',
    'tgl_selesai_pelaksanaan',
    'status_lelang',
    'EntryLvl',
    'Descr',            
    'TA',
    'TABULAN',
    'Locked',
    'RKARincID_Src',                    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RKARincID';
  /**
   * enable auto_increment.
   *
   * @var string
   */
  public $incrementing = false;
  /**
   * activated timestamps.
   *
   * @var string
   */
  public $timestamps = true;

}
