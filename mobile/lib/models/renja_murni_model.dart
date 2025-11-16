class RenjaMurniStatistik1 {
  final double paguDana1;
  final int jumlahProgram1;
  final int jumlahKegiatan1;
  final int jumlahSubKegiatan1;
  final double realisasiKeuangan1;
  final double persenRealisasiKeuangan1;
  final double realisasiFisik1;

  RenjaMurniStatistik1({
    required this.paguDana1,
    required this.jumlahProgram1,
    required this.jumlahKegiatan1,
    required this.jumlahSubKegiatan1,
    required this.realisasiKeuangan1,
    required this.persenRealisasiKeuangan1,
    required this.realisasiFisik1,
  });

  factory RenjaMurniStatistik1.fromJson(Map<String, dynamic> json) {
    return RenjaMurniStatistik1(
      paguDana1: (json['PaguDana1'] ?? 0).toDouble(),
      jumlahProgram1: json['JumlahProgram1'] ?? 0,
      jumlahKegiatan1: json['JumlahKegiatan1'] ?? 0,
      jumlahSubKegiatan1: json['JumlahSubKegiatan1'] ?? 0,
      realisasiKeuangan1: (json['RealisasiKeuangan1'] ?? 0).toDouble(),
      persenRealisasiKeuangan1: (json['PersenRealisasiKeuangan1'] ?? 0).toDouble(),
      realisasiFisik1: (json['RealisasiFisik1'] ?? 0).toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'PaguDana1': paguDana1,
      'JumlahProgram1': jumlahProgram1,
      'JumlahKegiatan1': jumlahKegiatan1,
      'JumlahSubKegiatan1': jumlahSubKegiatan1,
      'RealisasiKeuangan1': realisasiKeuangan1,
      'PersenRealisasiKeuangan1': persenRealisasiKeuangan1,
      'RealisasiFisik1': realisasiFisik1,
    };
  }
}

class RenjaMurniChart {
  final List<dynamic> target; // Array of target values
  final List<dynamic> realisasi; // Array of realisasi values

  RenjaMurniChart({
    required this.target,
    required this.realisasi,
  });

  factory RenjaMurniChart.fromJson(List<dynamic>? json) {
    if (json == null || json.length < 2) {
      return RenjaMurniChart(
        target: [],
        realisasi: [],
      );
    }
    
    // Ensure target and realisasi are lists
    final targetData = json[0];
    final realisasiData = json[1];
    
    return RenjaMurniChart(
      target: targetData is List ? targetData : [],
      realisasi: realisasiData is List ? realisasiData : [],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'target': target,
      'realisasi': realisasi,
    };
  }

  // Generate labels based on data length (bulan 1-12)
  List<String> get labels {
    if (target.isEmpty && realisasi.isEmpty) {
      return [];
    }
    final length = target.length > realisasi.length ? target.length : realisasi.length;
    return List.generate(length, (index) => (index + 1).toString());
  }
}

