import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Modern Color Palette - Premium & Professional
  static const Color primary = Color(
    0xFF6C63FF,
  ); // Vibrant Purple from image card
  static const Color primaryDark = Color(0xFF5A52D5);
  static const Color primaryLight = Color(0xFFEEF2FF); // Very light purple tint

  static const Color secondary = Color(
    0xFF4DB6AC,
  ); // Teal/Cyan from History icon
  static const Color accent = Color(
    0xFFFFB74D,
  ); // Orange from Shift Change icon
  static const Color info = Color(0xFF4FC3F7); // Light Blue from List icon
  static const Color pink = Color(0xFFF06292); // Pink from Contact HR

  static const Color success = Color(0xFF10B981);
  static const Color warning = Color(0xFFF59E0B);
  static const Color error = Color(0xFFEF4444);

  // Light Mode Colors
  static const Color background = Color(
    0xFFF4F6F8,
  ); // Light Blue-Grey Background
  static const Color surface = Colors.white;
  static const Color textMain = Color(0xFF2D3748); // Dark Blue-Grey
  static const Color textSub = Color(0xFF718096); // Medium Grey
  static const Color border = Color(0xFFE2E8F0);

  // Dark Mode Colors
  static const Color backgroundDark = Color(0xFF0F172A); // Slate 900
  static const Color surfaceDark = Color(0xFF1E293B); // Slate 800
  static const Color textMainDark = Color(0xFFF8FAFC); // Slate 50
  static const Color textSubDark = Color(0xFF94A3B8); // Slate 400
  static const Color borderDark = Color(0xFF334155); // Slate 700
  static const Color inputFillDark = Color(0xFF334155); // Slate 700

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
      brightness: Brightness.light,

      // Typography
      fontFamily: GoogleFonts.kanit().fontFamily,
      textTheme: GoogleFonts.kanitTextTheme().copyWith(
        // Headings - Kanit Bold (700)
        displayLarge: GoogleFonts.kanit(
          fontSize: 32,
          fontWeight: FontWeight.w700,
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
          fontWeight: FontWeight.w700,
          color: textMain,
        ),
        titleMedium: GoogleFonts.kanit(
          fontSize: 16,
          fontWeight: FontWeight.w700,
          color: textMain,
        ),

        // Body - Sarabun
        bodyLarge: GoogleFonts.sarabun(
          fontSize: 16,
          fontWeight: FontWeight.w400, // Regular
          color: textMain,
          height: 1.5,
        ),
        bodyMedium: GoogleFonts.sarabun(
          fontSize: 14,
          fontWeight: FontWeight.w400, // Regular
          color: textSub,
          height: 1.5,
        ),
        bodySmall: GoogleFonts.sarabun(
          fontSize: 12,
          fontWeight: FontWeight.w300, // Light for helper/caption
          color: textSub,
        ),

        // Buttons / Labels
        labelLarge: GoogleFonts.kanit(
          fontSize: 14,
          fontWeight: FontWeight.w600, // SemiBold
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
        labelStyle: GoogleFonts.sarabun(
          color: textSub,
          fontWeight: FontWeight.w400, // Regular
        ),
        hintStyle: GoogleFonts.sarabun(
          color: const Color(0xFF94A3B8),
          fontWeight: FontWeight.w300, // Light (Helper text)
        ),
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
                fontWeight: FontWeight.w600, // SemiBold
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
            fontWeight: FontWeight.w600, // SemiBold
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

  static ThemeData get darkTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: primary,
        primary: primary,
        secondary: secondary,
        surface: surfaceDark,
        background: backgroundDark,
        error: error,
        brightness: Brightness.dark,
      ),
      scaffoldBackgroundColor: backgroundDark,
      brightness: Brightness.dark,

      // Typography
      fontFamily: GoogleFonts.kanit().fontFamily,
      textTheme: GoogleFonts.kanitTextTheme(ThemeData.dark().textTheme)
          .copyWith(
            // Headings - Kanit Bold (700)
            displayLarge: GoogleFonts.kanit(
              fontSize: 32,
              fontWeight: FontWeight.w700,
              color: textMainDark,
              letterSpacing: -0.5,
            ),
            displayMedium: GoogleFonts.kanit(
              fontSize: 28,
              fontWeight: FontWeight.w700,
              color: textMainDark,
            ),
            headlineSmall: GoogleFonts.kanit(
              fontSize: 24,
              fontWeight: FontWeight.w700,
              color: textMainDark,
            ),
            titleLarge: GoogleFonts.kanit(
              fontSize: 20,
              fontWeight: FontWeight.w700,
              color: textMainDark,
            ),
            titleMedium: GoogleFonts.kanit(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: textMainDark,
            ),

            // Body - Sarabun
            bodyLarge: GoogleFonts.sarabun(
              fontSize: 16,
              fontWeight: FontWeight.w400, // Regular
              color: textMainDark,
              height: 1.5,
            ),
            bodyMedium: GoogleFonts.sarabun(
              fontSize: 14,
              fontWeight: FontWeight.w400, // Regular
              color: textSubDark,
              height: 1.5,
            ),
            bodySmall: GoogleFonts.sarabun(
              fontSize: 12,
              fontWeight: FontWeight.w300, // Light for helper/caption
              color: textSubDark,
            ),

            // Buttons / Labels
            labelLarge: GoogleFonts.kanit(
              fontSize: 14,
              fontWeight: FontWeight.w600, // SemiBold
              color: textMainDark,
            ),
          ),

      cardTheme: CardThemeData(
        color: surfaceDark,
        elevation: 0,
        margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: const BorderSide(color: borderDark, width: 1),
        ),
        clipBehavior: Clip.antiAlias,
      ),

      // Input Decoration
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: inputFillDark,
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 20,
          vertical: 18,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: borderDark, width: 1),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: borderDark, width: 1),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: error, width: 1),
        ),
        labelStyle: GoogleFonts.sarabun(color: textSubDark),
        hintStyle: GoogleFonts.sarabun(color: textSubDark.withOpacity(0.5)),
        prefixIconColor: textSubDark,
        suffixIconColor: textSubDark,
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
                fontWeight: FontWeight.w600, // SemiBold
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
            fontWeight: FontWeight.w600, // SemiBold
          ),
        ),
      ),

      // AppBar Theme
      appBarTheme: AppBarTheme(
        backgroundColor: surfaceDark,
        foregroundColor: textMainDark,
        elevation: 0,
        centerTitle: true,
        surfaceTintColor: Colors.transparent,
        titleTextStyle: GoogleFonts.kanit(
          fontSize: 18,
          fontWeight: FontWeight.w700,
          color: textMainDark,
        ),
        iconTheme: const IconThemeData(color: textMainDark, size: 24),
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
