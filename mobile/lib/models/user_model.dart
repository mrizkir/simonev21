class UserModel {
  final String id;
  final String username;
  final String name;
  final String? email;
  final String? nomorHp;
  final String? foto;
  final List<String> roles;
  final bool isSuperAdmin;
  final Map<String, dynamic> permissions;
  final String? tahunSelected; // Tahun anggaran yang dipilih saat login

  UserModel({
    required this.id,
    required this.username,
    required this.name,
    this.email,
    this.nomorHp,
    this.foto,
    required this.roles,
    required this.isSuperAdmin,
    required this.permissions,
    this.tahunSelected,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id']?.toString() ?? '',
      username: json['username'] ?? '',
      name: json['name'] ?? '',
      email: json['email'],
      nomorHp: json['nomor_hp'],
      foto: json['foto'],
      roles: json['role'] != null
          ? (json['role'] is List
              ? List<String>.from(json['role'])
              : [json['role'].toString()])
          : [],
      isSuperAdmin: json['issuperadmin'] ?? false,
      permissions: _parsePermissions(json['permissions']),
      tahunSelected: json['tahun_selected']?.toString(),
    );
  }

  // Helper method untuk parse permissions (bisa List atau Map)
  static Map<String, dynamic> _parsePermissions(dynamic permissionsData) {
    if (permissionsData == null) {
      return {};
    }
    
    if (permissionsData is Map) {
      return Map<String, dynamic>.from(permissionsData);
    }
    
    if (permissionsData is List) {
      // Jika List, convert ke Map dengan index sebagai key
      // Atau bisa juga di-skip jika tidak diperlukan
      return {};
    }
    
    return {};
  }

  // Helper method untuk check permission (mirip dengan Vue can())
  bool can(String permission) {
    if (isSuperAdmin) {
      return true; // Super admin bisa akses semua
    }
    
    // Check di permissions map
    if (permissions.containsKey(permission)) {
      final perm = permissions[permission];
      if (perm is bool) {
        return perm;
      }
      if (perm == 1 || perm == '1' || perm == 'true') {
        return true;
      }
    }
    
    // Check berdasarkan role
    // Format permission biasanya: 'GROUP-NAME-GROUP'
    // Role biasanya: 'GROUP-NAME' atau 'GROUP-NAME-GROUP'
    for (final role in roles) {
      final normalizedRole = role.toUpperCase().replaceAll(' ', '-');
      final normalizedPerm = permission.toUpperCase().replaceAll(' ', '-');
      
      // Jika role sama dengan permission (tanpa '-GROUP' di akhir)
      if (normalizedRole == normalizedPerm || 
          normalizedRole == normalizedPerm.replaceAll('-GROUP', '') ||
          normalizedPerm.contains(normalizedRole)) {
        return true;
      }
    }
    
    return false;
  }

  // Get default role
  String get defaultRole {
    if (roles.isNotEmpty) {
      return roles.first;
    }
    return 'user';
  }

  // Get all roles as string
  String get rolesString {
    return roles.join(', ');
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'username': username,
      'name': name,
      'email': email,
      'nomor_hp': nomorHp,
      'foto': foto,
      'role': roles,
      'issuperadmin': isSuperAdmin,
      'permissions': permissions,
      'tahun_selected': tahunSelected,
    };
  }
}

