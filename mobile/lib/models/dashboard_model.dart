class DashboardModel {
  final int totalOPD;
  final int totalProgram;
  final int totalKegiatan;
  final double totalAnggaran;
  final double totalRealisasi;
  final double persentaseRealisasi;
  final List<StatistikItem> statistikItems;

  DashboardModel({
    required this.totalOPD,
    required this.totalProgram,
    required this.totalKegiatan,
    required this.totalAnggaran,
    required this.totalRealisasi,
    required this.persentaseRealisasi,
    required this.statistikItems,
  });

  factory DashboardModel.fromJson(Map<String, dynamic> json) {
    return DashboardModel(
      totalOPD: json['total_opd'] ?? 0,
      totalProgram: json['total_program'] ?? 0,
      totalKegiatan: json['total_kegiatan'] ?? 0,
      totalAnggaran: (json['total_anggaran'] ?? 0).toDouble(),
      totalRealisasi: (json['total_realisasi'] ?? 0).toDouble(),
      persentaseRealisasi: (json['persentase_realisasi'] ?? 0).toDouble(),
      statistikItems: json['statistik'] != null
          ? (json['statistik'] as List)
              .map((item) => StatistikItem.fromJson(item))
              .toList()
          : [],
    );
  }
}

class StatistikItem {
  final String label;
  final double value;
  final String? color;

  StatistikItem({
    required this.label,
    required this.value,
    this.color,
  });

  factory StatistikItem.fromJson(Map<String, dynamic> json) {
    return StatistikItem(
      label: json['label'] ?? '',
      value: (json['value'] ?? 0).toDouble(),
      color: json['color'],
    );
  }
}

