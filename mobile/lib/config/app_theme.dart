import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Modern Color Palette
  static const Color primary = Color(0xFF4F46E5); // Indigo 600
  static const Color primaryDark = Color(0xFF4338CA); // Indigo 700
  static const Color secondary = Color(0xFF0EA5E9); // Sky 500
  static const Color accent = Color(0xFFF59E0B); // Amber 500
  static const Color background = Color(0xFFF3F4F6); // Gray 100
  static const Color surface = Colors.white;
  static const Color error = Color(0xFFEF4444); // Red 500
  static const Color textMain = Color(0xFF1F2937); // Gray 800
  static const Color textSub = Color(0xFF6B7280); // Gray 500

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primary,
        primary: primary,
        secondary: secondary,
        surface: surface,
        background: background,
        error: error,
      ),
      scaffoldBackgroundColor: background,

      // Typography
      fontFamily: GoogleFonts.sarabun().fontFamily,
      textTheme: TextTheme(
        displayLarge: GoogleFonts.sarabun(
          fontSize: 32,
          fontWeight: FontWeight.bold,
          color: textMain,
        ),
        headlineSmall: GoogleFonts.sarabun(
          fontSize: 24,
          fontWeight: FontWeight.bold,
          color: textMain,
        ),
        titleLarge: GoogleFonts.sarabun(
          fontSize: 20,
          fontWeight: FontWeight.w600,
          color: textMain,
        ),
        bodyLarge: GoogleFonts.sarabun(fontSize: 16, color: textMain),
        bodyMedium: GoogleFonts.sarabun(fontSize: 14, color: textSub),
      ),

      cardTheme: CardThemeData(
        color: surface,
        elevation:
            0, // Flat with border or soft shadow via Container decoration preferred
        margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        clipBehavior: Clip.antiAlias,
      ),

      // Input Decoration
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.grey[50], // Very light gray
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 20,
          vertical: 16,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primary, width: 2),
        ),
        labelStyle: TextStyle(color: textSub),
      ),

      // Button Theme
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primary,
          foregroundColor: Colors.white,
          elevation: 2,
          padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 24),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: GoogleFonts.sarabun(
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),

      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: primary,
          side: const BorderSide(color: primary),
          padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 24),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: GoogleFonts.sarabun(
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),

      // AppBar Theme
      appBarTheme: AppBarTheme(
        backgroundColor: surface,
        foregroundColor: textMain,
        elevation: 0,
        centerTitle: true,
        titleTextStyle: GoogleFonts.sarabun(
          fontSize: 18,
          fontWeight: FontWeight.bold,
          color: textMain,
        ),
        iconTheme: const IconThemeData(color: textMain),
      ),
    );
  }
}
