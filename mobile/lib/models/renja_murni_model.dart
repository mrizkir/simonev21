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
  final List<dynamic> labels; // Array of labels
  final List<dynamic> data; // Array of data values

  RenjaMurniChart({
    required this.labels,
    required this.data,
  });

  factory RenjaMurniChart.fromJson(List<dynamic> json) {
    if (json.length >= 2) {
      return RenjaMurniChart(
        labels: json[0] ?? [],
        data: json[1] ?? [],
      );
    }
    return RenjaMurniChart(
      labels: [],
      data: [],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'labels': labels,
      'data': data,
    };
  }
}

