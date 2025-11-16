import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/auth_provider.dart';
import '../../providers/rka_murni_provider.dart';
import '../../providers/ui_front_provider.dart';
import '../../models/rka_murni_model.dart';
import 'renja_murni_layout.dart';

class RKAMurniScreen extends StatefulWidget {
  const RKAMurniScreen({super.key});

  @override
  State<RKAMurniScreen> createState() => _RKAMurniScreenState();
}

class _RKAMurniScreenState extends State<RKAMurniScreen> {
  final TextEditingController _searchController = TextEditingController();
  final Map<String, RKAItem> _expandedItems = {};

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      final rkaProvider = Provider.of<RKAMurniProvider>(context, listen: false);
      
      if (authProvider.user?.tahunSelected != null) {
        rkaProvider.fetchOPD(authProvider.user!.tahunSelected!);
      }
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    );
    return formatter.format(amount);
  }

  void _toggleExpand(RKAItem item) {
    setState(() {
      if (_expandedItems.containsKey(item.RKAID)) {
        _expandedItems.remove(item.RKAID);
      } else {
        _expandedItems.clear();
        _expandedItems[item.RKAID] = item;
      }
    });
  }

  Future<void> _handleDelete(RKAItem item, RKAMurniProvider provider, String tahun, String bulan) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus RKA'),
        content: Text(
          'Apakah Anda ingin menghapus data RKA Murni dengan Nama ${item.Nm_Sub_Kegiatan}?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final success = await provider.deleteRKA(item.RKAID);
      if (success && mounted) {
        await provider.loadDataKegiatan(tahun, bulan);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Data berhasil dihapus')),
          );
        }
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(provider.errorMessage ?? 'Gagal menghapus data'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Future<void> _handleReset(RKAItem item, RKAMurniProvider provider, String tahun, String bulan) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Reset Data Kegiatan'),
        content: Text(
          'Apakah Anda ingin mengeset ulang jumlah pagu, jumlah realisasi fisik dan keuangan untuk RKA dengan kode ${item.kode_sub_kegiatan}?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.orange),
            child: const Text('Reset'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final success = await provider.resetDataKegiatan(item.RKAID);
      if (success && mounted) {
        await provider.loadDataKegiatan(tahun, bulan);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Data berhasil direset')),
          );
        }
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(provider.errorMessage ?? 'Gagal reset data'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return RenjaMurniLayout(
      onMenuItemSelected: (route) {
        // Navigate to selected route
        // If already on the same route, pushReplacementNamed will replace current screen
        if (route == '/renjamurni/rka') {
          // Already on RKA Murni screen, do nothing
          return;
        }
        Navigator.pushNamed(context, route);
      },
      child: Consumer3<AuthProvider, RKAMurniProvider, UIFrontProvider>(
        builder: (context, authProvider, rkaProvider, uiFrontProvider, child) {
          final user = authProvider.user;
          final tahun = user?.tahunSelected ?? '';
          final bulan = uiFrontProvider.bulanRealisasi ?? DateTime.now().month.toString();

          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Filter Section
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'FILTER',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 16),
                        // OPD Dropdown
                        DropdownButtonFormField<String>(
                          decoration: const InputDecoration(
                            labelText: 'OPD / SKPD',
                            border: OutlineInputBorder(),
                          ),
                          value: rkaProvider.orgIDSelected,
                          items: rkaProvider.daftarOPD.map((opd) {
                            return DropdownMenuItem<String>(
                              value: opd.OrgID,
                              child: Text(opd.Nm_Organisasi),
                            );
                          }).toList(),
                          onChanged: (String? orgID) {
                            if (orgID != null) {
                              rkaProvider.loadUnitKerja(orgID);
                            }
                          },
                        ),
                        const SizedBox(height: 16),
                        // Unit Kerja Dropdown
                        DropdownButtonFormField<String>(
                          decoration: const InputDecoration(
                            labelText: 'UNIT KERJA',
                            border: OutlineInputBorder(),
                          ),
                          value: rkaProvider.sOrgIDSelected,
                          items: rkaProvider.daftarUnitKerja.map((unit) {
                            return DropdownMenuItem<String>(
                              value: unit.SOrgID,
                              child: Text(unit.Nm_Sub_Organisasi),
                            );
                          }).toList(),
                          onChanged: (String? sOrgID) {
                            if (sOrgID != null) {
                              rkaProvider.setSOrgIDSelected(sOrgID, tahun, bulan);
                            }
                          },
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                // Search Section
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: TextField(
                      controller: _searchController,
                      decoration: const InputDecoration(
                        labelText: 'Search',
                        prefixIcon: Icon(Icons.search),
                        border: OutlineInputBorder(),
                      ),
                      onChanged: (value) {
                        rkaProvider.setSearchQuery(value);
                      },
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                // Data Table Section
                Card(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Toolbar
                      Padding(
                        padding: const EdgeInsets.all(16),
                        child: Row(
                          children: [
                            const Expanded(
                              child: Text(
                                'DAFTAR SUB KEGIATAN',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            if (rkaProvider.sOrgIDSelected != null &&
                                rkaProvider.sOrgIDSelected!.isNotEmpty &&
                                rkaProvider.locked)
                              IconButton(
                                icon: const Icon(Icons.add),
                                color: Colors.blue,
                                tooltip: 'TAMBAH RKA',
                                onPressed: () {
                                  _showDialogForm(context, rkaProvider, authProvider, uiFrontProvider);
                                },
                              ),
                          ],
                        ),
                      ),
                      // Data Table
                      if (rkaProvider.dataTableLoading)
                        const Padding(
                          padding: EdgeInsets.all(32),
                          child: Center(child: CircularProgressIndicator()),
                        )
                      else if (rkaProvider.filteredDataTable.isEmpty)
                        Padding(
                          padding: const EdgeInsets.all(32),
                          child: Center(
                            child: Column(
                              children: [
                                if (rkaProvider.showBtnLoadDataKegiatan)
                                  ElevatedButton(
                                    onPressed: rkaProvider.btnLoading
                                        ? null
                                        : () async {
                                            await rkaProvider.loadDataKegiatanFirstTime(tahun, bulan);
                                          },
                                    child: rkaProvider.btnLoading
                                        ? const SizedBox(
                                            width: 20,
                                            height: 20,
                                            child: CircularProgressIndicator(strokeWidth: 2),
                                          )
                                        : const Text('LOAD DATA KEGIATAN'),
                                  )
                                else
                                  const Text(
                                    'Tidak ada data',
                                    style: TextStyle(color: Colors.grey),
                                  ),
                              ],
                            ),
                          ),
                        )
                      else
                        SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: DataTable(
                            showCheckboxColumn: false,
                            columns: const [
                              DataColumn(label: Text('KODE', style: TextStyle(fontWeight: FontWeight.bold))),
                              DataColumn(label: Text('NAMA SUB KEGIATAN', style: TextStyle(fontWeight: FontWeight.bold))),
                              DataColumn(label: Text('PAGU KEGIATAN', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('REALISASI FISIK', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('REALISASI KEUANGAN', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('%', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('SISA PAGU', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('AKSI', style: TextStyle(fontWeight: FontWeight.bold))),
                            ],
                            rows: [
                              ...rkaProvider.filteredDataTable.map((item) {
                                return DataRow(
                                  onLongPress: () {
                                    _toggleExpand(item);
                                  },
                                  cells: [
                                    DataCell(Text(item.kode_sub_kegiatan)),
                                    DataCell(
                                      Expanded(
                                        child: Text(
                                          item.Nm_Sub_Kegiatan,
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    ),
                                    DataCell(Text(_formatCurrency(item.PaguDana1))),
                                    DataCell(Text(item.RealisasiFisik1.toStringAsFixed(1))),
                                    DataCell(Text(_formatCurrency(item.RealisasiKeuangan1))),
                                    DataCell(Text(item.persen_keuangan1.toStringAsFixed(2))),
                                    DataCell(Text(_formatCurrency(item.sisaAnggaran))),
                                    DataCell(
                                      Row(
                                        mainAxisSize: MainAxisSize.min,
                                        children: [
                                          IconButton(
                                            icon: const Icon(Icons.visibility, size: 20),
                                            color: Colors.blue,
                                            tooltip: 'Detail uraian kegiatan',
                                            onPressed: () {
                                              // Navigate to uraian screen
                                              debugPrint('View uraian: ${item.RKAID}');
                                            },
                                          ),
                                          IconButton(
                                            icon: const Icon(Icons.sync, size: 20),
                                            color: Colors.orange,
                                            tooltip: 'Load uraian',
                                            onPressed: item.PaguDana1 > 0 || item.isLocked || rkaProvider.btnLoading
                                                ? null
                                                : () async {
                                                    await rkaProvider.loadDataUraianFirstTime(item.RKAID);
                                                    if (mounted) {
                                                      await rkaProvider.loadDataKegiatan(tahun, bulan);
                                                    }
                                                  },
                                          ),
                                          IconButton(
                                            icon: const Icon(Icons.delete, size: 20),
                                            color: Colors.red,
                                            tooltip: 'Hapus RKA',
                                            onPressed: rkaProvider.btnLoading || item.isLocked
                                                ? null
                                                : () => _handleDelete(item, rkaProvider, tahun, bulan),
                                          ),
                                          if (item.isLocked)
                                            const Icon(Icons.lock, size: 16, color: Colors.grey),
                                        ],
                                      ),
                                    ),
                                  ],
                                );
                              }),
                              // Footer Row
                              DataRow(
                                color: MaterialStateProperty.all(Colors.amber.shade100),
                                cells: [
                                  const DataCell(Text('TOTAL', style: TextStyle(fontWeight: FontWeight.bold))),
                                  const DataCell(SizedBox.shrink()),
                                  DataCell(Text(
                                    _formatCurrency(rkaProvider.summary.pagukegiatan),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    rkaProvider.summary.fisik.toStringAsFixed(1),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    _formatCurrency(rkaProvider.summary.realisasi),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    rkaProvider.summary.persen_keuangan.toStringAsFixed(2),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    _formatCurrency(rkaProvider.summary.sisa),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  const DataCell(SizedBox.shrink()),
                                ],
                              ),
                            ],
                          ),
                        ),
                      // Expanded Row Content
                      if (_expandedItems.isNotEmpty)
                        ..._expandedItems.values.map((item) {
                          return Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              border: Border(
                                top: BorderSide(color: Colors.grey.shade300),
                              ),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('ID: ${item.RKAID}'),
                                if (item.created_at != null)
                                  Text('created_at: ${item.created_at}'),
                                if (item.updated_at != null)
                                  Text('updated_at: ${item.updated_at}'),
                                if (!item.isLocked)
                                  Padding(
                                    padding: const EdgeInsets.only(top: 8),
                                    child: ElevatedButton(
                                      onPressed: rkaProvider.btnLoading
                                          ? null
                                          : () => _handleReset(item, rkaProvider, tahun, bulan),
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.orange,
                                      ),
                                      child: const Text('RESET'),
                                    ),
                                  ),
                              ],
                            ),
                          );
                        }),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                // Info Section
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Total Realisasi Fisik: (Total Realisasi Fisik / Jumlah Sub Kegiatan)',
                          style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Total Persen Realisasi Keuangan: (Total Realisasi Keuangan / Pagu Kegiatan) * 100',
                          style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
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

  void _showDialogForm(
    BuildContext context,
    RKAMurniProvider rkaProvider,
    AuthProvider authProvider,
    UIFrontProvider uiFrontProvider,
  ) {
    final user = authProvider.user;
    final tahun = user?.tahunSelected ?? '';
    final _formKey = GlobalKey<FormState>();

    rkaProvider.openDialogForm();

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (dialogContext) => Consumer<RKAMurniProvider>(
        builder: (context, provider, child) {
          return Dialog(
            child: SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Form(
                  key: _formKey,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'TAMBAH KEGIATAN',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      if (provider.dataUnitKerja != null) ...[
                        const SizedBox(height: 8),
                        Text(
                          '${provider.dataUnitKerja!.kode_sub_organisasi ?? ''} / ${provider.dataUnitKerja!.Nm_Sub_Organisasi}',
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.grey.shade600,
                          ),
                        ),
                      ],
                      const SizedBox(height: 16),
                      // Bidang Urusan
                      DropdownButtonFormField<String>(
                        decoration: const InputDecoration(
                          labelText: 'BIDANG URUSAN',
                          border: OutlineInputBorder(),
                        ),
                        value: provider.formBidangID,
                        items: provider.daftarBidang.map((bidang) {
                          return DropdownMenuItem<String>(
                            value: bidang.BidangID,
                            child: Text(bidang.nama_bidang),
                          );
                        }).toList(),
                        onChanged: (String? bidangID) {
                          if (bidangID != null && bidangID != 'all') {
                            provider.setFormBidangID(bidangID);
                            provider.loadProgramList(bidangID, tahun);
                          }
                        },
                      ),
                      const SizedBox(height: 16),
                      // Program
                      DropdownButtonFormField<String>(
                        decoration: const InputDecoration(
                          labelText: 'PROGRAM',
                          border: OutlineInputBorder(),
                        ),
                        value: provider.formPrgID,
                        items: provider.daftarProgram.map((program) {
                          return DropdownMenuItem<String>(
                            value: program.PrgID,
                            child: Text(program.nama_program),
                          );
                        }).toList(),
                        onChanged: (String? prgID) {
                          if (prgID != null) {
                            provider.setFormPrgID(prgID);
                            provider.loadKegiatanList(prgID);
                          }
                        },
                      ),
                      const SizedBox(height: 16),
                      // Kegiatan
                      DropdownButtonFormField<String>(
                        decoration: const InputDecoration(
                          labelText: 'KEGIATAN',
                          border: OutlineInputBorder(),
                        ),
                        value: provider.formKgtID,
                        items: provider.daftarKegiatan.map((kegiatan) {
                          return DropdownMenuItem<String>(
                            value: kegiatan.KgtID,
                            child: Text(kegiatan.nama_kegiatan),
                          );
                        }).toList(),
                        onChanged: (String? kgtID) {
                          if (kgtID != null && provider.dataUnitKerja != null) {
                            provider.setFormKgtID(kgtID);
                            provider.loadSubKegiatanList(kgtID, provider.dataUnitKerja!.SOrgID);
                          }
                        },
                      ),
                      const SizedBox(height: 16),
                      // Sub Kegiatan (Required)
                      DropdownButtonFormField<String>(
                        decoration: const InputDecoration(
                          labelText: 'SUB KEGIATAN *',
                          border: OutlineInputBorder(),
                        ),
                        value: provider.formSubKgtID,
                        items: provider.daftarSubKegiatan.map((subKgt) {
                          return DropdownMenuItem<String>(
                            value: subKgt.SubKgtID,
                            child: Text(subKgt.nama_sub_kegiatan),
                          );
                        }).toList(),
                        onChanged: (String? subKgtID) {
                          if (subKgtID != null) {
                            provider.setFormSubKgtID(subKgtID);
                          }
                        },
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'Mohon untuk dipilih sub kegiatan !!!';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 24),
                      // Buttons
                      Row(
                        mainAxisAlignment: MainAxisAlignment.end,
                        children: [
                          TextButton(
                            onPressed: provider.btnLoading
                                ? null
                                : () {
                                    provider.closeDialogForm();
                                    Navigator.pop(dialogContext);
                                  },
                            child: const Text('TUTUP'),
                          ),
                          const SizedBox(width: 8),
                          ElevatedButton(
                            onPressed: provider.btnLoading ||
                                    provider.formSubKgtID == null ||
                                    provider.formSubKgtID!.isEmpty
                                ? null
                                : () async {
                                    if (_formKey.currentState!.validate()) {
                                      final success = await provider.saveKegiatan();
                                      if (success && mounted) {
                                        final bulan = uiFrontProvider.bulanRealisasi ?? DateTime.now().month.toString();
                                        await provider.loadDataKegiatan(tahun, bulan);
                                        if (mounted) {
                                          Navigator.pop(dialogContext);
                                          ScaffoldMessenger.of(context).showSnackBar(
                                            const SnackBar(content: Text('Data berhasil disimpan')),
                                          );
                                        }
                                      } else if (mounted) {
                                        ScaffoldMessenger.of(dialogContext).showSnackBar(
                                          SnackBar(
                                            content: Text(provider.errorMessage ?? 'Gagal menyimpan data'),
                                            backgroundColor: Colors.red,
                                          ),
                                        );
                                      }
                                    }
                                  },
                            child: provider.btnLoading
                                ? const SizedBox(
                                    width: 20,
                                    height: 20,
                                    child: CircularProgressIndicator(strokeWidth: 2),
                                  )
                                : const Text('SIMPAN'),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          );
        },
      ),
    ).then((_) {
      rkaProvider.closeDialogForm();
    });
  }
}

