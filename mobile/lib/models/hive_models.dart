// import 'package:hive/hive.dart';

// Note: Untuk production, gunakan Hive adapters dengan build_runner
// Untuk sekarang, kita simpan data sebagai Map<String, dynamic> langsung

/// Hive Type IDs
/// Setiap model harus punya typeId unik (0-223)
/// (Tidak digunakan untuk sementara, akan digunakan setelah generate adapters)
class HiveTypeIds {
  static const int opd = 0;
  static const int unitKerja = 1;
  static const int urusan = 2;
  static const int bidangUrusan = 3;
  static const int program = 4;
  static const int kegiatan = 5;
  static const int subKegiatan = 6;
  static const int rekeningAkun = 7;
  static const int rekeningKelompok = 8;
  static const int rekeningJenis = 9;
  static const int rekeningObjek = 10;
  static const int rekeningRincianObjek = 11;
  static const int rekeningSubRincianObjek = 12;
  static const int asn = 13;
  static const int pejabat = 14;
  static const int sumberDana = 15;
  static const int tahunAnggaran = 16;
  static const int jenisPelaksanaan = 17;
  static const int jenisPembangunan = 18;
  static const int provinsi = 19;
  static const int kabupaten = 20;
  static const int kecamatan = 21;
  static const int desa = 22;
}

/// Hive Box Names
class HiveBoxNames {
  static const String opd = 'opd';
  static const String unitKerja = 'unitkerja';
  static const String urusan = 'urusan';
  static const String bidangUrusan = 'bidangurusan';
  static const String program = 'program';
  static const String kegiatan = 'kegiatan';
  static const String subKegiatan = 'subkegiatan';
  static const String rekeningAkun = 'rekeningakun';
  static const String rekeningKelompok = 'rekeningkelompok';
  static const String rekeningJenis = 'rekeningjenis';
  static const String rekeningObjek = 'rekeningobjek';
  static const String rekeningRincianObjek = 'rekeningrincianobjek';
  static const String rekeningSubRincianObjek = 'rekeningsubrincianobjek';
  static const String asn = 'asn';
  static const String pejabat = 'pejabat';
  static const String sumberDana = 'sumberdana';
  static const String tahunAnggaran = 'tahunanggaran';
  static const String jenisPelaksanaan = 'jenispelaksanaan';
  static const String jenisPembangunan = 'jenispembangunan';
  static const String provinsi = 'provinsi';
  static const String kabupaten = 'kabupaten';
  static const String kecamatan = 'kecamatan';
  static const String desa = 'desa';
  static const String syncMetadata = 'syncmetadata';
}

/// Model untuk menyimpan metadata sync
class SyncMetadata {
  final String endpoint;
  final String? tahun; // Untuk data yang filter by tahun
  final String? orgId; // Untuk data yang filter by OrgID
  final DateTime lastSync;
  final String? version; // Version dari data (untuk delta sync)

  SyncMetadata({
    required this.endpoint,
    this.tahun,
    this.orgId,
    required this.lastSync,
    this.version,
  });

  String get cacheKey {
    if (orgId != null) {
      return '${endpoint}_${tahun}_$orgId';
    } else if (tahun != null) {
      return '${endpoint}_$tahun';
    }
    return endpoint;
  }

  Map<String, dynamic> toMap() {
    return {
      'endpoint': endpoint,
      'tahun': tahun,
      'orgId': orgId,
      'lastSync': lastSync.toIso8601String(),
      'version': version,
    };
  }

  factory SyncMetadata.fromMap(Map<String, dynamic> map) {
    return SyncMetadata(
      endpoint: map['endpoint'] ?? '',
      tahun: map['tahun'],
      orgId: map['orgId'],
      lastSync: DateTime.parse(map['lastSync']),
      version: map['version'],
    );
  }
}

