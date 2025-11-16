import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/ui_front_provider.dart';
import '../../utils/app_config.dart';
import '../auth/login_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  @override
  void initState() {
    super.initState();
    // Load UI frontend settings untuk mendapatkan bulan realisasi dan masa pelaporan
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final uiFrontProvider =
          Provider.of<UIFrontProvider>(context, listen: false);
      if (uiFrontProvider.bulanRealisasi == null) {
        uiFrontProvider.init();
      }
    });
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Konfirmasi Logout'),
        content: const Text('Apakah Anda yakin ingin keluar?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Logout'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      await authProvider.logout();

      if (mounted) {
        Navigator.of(context).pushAndRemoveUntil(
          MaterialPageRoute(builder: (_) => const LoginScreen()),
          (route) => false,
        );
      }
    }
  }

  String _getPhotoUrl(String? foto) {
    if (foto == null || foto.isEmpty) {
      return '${AppConfig.baseUrl.replaceAll('/v1', '')}/storages/images/users/no_photo.png';
    }
    return '${AppConfig.baseUrl.replaceAll('/v1', '')}/$foto';
  }

  Widget _buildModuleCard({
    required String title,
    required IconData icon,
    required VoidCallback? onTap,
    bool disabled = false,
  }) {
    return Card(
      elevation: 0,
      color: const Color(0xFF385F73),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
      ),
      child: InkWell(
        onTap: disabled ? null : onTap,
        borderRadius: BorderRadius.circular(8),
        child: Opacity(
          opacity: disabled ? 0.5 : 1.0,
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const SizedBox(height: 8),
                Container(
                  width: 64,
                  height: 64,
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    icon,
                    color: const Color(0xFFDA4453),
                    size: 32,
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  title,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Consumer2<AuthProvider, UIFrontProvider>(
        builder: (context, authProvider, uiFrontProvider, child) {
          final user = authProvider.user;

          return Column(
            children: [
              // System Bar (mirip dengan DashboardAdmin.vue)
              Container(
                height: 32,
                color: Colors.blueGrey.shade900,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: const Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [],
                ),
              ),
              // App Bar
              AppBar(
                backgroundColor: const Color(0xFFE6E9ED),
                elevation: 0,
                title: GestureDetector(
                  onTap: () {
                    // Navigate to dashboard (reload)
                    setState(() {});
                  },
                  child: const Text(
                    'SIMONEV 21',
                    style: TextStyle(
                      color: Colors.black87,
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                actions: [
                  // User Avatar with Menu
                  if (user != null)
                    PopupMenuButton<String>(
                      icon: CircleAvatar(
                        radius: 16,
                        backgroundColor: Colors.grey.shade300,
                        backgroundImage: user.foto != null && user.foto!.isNotEmpty
                            ? NetworkImage(_getPhotoUrl(user.foto))
                            : null,
                        child: user.foto == null || user.foto!.isEmpty
                            ? Text(
                                user.name.isNotEmpty
                                    ? user.name[0].toUpperCase()
                                    : 'U',
                                style: const TextStyle(
                                  color: Colors.black87,
                                  fontSize: 14,
                                ),
                              )
                            : null,
                      ),
                      onSelected: (value) {
                        if (value == 'profile') {
                          Navigator.pushNamed(context, '/profile');
                        } else if (value == 'logout') {
                          _handleLogout();
                        }
                      },
                      itemBuilder: (context) => [
                        PopupMenuItem(
                          value: 'profile',
                          child: Row(
                            children: [
                              CircleAvatar(
                                radius: 20,
                                backgroundColor: Colors.grey.shade300,
                                backgroundImage: user.foto != null && user.foto!.isNotEmpty
                                    ? NetworkImage(_getPhotoUrl(user.foto))
                                    : null,
                                child: user.foto == null || user.foto!.isEmpty
                                    ? Text(
                                        user.name.isNotEmpty
                                            ? user.name[0].toUpperCase()
                                            : 'U',
                                        style: const TextStyle(
                                          color: Colors.black87,
                                          fontSize: 16,
                                        ),
                                      )
                                    : null,
                              ),
                              const SizedBox(width: 12),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Text(
                                    user.username,
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  Text(
                                    '[${user.defaultRole}]',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Colors.grey.shade600,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        const PopupMenuDivider(),
                        const PopupMenuItem(
                          value: 'profile',
                          child: Row(
                            children: [
                              Icon(Icons.person, size: 20),
                              SizedBox(width: 12),
                              Text('Profil'),
                            ],
                          ),
                        ),
                        const PopupMenuDivider(),
                        const PopupMenuItem(
                          value: 'logout',
                          child: Row(
                            children: [
                              Icon(Icons.power_settings_new, size: 20),
                              SizedBox(width: 12),
                              Text('Logout'),
                            ],
                          ),
                        ),
                      ],
                    ),
                ],
              ),
              // Main Content
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: user != null
                      ? Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // System Bar (mirip dengan DashboardAdmin.vue)
                            Container(
                              width: double.infinity,
                              decoration: BoxDecoration(
                                color: Colors.white,
                                border: Border.all(
                                  color: Colors.grey.shade300,
                                  width: 1,
                                ),
                                borderRadius: BorderRadius.circular(4),
                              ),
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  // Hak Akses
                                  RichText(
                                    text: TextSpan(
                                      style: TextStyle(
                                        color: Colors.grey.shade800,
                                        fontSize: 12,
                                      ),
                                      children: [
                                        const TextSpan(
                                          text: 'Hak Akses: ',
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        TextSpan(
                                          text: user.defaultRole.toUpperCase(),
                                        ),
                                      ],
                                    ),
                                  ),
                                  // Tahun Anggaran
                                  RichText(
                                    text: TextSpan(
                                      style: TextStyle(
                                        color: Colors.grey.shade800,
                                        fontSize: 12,
                                      ),
                                      children: [
                                        const TextSpan(
                                          text: 'Tahun Anggaran: ',
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        TextSpan(
                                          text: user.tahunSelected ?? '-',
                                        ),
                                      ],
                                    ),
                                  ),
                                  // Bulan Realisasi
                                  RichText(
                                    text: TextSpan(
                                      style: TextStyle(
                                        color: Colors.grey.shade800,
                                        fontSize: 12,
                                      ),
                                      children: [
                                        const TextSpan(
                                          text: 'Bulan Realisasi: ',
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        TextSpan(
                                          text: uiFrontProvider.getNamaBulan(uiFrontProvider.bulanRealisasi),
                                        ),
                                      ],
                                    ),
                                  ),
                                  // APBD
                                  RichText(
                                    text: TextSpan(
                                      style: TextStyle(
                                        color: Colors.grey.shade800,
                                        fontSize: 12,
                                      ),
                                      children: [
                                        const TextSpan(
                                          text: 'APBD: ',
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        TextSpan(
                                          text: uiFrontProvider.masaPelaporan?.toUpperCase() ?? '-',
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(height: 16),
                            GridView.count(
                          crossAxisCount: 2,
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 16,
                          childAspectRatio: 0.85,
                          children: [
                            // DATA MASTER
                            if (user.can('DMASTER-GROUP'))
                              _buildModuleCard(
                                title: 'DATA MASTER',
                                icon: Icons.business_center,
                                onTap: () {
                                  // TODO: Navigate to DATA MASTER
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text('DATA MASTER - Coming Soon'),
                                    ),
                                  );
                                },
                              ),
                            // RKPD MURNI
                            if (user.can('RKPD-GROUP'))
                              _buildModuleCard(
                                title: 'RKPD MURNI',
                                icon: Icons.assignment,
                                onTap: () {
                                  // TODO: Navigate to RKPD MURNI
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text('RKPD MURNI - Coming Soon'),
                                    ),
                                  );
                                },
                              ),
                            // RENJA MURNI
                            if (user.can('RENJA-GROUP'))
                              _buildModuleCard(
                                title: 'RENJA MURNI',
                                icon: Icons.work,
                                onTap: () {
                                  Navigator.pushNamed(context, '/renjamurni');
                                },
                              ),
                            // RENJA PERUBAHAN
                            if (user.can('RENJA-GROUP'))
                              _buildModuleCard(
                                title: 'RENJA PERUBAHAN',
                                icon: Icons.edit_document,
                                onTap: uiFrontProvider.masaPelaporan == 'murni'
                                    ? null
                                    : () {
                                        // TODO: Navigate to RENJA PERUBAHAN
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(
                                          const SnackBar(
                                            content: Text(
                                                'RENJA PERUBAHAN - Coming Soon'),
                                          ),
                                        );
                                      },
                                disabled: uiFrontProvider.masaPelaporan == 'murni',
                              ),
                            // DAK MURNI
                            if (user.can('DAK-GROUP'))
                              _buildModuleCard(
                                title: 'DAK MURNI',
                                icon: Icons.account_balance,
                                onTap: () {
                                  // TODO: Navigate to DAK MURNI
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text('DAK MURNI - Coming Soon'),
                                    ),
                                  );
                                },
                              ),
                            // GALLERY PEMBANGUNAN (no permission check)
                            _buildModuleCard(
                              title: 'GALLERY PEMBANGUNAN',
                              icon: Icons.photo_library,
                              onTap: () {
                                // TODO: Navigate to GALLERY PEMBANGUNAN
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text(
                                        'GALLERY PEMBANGUNAN - Coming Soon'),
                                  ),
                                );
                              },
                            ),
                            // USER SISTEM
                            if (user.can('SYSTEM-USERS-GROUP'))
                              _buildModuleCard(
                                title: 'USER SISTEM',
                                icon: Icons.people,
                                onTap: () {
                                  // TODO: Navigate to USER SISTEM
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text('USER SISTEM - Coming Soon'),
                                    ),
                                  );
                                },
                              ),
                            // KONFIGURASI SISTEM
                            if (user.can('SYSTEM-SETTING-GROUP'))
                              _buildModuleCard(
                                title: 'KONFIGURASI SISTEM',
                                icon: Icons.settings,
                                onTap: () {
                                  // TODO: Navigate to KONFIGURASI SISTEM
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                          'KONFIGURASI SISTEM - Coming Soon'),
                                    ),
                                  );
                                },
                              ),
                            ],
                            ),
                          ],
                        )
                      : const Center(
                          child: Text('User tidak ditemukan'),
                        ),
                ),
              ),
              // Footer (mirip dengan DashboardAdmin.vue)
              Container(
                padding: const EdgeInsets.symmetric(vertical: 8),
                decoration: BoxDecoration(
                  border: Border(
                    top: BorderSide(
                      color: Colors.grey.shade300,
                      width: 1,
                    ),
                  ),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Text(
                      'SIMONEV 21 (2021-2026).',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.black54,
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.code, size: 20),
                      onPressed: () {
                        // TODO: Open GitHub link
                      },
                      padding: EdgeInsets.zero,
                      constraints: const BoxConstraints(),
                    ),
                  ],
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}
