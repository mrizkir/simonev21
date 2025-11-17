import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/uraian_rka_murni_provider.dart';
import '../../models/rka_murni_model.dart';
import 'renja_murni_layout.dart';

class UraianRKAMurniScreen extends StatefulWidget {
  final String? rkaID;
  final RKAItem? rkaItem;

  const UraianRKAMurniScreen({
    super.key,
    this.rkaID,
    this.rkaItem,
  });

  @override
  State<UraianRKAMurniScreen> createState() => _UraianRKAMurniScreenState();
}

class _UraianRKAMurniScreenState extends State<UraianRKAMurniScreen> {
  final TextEditingController _searchController = TextEditingController();
  final Map<String, RKAUraianItem> _expandedItems = {};

  String get _rkaID {
    if (widget.rkaItem != null) {
      return widget.rkaItem!.RKAID;
    }
    return widget.rkaID ?? '';
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<UraianRKAMurniProvider>(context, listen: false);
      
      // Jika ada RKAItem, gunakan untuk membuat data kegiatan sementara
      if (widget.rkaItem != null) {
        provider.setDataKegiatanFromRKAItem(widget.rkaItem!);
      }
      
      provider.loadData(_rkaID);
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

  String _formatDate(String? dateString) {
    if (dateString == null || dateString.isEmpty) return '-';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd/MM/yyyy HH:mm').format(date);
    } catch (e) {
      return dateString;
    }
  }

  void _toggleExpand(RKAUraianItem item) {
    setState(() {
      if (_expandedItems.containsKey(item.RKARincID)) {
        _expandedItems.remove(item.RKARincID);
      } else {
        _expandedItems.clear();
        _expandedItems[item.RKARincID] = item;
      }
    });
  }

  Future<void> _handleDelete(RKAUraianItem item, UraianRKAMurniProvider provider) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Uraian'),
        content: Text(
          'Apakah Anda ingin menghapus data uraian RKA Murni dengan kode ${item.kode_uraian}?',
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
      final success = await provider.deleteUraian(item.RKARincID);
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Data berhasil dihapus')),
        );
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

  Future<void> _handleReset(UraianRKAMurniProvider provider) async {
    if (provider.dataKegiatan == null) return;

    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Reset Data Kegiatan'),
        content: Text(
          'Apakah Anda ingin mengeset ulang jumlah pagu, jumlah realisasi fisik dan keuangan untuk RKA dengan kode ${provider.dataKegiatan!.kode_sub_kegiatan}?',
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
      final success = await provider.resetDataKegiatan(provider.dataKegiatan!.RKAID);
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Data berhasil direset')),
        );
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

  void _exitUraianRKA() {
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return RenjaMurniLayout(
      onMenuItemSelected: (route) {
        if (route == '/renjamurni/rka') {
          Navigator.popUntil(context, (route) => route.settings.name == '/renjamurni/rka' || route.isFirst);
          if (route != '/renjamurni/rka') {
            Navigator.pushNamed(context, '/renjamurni/rka');
          }
        } else {
          Navigator.pushNamed(context, route);
        }
      },
      child: Consumer<UraianRKAMurniProvider>(
        builder: (context, provider, child) {
          // Show loading while fetching data
          if (provider.dataTableLoading && provider.dataKegiatan == null) {
            return const Center(child: CircularProgressIndicator());
          }

          // Check if we have data kegiatan (either from RKAItem or from API)
          if (provider.dataKegiatan == null || provider.dataKegiatan!.RKAID.isEmpty) {
            // If still loading, show loading indicator
            if (provider.dataTableLoading) {
              return const Center(child: CircularProgressIndicator());
            }
            // If not loading and no data, show error
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text('Data kegiatan tidak ditemukan'),
                  if (provider.errorMessage != null)
                    Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Text(
                        provider.errorMessage!,
                        style: const TextStyle(color: Colors.red),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => _exitUraianRKA(),
                    child: const Text('Kembali'),
                  ),
                ],
              ),
            );
          }

          final kegiatan = provider.dataKegiatan!;

          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Data Kegiatan Card
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Text(
                              'DATA KEGIATAN',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const Spacer(),
                            if (kegiatan.isLocked)
                              const Icon(Icons.lock, size: 20, color: Colors.grey),
                          ],
                        ),
                        const SizedBox(height: 16),
                        SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: Table(
                            columnWidths: const {
                              0: FixedColumnWidth(150),
                              1: FixedColumnWidth(300),
                              2: FixedColumnWidth(150),
                              3: FixedColumnWidth(300),
                            },
                            children: [
                            TableRow(
                              children: [
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('RKAID', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.RKAID),
                                ),
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('PAGU DANA', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(_formatCurrency(kegiatan.PaguDana1)),
                                ),
                              ],
                            ),
                            TableRow(
                              children: [
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('KODE PROGRAM', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.kode_program),
                                ),
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('NAMA BIDANG URUSAN', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.Nm_Bidang ?? '-'),
                                ),
                              ],
                            ),
                            TableRow(
                              children: [
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('PROGRAM', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.Nm_Program),
                                ),
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('KODE OPD / SKPD', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.kode_organisasi),
                                ),
                              ],
                            ),
                            TableRow(
                              children: [
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('KODE KEGIATAN', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.kode_kegiatan),
                                ),
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('OPD / SKPD', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.Nm_Organisasi),
                                ),
                              ],
                            ),
                            TableRow(
                              children: [
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('NAMA KEGIATAN', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.Nm_Kegiatan),
                                ),
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('KODE UNIT KERJA', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.kode_sub_organisasi ?? '-'),
                                ),
                              ],
                            ),
                            TableRow(
                              children: [
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('KODE SUB KEGIATAN', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.kode_sub_kegiatan),
                                ),
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('UNIT KERJA', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.Nm_Sub_Organisasi),
                                ),
                              ],
                            ),
                            TableRow(
                              children: [
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('NAMA SUB KEGIATAN', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text(kegiatan.Nm_Sub_Kegiatan),
                                ),
                                const Padding(
                                  padding: EdgeInsets.all(8.0),
                                  child: Text('DIBUAT/DIUBAH', style: TextStyle(fontWeight: FontWeight.bold)),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text('${_formatDate(kegiatan.created_at)} / ${_formatDate(kegiatan.updated_at)}'),
                                ),
                              ],
                            ),
                          ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                // Bottom Navigation
                Card(
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 8),
                    child: Wrap(
                      alignment: WrapAlignment.center,
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        SizedBox(
                          width: (MediaQuery.of(context).size.width - 48) / 3 - 8,
                          child: TextButton.icon(
                            onPressed: kegiatan.isLocked || provider.btnLoading
                                ? null
                                : () => _handleReset(provider),
                            icon: const Icon(Icons.refresh, size: 18),
                            label: const Text('Reset', style: TextStyle(fontSize: 12)),
                          ),
                        ),
                        SizedBox(
                          width: (MediaQuery.of(context).size.width - 48) / 3 - 8,
                          child: TextButton.icon(
                            onPressed: kegiatan.isLocked
                                ? null
                                : () {
                                    // TODO: Navigate to edit RKA screen
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(content: Text('Fitur Edit RKA belum tersedia')),
                                    );
                                  },
                            icon: const Icon(Icons.edit, size: 18),
                            label: const Text('Edit RKA', style: TextStyle(fontSize: 12)),
                          ),
                        ),
                        SizedBox(
                          width: (MediaQuery.of(context).size.width - 48) / 3 - 8,
                          child: TextButton.icon(
                            onPressed: kegiatan.isLocked
                                ? null
                                : () {
                                    // TODO: Show target fisik dialog
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(content: Text('Fitur Target Fisik belum tersedia')),
                                    );
                                  },
                            icon: const Icon(Icons.history, size: 18),
                            label: const Text('Target Fisik', style: TextStyle(fontSize: 12)),
                          ),
                        ),
                        SizedBox(
                          width: (MediaQuery.of(context).size.width - 48) / 3 - 8,
                          child: TextButton.icon(
                            onPressed: kegiatan.isLocked
                                ? null
                                : () {
                                    // TODO: Show target anggaran kas dialog
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(content: Text('Fitur Target Anggaran Kas belum tersedia')),
                                    );
                                  },
                            icon: const Icon(Icons.history, size: 18),
                            label: const Text('Target Anggaran', style: TextStyle(fontSize: 12)),
                          ),
                        ),
                        SizedBox(
                          width: (MediaQuery.of(context).size.width - 48) / 3 - 8,
                          child: TextButton.icon(
                            onPressed: () {
                              // TODO: Show realisasi kinerja dialog
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('Fitur Realisasi Kinerja belum tersedia')),
                              );
                            },
                            icon: const Icon(Icons.note_outlined, size: 18),
                            label: const Text('Realisasi', style: TextStyle(fontSize: 12)),
                          ),
                        ),
                        SizedBox(
                          width: (MediaQuery.of(context).size.width - 48) / 3 - 8,
                          child: TextButton.icon(
                            onPressed: () => _exitUraianRKA(),
                            icon: const Icon(Icons.close, size: 18),
                            label: const Text('Keluar', style: TextStyle(fontSize: 12)),
                          ),
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
                        provider.setSearchQuery(value);
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
                                'DAFTAR URAIAN',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            if (!kegiatan.isLocked)
                              IconButton(
                                icon: const Icon(Icons.add),
                                color: Colors.blue,
                                tooltip: 'TAMBAH URAIAN RKA',
                                onPressed: () {
                                  // TODO: Navigate to add uraian screen
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(content: Text('Fitur Tambah Uraian belum tersedia')),
                                  );
                                },
                              ),
                          ],
                        ),
                      ),
                      // Data Table
                      if (provider.dataTableLoading)
                        const Padding(
                          padding: EdgeInsets.all(32),
                          child: Center(child: CircularProgressIndicator()),
                        )
                      else if (provider.filteredDataTable.isEmpty)
                        Padding(
                          padding: const EdgeInsets.all(32),
                          child: Center(
                            child: Column(
                              children: [
                                if (provider.showBtnLoadDataUraian)
                                  ElevatedButton(
                                    onPressed: provider.btnLoading || kegiatan.isLocked
                                        ? null
                                        : () async {
                                            await provider.loadDataUraianFirstTime(_rkaID);
                                          },
                                    child: provider.btnLoading
                                        ? const SizedBox(
                                            width: 20,
                                            height: 20,
                                            child: CircularProgressIndicator(strokeWidth: 2),
                                          )
                                        : const Text('LOAD DATA URAIAN'),
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
                              DataColumn(label: Text('NAMA PAKET PEKERJAAN', style: TextStyle(fontWeight: FontWeight.bold))),
                              DataColumn(label: Text('PAGU URAIAN', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('REALISASI FISIK (%)', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('REALISASI KEUANGAN', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('%', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('SISA PAGU', style: TextStyle(fontWeight: FontWeight.bold)), numeric: true),
                              DataColumn(label: Text('AKSI', style: TextStyle(fontWeight: FontWeight.bold))),
                            ],
                            rows: [
                              ...provider.filteredDataTable.map((item) {
                                return DataRow(
                                  onLongPress: () {
                                    _toggleExpand(item);
                                  },
                                  cells: [
                                    DataCell(Text(item.kode_uraian)),
                                    DataCell(
                                      Text(
                                        item.nama_uraian,
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    DataCell(Text(_formatCurrency(item.PaguUraian1))),
                                    DataCell(Text(item.fisik1.toStringAsFixed(2))),
                                    DataCell(Text(_formatCurrency(item.realisasi1))),
                                    DataCell(Text(item.persen_keuangan1.toStringAsFixed(2))),
                                    DataCell(Text(_formatCurrency(item.sisa))),
                                    DataCell(
                                      Row(
                                        mainAxisSize: MainAxisSize.min,
                                        children: [
                                          IconButton(
                                            icon: const Icon(Icons.visibility, size: 20),
                                            color: Colors.blue,
                                            tooltip: 'Realisasi Uraian',
                                            onPressed: () {
                                              // TODO: Navigate to realisasi uraian screen
                                              ScaffoldMessenger.of(context).showSnackBar(
                                                const SnackBar(content: Text('Fitur Realisasi Uraian belum tersedia')),
                                              );
                                            },
                                          ),
                                          IconButton(
                                            icon: const Icon(Icons.edit, size: 20),
                                            color: Colors.orange,
                                            tooltip: 'Ubah Uraian',
                                            onPressed: kegiatan.isLocked || provider.btnLoading
                                                ? null
                                                : () {
                                                    // TODO: Show edit uraian dialog
                                                    ScaffoldMessenger.of(context).showSnackBar(
                                                      const SnackBar(content: Text('Fitur Edit Uraian belum tersedia')),
                                                    );
                                                  },
                                          ),
                                          IconButton(
                                            icon: const Icon(Icons.delete, size: 20),
                                            color: Colors.red,
                                            tooltip: 'Hapus Uraian',
                                            onPressed: provider.btnLoading || kegiatan.isLocked
                                                ? null
                                                : () => _handleDelete(item, provider),
                                          ),
                                          if (kegiatan.isLocked)
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
                                    _formatCurrency(provider.summary.paguuraian),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    provider.summary.fisik.toStringAsFixed(2),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    _formatCurrency(provider.summary.realisasi),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    provider.summary.persen_keuangan.toStringAsFixed(2),
                                    style: const TextStyle(fontWeight: FontWeight.bold),
                                  )),
                                  DataCell(Text(
                                    _formatCurrency(provider.summary.sisa),
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
                                Text('ID: ${item.RKARincID}'),
                                Text('VOLUME: ${item.volume1}'),
                                Text('HARGA SATUAN: ${_formatCurrency(item.harga_satuan1)}'),
                                if (item.created_at != null)
                                  Text('created_at: ${_formatDate(item.created_at)}'),
                                if (item.updated_at != null)
                                  Text('updated_at: ${_formatDate(item.updated_at)}'),
                              ],
                            ),
                          );
                        }),
                    ],
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

