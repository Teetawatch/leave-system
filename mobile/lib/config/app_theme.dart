import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Modern Color Palette - Premium & Professional
  static const Color primary = Color(0xFF6366F1); // Indigo 500
  static const Color primaryDark = Color(0xFF4338CA); // Indigo 700
  static const Color primaryLight = Color(0xFFEEF2FF); // Indigo 50

  static const Color secondary = Color(0xFF06B6D4); // Cyan 500
  static const Color accent = Color(0xFFF59E0B); // Amber 500

  static const Color success = Color(0xFF10B981); // Emerald 500
  static const Color warning = Color(0xFFFBBF24); // Amber 400
  static const Color error = Color(0xFFEF4444); // Red 500
  static const Color info = Color(0xFF3B82F6); // Blue 500

  static const Color background = Color(0xFFF8FAFC); // Slate 50
  static const Color surface = Colors.white;
  static const Color textMain = Color(0xFF0F172A); // Slate 900
  static const Color textSub = Color(0xFF64748B); // Slate 500
  static const Color border = Color(0xFFE2E8F0); // Slate 200

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

      // Typography - Using Kanit for modern Thai support
      fontFamily: GoogleFonts.kanit().fontFamily,
      textTheme: TextTheme(
        displayLarge: GoogleFonts.kanit(
          fontSize: 32,
          fontWeight: FontWeight.w800,
          color: textMain,
          letterSpacing: -0.5,
        ),
        displayMedium: GoogleFonts.kanit(
          fontSize: 28,
          fontWeight: FontWeight.w700,
          color: textMain,
        ),
        headlineSmall: GoogleFonts.kanit(
          fontSize: 24,
          fontWeight: FontWeight.w700,
          color: textMain,
        ),
        titleLarge: GoogleFonts.kanit(
          fontSize: 20,
          fontWeight: FontWeight.w600,
          color: textMain,
        ),
        titleMedium: GoogleFonts.kanit(
          fontSize: 16,
          fontWeight: FontWeight.w600,
          color: textMain,
        ),
        bodyLarge: GoogleFonts.kanit(
          fontSize: 16,
          color: textMain,
          height: 1.5,
        ),
        bodyMedium: GoogleFonts.kanit(
          fontSize: 14,
          color: textSub,
          height: 1.5,
        ),
        labelLarge: GoogleFonts.kanit(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: textMain,
        ),
      ),

      cardTheme: CardThemeData(
        color: surface,
        elevation: 0,
        margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: const BorderSide(color: border, width: 1),
        ),
        clipBehavior: Clip.antiAlias,
      ),

      // Input Decoration - Premium Look
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 20,
          vertical: 18,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: border, width: 1),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: border, width: 1),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: error, width: 1),
        ),
        labelStyle: const TextStyle(
          color: textSub,
          fontWeight: FontWeight.w500,
        ),
        hintStyle: const TextStyle(color: Color(0xFF94A3B8)),
        prefixIconColor: textSub,
        suffixIconColor: textSub,
      ),

      // Button Themes
      elevatedButtonTheme: ElevatedButtonThemeData(
        style:
            ElevatedButton.styleFrom(
              backgroundColor: primary,
              foregroundColor: Colors.white,
              elevation: 0,
              padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 24),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              textStyle: GoogleFonts.kanit(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                letterSpacing: 0.5,
              ),
            ).copyWith(
              overlayColor: WidgetStateProperty.all(
                Colors.white.withOpacity(0.1),
              ),
            ),
      ),

      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: primary,
          side: const BorderSide(color: primary, width: 1.5),
          padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 24),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          textStyle: GoogleFonts.kanit(
            fontSize: 16,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),

      // AppBar Theme
      appBarTheme: AppBarTheme(
        backgroundColor: surface,
        foregroundColor: textMain,
        elevation: 0,
        centerTitle: true,
        surfaceTintColor: Colors.transparent,
        titleTextStyle: GoogleFonts.kanit(
          fontSize: 18,
          fontWeight: FontWeight.w700,
          color: textMain,
        ),
        iconTheme: const IconThemeData(color: textMain, size: 24),
      ),

      // Floating Action Button
      floatingActionButtonTheme: FloatingActionButtonThemeData(
        backgroundColor: primary,
        foregroundColor: Colors.white,
        elevation: 4,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      ),
    );
  }
}
