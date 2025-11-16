import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'providers/auth_provider.dart';
import 'providers/dashboard_provider.dart';
import 'providers/ui_front_provider.dart';
import 'providers/renja_murni_provider.dart';
import 'providers/rka_murni_provider.dart';
import 'services/api_service.dart';
import 'services/storage_service.dart';
import 'screens/splash_screen.dart';
import 'screens/auth/login_screen.dart';
import 'screens/home/dashboard_screen.dart';
import 'screens/profile/profile_screen.dart';
import 'screens/renjamurni/renja_murni_screen.dart';
import 'screens/renjamurni/rka_murni_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await StorageService.init();
  
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(
          create: (_) => AuthProvider(
            apiService: ApiService(),
            storageService: StorageService(),
          ),
        ),
        ChangeNotifierProvider(
          create: (_) => UIFrontProvider(apiService: ApiService()),
        ),
        ChangeNotifierProxyProvider<AuthProvider, DashboardProvider>(
          create: (_) => DashboardProvider(apiService: ApiService()),
          update: (_, authProvider, previous) =>
              previous ?? DashboardProvider(apiService: ApiService())
                ..setAuthProvider(authProvider),
        ),
        ChangeNotifierProvider(
          create: (_) => RenjaMurniProvider(apiService: ApiService()),
        ),
        ChangeNotifierProvider(
          create: (_) => RKAMurniProvider(apiService: ApiService()),
        ),
      ],
      child: MaterialApp(
        title: 'SIMONEV 21',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          primarySwatch: Colors.blue,
          primaryColor: const Color(0xFF1976D2),
          scaffoldBackgroundColor: const Color(0xFFF5F5F5),
          appBarTheme: const AppBarTheme(
            backgroundColor: Color(0xFF1976D2),
            foregroundColor: Colors.white,
            elevation: 0,
          ),
          cardTheme: CardThemeData(
            elevation: 2,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
          inputDecorationTheme: InputDecorationTheme(
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(8),
            ),
            filled: true,
            fillColor: Colors.white,
          ),
        ),
        home: const SplashScreen(),
        routes: {
          '/login': (context) => const LoginScreen(),
          '/dashboard': (context) => const DashboardScreen(),
          '/profile': (context) => const ProfileScreen(),
          '/renjamurni': (context) => const RenjaMurniScreen(),
          '/renjamurni/rka': (context) => const RKAMurniScreen(),
        },
      ),
    );
  }
}

