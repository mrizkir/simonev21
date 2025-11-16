class OPDModel {
  final String OrgID;
  final String Nm_Organisasi;
  final String? kode_organisasi;
  final String? BidangID_1;
  final String? kode_bidang_1;
  final String? Nm_Bidang_1;
  final String? BidangID_2;
  final String? kode_bidang_2;
  final String? Nm_Bidang_2;
  final String? BidangID_3;
  final String? kode_bidang_3;
  final String? Nm_Bidang_3;

  OPDModel({
    required this.OrgID,
    required this.Nm_Organisasi,
    this.kode_organisasi,
    this.BidangID_1,
    this.kode_bidang_1,
    this.Nm_Bidang_1,
    this.BidangID_2,
    this.kode_bidang_2,
    this.Nm_Bidang_2,
    this.BidangID_3,
    this.kode_bidang_3,
    this.Nm_Bidang_3,
  });

  factory OPDModel.fromJson(Map<String, dynamic> json) {
    return OPDModel(
      OrgID: json['OrgID']?.toString() ?? '',
      Nm_Organisasi: json['Nm_Organisasi']?.toString() ?? '',
      kode_organisasi: json['kode_organisasi']?.toString(),
      BidangID_1: json['BidangID_1']?.toString(),
      kode_bidang_1: json['kode_bidang_1']?.toString(),
      Nm_Bidang_1: json['Nm_Bidang_1']?.toString(),
      BidangID_2: json['BidangID_2']?.toString(),
      kode_bidang_2: json['kode_bidang_2']?.toString(),
      Nm_Bidang_2: json['Nm_Bidang_2']?.toString(),
      BidangID_3: json['BidangID_3']?.toString(),
      kode_bidang_3: json['kode_bidang_3']?.toString(),
      Nm_Bidang_3: json['Nm_Bidang_3']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'OrgID': OrgID,
      'Nm_Organisasi': Nm_Organisasi,
      'kode_organisasi': kode_organisasi,
      'BidangID_1': BidangID_1,
      'kode_bidang_1': kode_bidang_1,
      'Nm_Bidang_1': Nm_Bidang_1,
      'BidangID_2': BidangID_2,
      'kode_bidang_2': kode_bidang_2,
      'Nm_Bidang_2': Nm_Bidang_2,
      'BidangID_3': BidangID_3,
      'kode_bidang_3': kode_bidang_3,
      'Nm_Bidang_3': Nm_Bidang_3,
    };
  }
}

class UnitKerjaModel {
  final String SOrgID;
  final String Nm_Sub_Organisasi;
  final String? kode_sub_organisasi;
  final double? PaguDana1;
  final double? RealisasiKeuangan1;
  final double? RealisasiFisik1;

  UnitKerjaModel({
    required this.SOrgID,
    required this.Nm_Sub_Organisasi,
    this.kode_sub_organisasi,
    this.PaguDana1,
    this.RealisasiKeuangan1,
    this.RealisasiFisik1,
  });

  factory UnitKerjaModel.fromJson(Map<String, dynamic> json) {
    return UnitKerjaModel(
      SOrgID: json['SOrgID']?.toString() ?? '',
      Nm_Sub_Organisasi: json['Nm_Sub_Organisasi']?.toString() ?? '',
      kode_sub_organisasi: json['kode_sub_organisasi']?.toString(),
      PaguDana1: (json['PaguDana1'] ?? 0).toDouble(),
      RealisasiKeuangan1: (json['RealisasiKeuangan1'] ?? 0).toDouble(),
      RealisasiFisik1: (json['RealisasiFisik1'] ?? 0).toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'SOrgID': SOrgID,
      'Nm_Sub_Organisasi': Nm_Sub_Organisasi,
      'kode_sub_organisasi': kode_sub_organisasi,
      'PaguDana1': PaguDana1,
      'RealisasiKeuangan1': RealisasiKeuangan1,
      'RealisasiFisik1': RealisasiFisik1,
    };
  }
}

class RKAItem {
  final String RKAID;
  final String kode_sub_kegiatan;
  final String Nm_Sub_Kegiatan;
  final double PaguDana1;
  final double RealisasiFisik1;
  final double RealisasiKeuangan1;
  final double persen_keuangan1;
  final int Locked;
  final String? created_at;
  final String? updated_at;

  RKAItem({
    required this.RKAID,
    required this.kode_sub_kegiatan,
    required this.Nm_Sub_Kegiatan,
    required this.PaguDana1,
    required this.RealisasiFisik1,
    required this.RealisasiKeuangan1,
    required this.persen_keuangan1,
    required this.Locked,
    this.created_at,
    this.updated_at,
  });

  factory RKAItem.fromJson(Map<String, dynamic> json) {
    return RKAItem(
      RKAID: json['RKAID']?.toString() ?? '',
      kode_sub_kegiatan: json['kode_sub_kegiatan']?.toString() ?? '',
      Nm_Sub_Kegiatan: json['Nm_Sub_Kegiatan']?.toString() ?? '',
      PaguDana1: (json['PaguDana1'] ?? 0).toDouble(),
      RealisasiFisik1: (json['RealisasiFisik1'] ?? 0).toDouble(),
      RealisasiKeuangan1: (json['RealisasiKeuangan1'] ?? 0).toDouble(),
      persen_keuangan1: (json['persen_keuangan1'] ?? 0).toDouble(),
      Locked: json['Locked'] ?? 0,
      created_at: json['created_at']?.toString(),
      updated_at: json['updated_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'RKAID': RKAID,
      'kode_sub_kegiatan': kode_sub_kegiatan,
      'Nm_Sub_Kegiatan': Nm_Sub_Kegiatan,
      'PaguDana1': PaguDana1,
      'RealisasiFisik1': RealisasiFisik1,
      'RealisasiKeuangan1': RealisasiKeuangan1,
      'persen_keuangan1': persen_keuangan1,
      'Locked': Locked,
      'created_at': created_at,
      'updated_at': updated_at,
    };
  }

  double get sisaAnggaran => PaguDana1 - RealisasiKeuangan1;
  bool get isLocked => Locked == 1;
}

class RKASummary {
  final double paguunitkerja;
  final double pagukegiatan;
  final double realisasi;
  final double sisa;
  final double persen_keuangan;
  final double fisik;

  RKASummary({
    required this.paguunitkerja,
    required this.pagukegiatan,
    required this.realisasi,
    required this.sisa,
    required this.persen_keuangan,
    required this.fisik,
  });

  factory RKASummary.fromJson(Map<String, dynamic> json) {
    return RKASummary(
      paguunitkerja: (json['paguunitkerja'] ?? 0).toDouble(),
      pagukegiatan: (json['pagukegiatan'] ?? 0).toDouble(),
      realisasi: (json['realisasi'] ?? 0).toDouble(),
      sisa: (json['sisa'] ?? 0).toDouble(),
      persen_keuangan: (json['persen_keuangan'] ?? 0).toDouble(),
      fisik: (json['fisik'] ?? 0).toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'paguunitkerja': paguunitkerja,
      'pagukegiatan': pagukegiatan,
      'realisasi': realisasi,
      'sisa': sisa,
      'persen_keuangan': persen_keuangan,
      'fisik': fisik,
    };
  }
}

class BidangUrusanModel {
  final String BidangID;
  final String nama_bidang;

  BidangUrusanModel({
    required this.BidangID,
    required this.nama_bidang,
  });

  factory BidangUrusanModel.fromJson(Map<String, dynamic> json) {
    return BidangUrusanModel(
      BidangID: json['BidangID']?.toString() ?? '',
      nama_bidang: json['nama_bidang']?.toString() ?? '',
    );
  }
}

class ProgramModel {
  final String PrgID;
  final String nama_program;

  ProgramModel({
    required this.PrgID,
    required this.nama_program,
  });

  factory ProgramModel.fromJson(Map<String, dynamic> json) {
    return ProgramModel(
      PrgID: json['PrgID']?.toString() ?? '',
      nama_program: json['nama_program']?.toString() ?? '',
    );
  }
}

class KegiatanModel {
  final String KgtID;
  final String nama_kegiatan;

  KegiatanModel({
    required this.KgtID,
    required this.nama_kegiatan,
  });

  factory KegiatanModel.fromJson(Map<String, dynamic> json) {
    return KegiatanModel(
      KgtID: json['KgtID']?.toString() ?? '',
      nama_kegiatan: json['nama_kegiatan']?.toString() ?? '',
    );
  }
}

class SubKegiatanModel {
  final String SubKgtID;
  final String nama_sub_kegiatan;

  SubKegiatanModel({
    required this.SubKgtID,
    required this.nama_sub_kegiatan,
  });

  factory SubKegiatanModel.fromJson(Map<String, dynamic> json) {
    return SubKegiatanModel(
      SubKgtID: json['SubKgtID']?.toString() ?? '',
      nama_sub_kegiatan: json['nama_sub_kegiatan']?.toString() ?? '',
    );
  }
}

