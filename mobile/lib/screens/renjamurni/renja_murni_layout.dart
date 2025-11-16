import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/ui_front_provider.dart';
import '../../utils/app_config.dart';
import '../auth/login_screen.dart';

class RenjaMurniLayout extends StatefulWidget {
  final Widget child;
  final Function(String)? onMenuItemSelected;
  final bool showRightSidebar;
  final bool temporaryLeftSidebar;

  const RenjaMurniLayout({
    super.key,
    required this.child,
    this.onMenuItemSelected,
    this.showRightSidebar = false,
    this.temporaryLeftSidebar = true,
  });

  @override
  State<RenjaMurniLayout> createState() => _RenjaMurniLayoutState();
}

class _RenjaMurniLayoutState extends State<RenjaMurniLayout> {
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();

  String _getPhotoUrl(String? foto) {
    if (foto == null || foto.isEmpty) {
      return '${AppConfig.baseUrl.replaceAll('/v1', '')}/storages/images/users/no_photo.png';
    }
    return '${AppConfig.baseUrl.replaceAll('/v1', '')}/$foto';
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

  Widget _buildMenuItem({
    required String title,
    required IconData icon,
    required String route,
    required bool isActive,
    required VoidCallback onTap,
  }) {
    return ListTile(
      leading: Icon(icon, color: isActive ? Colors.white : Colors.white70),
      title: Text(
        title,
        style: TextStyle(
          color: isActive ? Colors.white : Colors.white70,
          fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
        ),
      ),
      selected: isActive,
      onTap: onTap,
      selectedTileColor: Colors.white.withValues(alpha: 0.1),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Consumer2<AuthProvider, UIFrontProvider>(
      builder: (context, authProvider, uiFrontProvider, _) {
        final user = authProvider.user;

        return Scaffold(
          key: _scaffoldKey,
          body: Column(
            children: [
              // System Bar
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
                leading: IconButton(
                  icon: const Icon(Icons.menu, color: Colors.black87),
                  onPressed: () {
                    _scaffoldKey.currentState?.openDrawer();
                  },
                ),
                title: GestureDetector(
                  onTap: () {
                    // Navigate to dashboard
                    Navigator.of(context).popUntil((route) => route.isFirst);
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
                child: widget.child,
              ),
              // Footer
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
                child: const Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      'SIMONEV 21 (2021-2026).',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.black54,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          // Left Navigation Drawer
          drawer: Drawer(
            backgroundColor: const Color(0xFF385F73),
            child: user != null
                ? ListView(
                    padding: EdgeInsets.zero,
                    children: [
                      // User Profile Header
                      DrawerHeader(
                        decoration: BoxDecoration(
                          color: Colors.blueGrey.shade900,
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            CircleAvatar(
                              radius: 30,
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
                                        fontSize: 24,
                                      ),
                                    )
                                  : null,
                            ),
                            const SizedBox(height: 12),
                            Text(
                              user.username,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              '[${user.defaultRole}]',
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.8),
                                fontSize: 14,
                              ),
                            ),
                          ],
                        ),
                      ),
                      // BOARD DATA RENJA
                      if (user.can('RENJA-GROUP'))
                        _buildMenuItem(
                          title: 'BOARD DATA RENJA',
                          icon: Icons.dashboard,
                          route: '/renjamurni',
                          isActive: true,
                          onTap: () {
                            Navigator.pop(context);
                          },
                        ),
                      // DATA MENTAH
                      if (user.can('RENJA-RKA-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'DATA MENTAH',
                          icon: Icons.storage,
                          route: '/renjamurni/datamentah',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/datamentah');
                            }
                          },
                        ),
                      const Divider(color: Colors.white24),
                      // TRANSAKSI Section
                      const Padding(
                        padding: EdgeInsets.all(16.0),
                        child: Text(
                          'TRANSAKSI',
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      // TARGET KINERJA
                      if (user.can('RENJA-RKA-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'TARGET KINERJA',
                          icon: Icons.track_changes,
                          route: '/renjamurni/targetkinerja',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/targetkinerja');
                            }
                          },
                        ),
                      // RKA MURNI
                      if (user.can('RENJA-RKA-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'RKA MURNI',
                          icon: Icons.account_balance,
                          route: '/renjamurni/rka',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/rka');
                            }
                          },
                        ),
                      // PELAPORAN OPD
                      if (user.can('RENJA-PELAPORAN-OPD_BROWSE'))
                        _buildMenuItem(
                          title: 'PELAPORAN OPD',
                          icon: Icons.description,
                          route: '/renjamurni/pelaporanopd',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/pelaporanopd');
                            }
                          },
                        ),
                      // PROGRES SP2D (only for superadmin)
                      if (user.can('RENJA-PELAPORAN-OPD_BROWSE') && user.isSuperAdmin)
                        _buildMenuItem(
                          title: 'PROGRES SP2D',
                          icon: Icons.trending_up,
                          route: '/renjamurni/progressp2d',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/progressp2d');
                            }
                          },
                        ),
                      const Divider(color: Colors.white24),
                      // LAPORAN Section
                      const Padding(
                        padding: EdgeInsets.all(16.0),
                        child: Text(
                          'LAPORAN',
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      // FORM A
                      if (user.can('RENJA-FORM-A-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'FORM A',
                          icon: Icons.description,
                          route: '/renjamurni/report/forma',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/report/forma');
                            }
                          },
                        ),
                      // FORM B OPD
                      if (user.can('RENJA-FORM-B-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'FORM B OPD',
                          icon: Icons.description,
                          route: '/renjamurni/report/formbopd',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/report/formbopd');
                            }
                          },
                        ),
                      // FORM B UNIT KERJA
                      if (user.can('RENJA-FORM-B-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'FORM B UNIT KERJA',
                          icon: Icons.description,
                          route: '/renjamurni/report/formbunitkerja',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/report/formbunitkerja');
                            }
                          },
                        ),
                      // REALISASI INDIKATOR SUB KEGIATAN
                      if (user.can('RENJA-FORM-B-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'REALISASI INDIKATOR SUB KEGIATAN',
                          icon: Icons.assessment,
                          route: '/renjamurni/report/realisasiindikatorsubkegiatan',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/report/realisasiindikatorsubkegiatan');
                            }
                          },
                        ),
                      // LRA OPD
                      if (user.can('RENJA-FORM-B-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'LRA OPD',
                          icon: Icons.receipt,
                          route: '/renjamurni/report/lraopd',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/report/lraopd');
                            }
                          },
                        ),
                      // REKAP. LRA BELANJA
                      if (user.can('RENJA-FORM-B-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'REKAP. LRA BELANJA',
                          icon: Icons.summarize,
                          route: '/renjamurni/report/rekaplra',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/report/rekaplra');
                            }
                          },
                        ),
                      const Divider(color: Colors.white24),
                      // STATISTIK Section
                      const Padding(
                        padding: EdgeInsets.all(16.0),
                        child: Text(
                          'STATISTIK',
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      // PERINGKAT OPD
                      if (user.can('RENJA-STATISTIK-PERINGKAT-OPD_BROWSE'))
                        _buildMenuItem(
                          title: 'PERINGKAT OPD',
                          icon: Icons.leaderboard,
                          route: '/renjamurni/statistik/peringkatopd',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/statistik/peringkatopd');
                            }
                          },
                        ),
                      // CAPAIAN PER REKENING
                      if (user.can('RENJA-STATISTIK-PERINGKAT-OPD_BROWSE'))
                        _buildMenuItem(
                          title: 'CAPAIAN PER REKENING',
                          icon: Icons.account_tree,
                          route: '/renjamurni/statistik/capaianrek',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/statistik/capaianrek');
                            }
                          },
                        ),
                      const Divider(color: Colors.white24),
                      // SNAPSHOT Section
                      const Padding(
                        padding: EdgeInsets.all(16.0),
                        child: Text(
                          'SNAPSHOT',
                          style: TextStyle(
                            color: Colors.white70,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      // SNAPSHOT RKA
                      if (user.can('RENJA-SNAPSHOT-RKA-MURNI_BROWSE'))
                        _buildMenuItem(
                          title: 'SNAPSHOT RKA',
                          icon: Icons.camera_alt,
                          route: '/renjamurni/snapshot/rka',
                          isActive: false,
                          onTap: () {
                            Navigator.pop(context);
                            if (widget.onMenuItemSelected != null) {
                              widget.onMenuItemSelected!('/renjamurni/snapshot/rka');
                            }
                          },
                        ),
                    ],
                  )
                : const Center(
                    child: Text(
                      'User tidak ditemukan',
                      style: TextStyle(color: Colors.white70),
                    ),
                  ),
          ),
        );
      },
    );
  }
}

