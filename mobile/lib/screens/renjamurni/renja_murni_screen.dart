import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/renja_murni_provider.dart';
import '../../providers/ui_front_provider.dart';
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
        // Handle menu item selection
        debugPrint('Menu selected: $route');
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
                      Expanded(
                        child: Text(
                          'Nilai persen realisasi keuangan tetap muncul 0% bila kurang dari 0.01%',
                          style: TextStyle(
                            color: Colors.blue.shade900,
                            fontSize: 12,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                // Stat Cards
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 16,
                  mainAxisSpacing: 16,
                  childAspectRatio: 0.85,
                  children: [
                    // APBD Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
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
                            const Spacer(),
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
                    // PROG. DAN KEG. Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
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
                            const Spacer(),
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
                    // KEUANGAN Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
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
                            const Spacer(),
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
                    // FISIK Card
                    Card(
                      elevation: 0,
                      color: const Color(0xFF385F73),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
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
                            const Spacer(),
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
                // Chart Section - Coming Soon
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Expanded(
                              child: Text(
                                'Progres Realisasi Keuangan',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.refresh),
                              onPressed: () {
                                if (user?.tahunSelected != null) {
                                  renjaMurniProvider.reloadStatistik2(user!.tahunSelected!);
                                }
                              },
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        Container(
                          height: 200,
                          decoration: BoxDecoration(
                            color: Colors.grey.shade100,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: const Center(
                            child: Text(
                              'Chart akan ditampilkan di sini\n(Coming Soon)',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                color: Colors.grey,
                                fontSize: 14,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Expanded(
                              child: Text(
                                'Progres Realisasi Fisik',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.refresh),
                              onPressed: () {
                                if (user?.tahunSelected != null) {
                                  renjaMurniProvider.reloadStatistik2(user!.tahunSelected!);
                                }
                              },
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        Container(
                          height: 200,
                          decoration: BoxDecoration(
                            color: Colors.grey.shade100,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: const Center(
                            child: Text(
                              'Chart akan ditampilkan di sini\n(Coming Soon)',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                color: Colors.grey,
                                fontSize: 14,
                              ),
                            ),
                          ),
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
}

