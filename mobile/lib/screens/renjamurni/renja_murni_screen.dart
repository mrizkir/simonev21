import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../providers/auth_provider.dart';
import '../../providers/renja_murni_provider.dart';
import '../../providers/ui_front_provider.dart';
import '../../models/renja_murni_model.dart';
import 'renja_murni_layout.dart';

class RenjaMurniScreen extends StatefulWidget {
  const RenjaMurniScreen({super.key});

  @override
  State<RenjaMurniScreen> createState() => _RenjaMurniScreenState();
}

class _RenjaMurniScreenState extends State<RenjaMurniScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      final uiFrontProvider = Provider.of<UIFrontProvider>(context, listen: false);
      final renjaMurniProvider = Provider.of<RenjaMurniProvider>(context, listen: false);
      
      if (authProvider.user?.tahunSelected != null) {
        renjaMurniProvider.init(
          authProvider.user!.tahunSelected!,
          uiFrontProvider.bulanRealisasi,
        );
      }
    });
  }

  String _formatCurrency(double amount) {
    if (amount >= 1000000000) {
      return '${(amount / 1000000000).toStringAsFixed(2)} Milyar';
    } else if (amount >= 1000000) {
      return '${(amount / 1000000).toStringAsFixed(2)} Juta';
    } else if (amount >= 1000) {
      return '${(amount / 1000).toStringAsFixed(2)} Ribu';
    }
    return amount.toStringAsFixed(2);
  }

  @override
  Widget build(BuildContext context) {
    return RenjaMurniLayout(
      onMenuItemSelected: (route) {
        // Navigate to selected route
        // Use pushReplacementNamed to replace current screen instead of stacking
        if (route.isNotEmpty) {
          try {
            Navigator.pushReplacementNamed(context, route);
          } catch (error) {
            debugPrint('Navigation error: $error');
            // Fallback: try pushNamed if pushReplacementNamed fails
            try {
              Navigator.pushNamed(context, route);
            } catch (e) {
              debugPrint('Fallback navigation also failed: $e');
            }
          }
        }
      },
      child: Consumer3<AuthProvider, RenjaMurniProvider, UIFrontProvider>(
        builder: (context, authProvider, renjaMurniProvider, uiFrontProvider, child) {
          final user = authProvider.user;
          final statistik = renjaMurniProvider.statistik1;

          if (renjaMurniProvider.isLoading && statistik == null) {
            return const Center(
              child: CircularProgressIndicator(),
            );
          }

          if (renjaMurniProvider.errorMessage != null && statistik == null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.error_outline,
                    size: 64,
                    color: Colors.red.shade300,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    renjaMurniProvider.errorMessage!,
                    style: TextStyle(color: Colors.red.shade700),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      if (user?.tahunSelected != null) {
                        renjaMurniProvider.init(
                          user!.tahunSelected!,
                          uiFrontProvider.bulanRealisasi,
                        );
                      }
                    },
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            );
          }

          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Dropdown Bulan Realisasi
                Card(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    child: Row(
                      children: [
                        const Text(
                          'Bulan Realisasi:',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: DropdownButton<String>(
                            value: uiFrontProvider.bulanRealisasi ?? DateTime.now().month.toString(),
                            isExpanded: true,
                            underline: Container(),
                            items: List.generate(12, (index) {
                              final bulan = (index + 1).toString();
                              final namaBulan = uiFrontProvider.getNamaBulan(bulan);
                              return DropdownMenuItem<String>(
                                value: bulan,
                                child: Text(namaBulan),
                              );
                            }),
                            onChanged: (String? newBulan) {
                              if (newBulan != null) {
                                uiFrontProvider.setBulanRealisasi(newBulan);
                                // Reload data dengan bulan baru
                                if (user?.tahunSelected != null) {
                                  renjaMurniProvider.init(
                                    user!.tahunSelected!,
                                    newBulan,
                                  );
                                }
                              }
                            },
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                // Alert Info
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.blue.shade50,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.blue.shade200),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.info_outline, color: Colors.blue.shade700),
                      const SizedBox(width: 12),
                      const Expanded(
                        child: Text(
                          'Nilai persen realisasi keuangan tetap muncul 0% bila kurang dari 0.01%',
                          style: TextStyle(
                            color: Colors.blue,
                            fontSize: 12,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                // Stat Cards
                Column(
                  children: [
                    // APBD Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    'APBD ${user?.tahunSelected ?? ''}',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.refresh, color: Colors.white),
                                  onPressed: () {
                                    if (user?.tahunSelected != null) {
                                      renjaMurniProvider.reloadStatistik1(user!.tahunSelected!);
                                    }
                                  },
                                  padding: EdgeInsets.zero,
                                  constraints: const BoxConstraints(),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'Total Pagu APBD Murni TA ${user?.tahunSelected ?? ''}',
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.8),
                                fontSize: 12,
                              ),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              _formatCurrency(statistik?.paguDana1 ?? 0),
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            LinearProgressIndicator(
                              value: (statistik?.persenRealisasiKeuangan1 ?? 0) / 100,
                              backgroundColor: Colors.red.shade300,
                              valueColor: AlwaysStoppedAnimation<Color>(
                                Colors.green.shade400,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    // PROG. DAN KEG. Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Row(
                              children: [
                                const Expanded(
                                  child: Text(
                                    'PROG. DAN KEG.',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.refresh, color: Colors.white),
                                  onPressed: () {
                                    if (user?.tahunSelected != null) {
                                      renjaMurniProvider.reloadStatistik1(user!.tahunSelected!);
                                    }
                                  },
                                  padding: EdgeInsets.zero,
                                  constraints: const BoxConstraints(),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'Jumlah Program, Keg. dan Sub Keg. TA ${user?.tahunSelected ?? ''}',
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.8),
                                fontSize: 12,
                              ),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              'Prog.: ${statistik?.jumlahProgram1 ?? 0} / '
                              'Keg.: ${statistik?.jumlahKegiatan1 ?? 0} / '
                              'Sub Keg.: ${statistik?.jumlahSubKegiatan1 ?? 0}',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            LinearProgressIndicator(
                              value: (statistik?.persenRealisasiKeuangan1 ?? 0) / 100,
                              backgroundColor: Colors.red.shade300,
                              valueColor: AlwaysStoppedAnimation<Color>(
                                Colors.green.shade400,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    // KEUANGAN Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Row(
                              children: [
                                const Expanded(
                                  child: Text(
                                    'KEUANGAN',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.refresh, color: Colors.white),
                                  onPressed: () {
                                    if (user?.tahunSelected != null) {
                                      renjaMurniProvider.reloadStatistik1(user!.tahunSelected!);
                                    }
                                  },
                                  padding: EdgeInsets.zero,
                                  constraints: const BoxConstraints(),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'Realisasi Keuangan TA ${user?.tahunSelected ?? ''}',
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.8),
                                fontSize: 12,
                              ),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              '${_formatCurrency(statistik?.realisasiKeuangan1 ?? 0)} '
                              '(${(statistik?.persenRealisasiKeuangan1 ?? 0).toStringAsFixed(1)}%)',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            LinearProgressIndicator(
                              value: (statistik?.persenRealisasiKeuangan1 ?? 0) / 100,
                              backgroundColor: Colors.red.shade300,
                              valueColor: AlwaysStoppedAnimation<Color>(
                                Colors.green.shade400,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    // FISIK Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Row(
                              children: [
                                const Expanded(
                                  child: Text(
                                    'FISIK',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.refresh, color: Colors.white),
                                  onPressed: () {
                                    if (user?.tahunSelected != null) {
                                      renjaMurniProvider.reloadStatistik1(user!.tahunSelected!);
                                    }
                                  },
                                  padding: EdgeInsets.zero,
                                  constraints: const BoxConstraints(),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'Realisasi Fisik TA ${user?.tahunSelected ?? ''}',
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.8),
                                fontSize: 12,
                              ),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              '${(statistik?.realisasiFisik1 ?? 0).toStringAsFixed(1)} %',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 24,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            LinearProgressIndicator(
                              value: (statistik?.realisasiFisik1 ?? 0) / 100,
                              backgroundColor: Colors.red.shade300,
                              valueColor: AlwaysStoppedAnimation<Color>(
                                Colors.green.shade400,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                // Chart Section - Realisasi Keuangan
                Card(
                  margin: const EdgeInsets.symmetric(horizontal: 0),
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(12, 12, 8, 12),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Expanded(
                              child: Text(
                                'Progres Realisasi Keuangan',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.refresh, size: 20),
                              padding: EdgeInsets.zero,
                              constraints: const BoxConstraints(),
                              onPressed: () {
                                if (user?.tahunSelected != null) {
                                  renjaMurniProvider.reloadStatistik2(user!.tahunSelected!);
                                }
                              },
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        // Legend
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            _buildLegendItem('Target', Colors.orange),
                            const SizedBox(width: 16),
                            _buildLegendItem('Realisasi', Colors.blue),
                          ],
                        ),
                        const SizedBox(height: 8),
                        _buildLineChart(
                          renjaMurniProvider.chartKeuangan,
                          'Realisasi Keuangan',
                          Colors.blue,
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                // Chart Section - Realisasi Fisik
                Card(
                  margin: const EdgeInsets.symmetric(horizontal: 0),
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(12, 12, 8, 12),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Expanded(
                              child: Text(
                                'Progres Realisasi Fisik',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.refresh, size: 20),
                              padding: EdgeInsets.zero,
                              constraints: const BoxConstraints(),
                              onPressed: () {
                                if (user?.tahunSelected != null) {
                                  renjaMurniProvider.reloadStatistik2(user!.tahunSelected!);
                                }
                              },
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        // Legend
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            _buildLegendItem('Target', Colors.orange),
                            const SizedBox(width: 16),
                            _buildLegendItem('Realisasi', Colors.green),
                          ],
                        ),
                        const SizedBox(height: 8),
                        _buildLineChart(
                          renjaMurniProvider.chartFisik,
                          'Realisasi Fisik',
                          Colors.green,
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildLineChart(RenjaMurniChart? chart, String title, Color color) {
    if (chart == null) {
      return Container(
        height: 200,
        decoration: BoxDecoration(
          color: Colors.grey.shade100,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Center(
          child: Text(
            'Data chart belum tersedia',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.grey.shade600,
              fontSize: 14,
            ),
          ),
        ),
      );
    }

    // Check if target and realisasi are valid
    if (chart.target.isEmpty || chart.realisasi.isEmpty) {
      return Container(
        height: 200,
        decoration: BoxDecoration(
          color: Colors.grey.shade100,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Center(
          child: Text(
            'Data chart belum tersedia',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.grey.shade600,
              fontSize: 14,
            ),
          ),
        ),
      );
    }

    // Convert target data to List<double>
    List<double> targetData = [];
    for (var item in chart.target) {
      if (item is num) {
        targetData.add(item.toDouble());
      } else if (item is String) {
        targetData.add(double.tryParse(item) ?? 0.0);
      } else {
        targetData.add(0.0);
      }
    }

    // Convert realisasi data to List<double>
    List<double> realisasiData = [];
    for (var item in chart.realisasi) {
      if (item is num) {
        realisasiData.add(item.toDouble());
      } else if (item is String) {
        realisasiData.add(double.tryParse(item) ?? 0.0);
      } else {
        realisasiData.add(0.0);
      }
    }

    // Convert labels to List<String>
    List<String> chartLabels = chart.labels;

    if (targetData.isEmpty || realisasiData.isEmpty || chartLabels.isEmpty) {
      return Container(
        height: 200,
        decoration: BoxDecoration(
          color: Colors.grey.shade100,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Center(
          child: Text(
            'Data chart kosong',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.grey.shade600,
              fontSize: 14,
            ),
          ),
        ),
      );
    }

    // Find min and max from both datasets for better scaling
    final allData = [...targetData, ...realisasiData];
    double minY = allData.reduce((a, b) => a < b ? a : b);
    double maxY = allData.reduce((a, b) => a > b ? a : b);
    
    // Add some padding to min/max
    double padding = (maxY - minY) * 0.1;
    minY = (minY - padding).clamp(0, double.infinity);
    maxY = maxY + padding;

    // If all values are same, add some range
    if (minY == maxY) {
      minY = 0;
      maxY = maxY + 10;
    }

    // Calculate chart width - minimum 300 per data point, but at least screen width
    double chartWidth = (chartLabels.length * 50.0).clamp(300.0, double.infinity);
    bool isScrollable = chartWidth > 300;
    
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (isScrollable)
          Padding(
            padding: const EdgeInsets.only(bottom: 8.0),
            child: Row(
              children: [
                Icon(
                  Icons.swipe,
                  size: 14,
                  color: Colors.grey.shade600,
                ),
                const SizedBox(width: 4),
                Text(
                  'Geser ke kanan/kiri untuk melihat data lengkap',
                  style: TextStyle(
                    color: Colors.grey.shade600,
                    fontSize: 11,
                    fontStyle: FontStyle.italic,
                  ),
                ),
              ],
            ),
          ),
        SizedBox(
          height: 220,
          child: SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            physics: const BouncingScrollPhysics(),
            child: SizedBox(
              width: chartWidth,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 8),
                child: LineChart(
                  LineChartData(
                gridData: FlGridData(
                  show: true,
                  drawVerticalLine: false,
                  horizontalInterval: (maxY - minY) / 5,
                  getDrawingHorizontalLine: (value) {
                    return FlLine(
                      color: Colors.grey.shade300,
                      strokeWidth: 1,
                    );
                  },
                ),
                titlesData: FlTitlesData(
                  show: true,
                  rightTitles: const AxisTitles(
                    sideTitles: SideTitles(showTitles: false),
                  ),
                  topTitles: const AxisTitles(
                    sideTitles: SideTitles(showTitles: false),
                  ),
                  bottomTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 35,
                      interval: chartLabels.length > 12 ? 2 : 1,
                      getTitlesWidget: (value, meta) {
                        final index = value.toInt();
                        if (index >= 0 && index < chartLabels.length) {
                          String label = chartLabels[index];
                          // Shorten label if too long for mobile
                          if (label.length > 6) {
                            label = label.substring(0, 6);
                          }
                          return Padding(
                            padding: const EdgeInsets.only(top: 8.0),
                            child: Text(
                              label,
                              style: TextStyle(
                                color: Colors.grey.shade700,
                                fontSize: 9,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          );
                        }
                        return const SizedBox.shrink();
                      },
                    ),
                  ),
                  leftTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 45,
                      interval: (maxY - minY) / 5,
                      getTitlesWidget: (value, meta) {
                        return Padding(
                          padding: const EdgeInsets.only(right: 4.0),
                          child: Text(
                            value.toInt().toString(),
                            style: TextStyle(
                              color: Colors.grey.shade700,
                              fontSize: 9,
                            ),
                            textAlign: TextAlign.right,
                          ),
                        );
                      },
                    ),
                  ),
                ),
                borderData: FlBorderData(
                  show: true,
                  border: Border.all(color: Colors.grey.shade300, width: 1),
                ),
                minX: 0,
                maxX: (chartLabels.length - 1).toDouble(),
                minY: minY,
                maxY: maxY,
                lineBarsData: [
                  // Line Target
                  LineChartBarData(
                    spots: List.generate(
                      targetData.length,
                      (index) => FlSpot(index.toDouble(), targetData[index]),
                    ),
                    isCurved: true,
                    color: Colors.orange,
                    barWidth: 2.5,
                    isStrokeCapRound: true,
                    dotData: FlDotData(
                      show: true,
                      getDotPainter: (spot, percent, barData, index) {
                        return FlDotCirclePainter(
                          radius: 3,
                          color: Colors.orange,
                          strokeWidth: 2,
                          strokeColor: Colors.white,
                        );
                      },
                    ),
                    belowBarData: BarAreaData(show: false),
                  ),
                  // Line Realisasi
                  LineChartBarData(
                    spots: List.generate(
                      realisasiData.length,
                      (index) => FlSpot(index.toDouble(), realisasiData[index]),
                    ),
                    isCurved: true,
                    color: color,
                    barWidth: 2.5,
                    isStrokeCapRound: true,
                    dotData: FlDotData(
                      show: true,
                      getDotPainter: (spot, percent, barData, index) {
                        return FlDotCirclePainter(
                          radius: 3,
                          color: color,
                          strokeWidth: 2,
                          strokeColor: Colors.white,
                        );
                      },
                    ),
                    belowBarData: BarAreaData(
                      show: true,
                      color: color.withOpacity(0.1),
                    ),
                  ),
                ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildLegendItem(String label, Color color) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(
            color: color,
            shape: BoxShape.circle,
          ),
        ),
        const SizedBox(width: 6),
        Text(
          label,
          style: TextStyle(
            fontSize: 11,
            color: Colors.grey.shade700,
          ),
        ),
      ],
    );
  }
}

