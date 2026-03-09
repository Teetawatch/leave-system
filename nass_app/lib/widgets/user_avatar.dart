import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../config/api_config.dart';
import '../config/app_theme.dart';

class UserAvatar extends StatelessWidget {
  final String? imageUrl;
  final String name;
  final double radius;
  final Color? backgroundColor;
  final Color? textColor;
  final BorderRadius? borderRadius;

  const UserAvatar({
    super.key,
    required this.name,
    this.imageUrl,
    this.radius = 22,
    this.backgroundColor,
    this.textColor,
    this.borderRadius,
  });

  String get _initials {
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return name.isNotEmpty ? name[0].toUpperCase() : 'U';
  }

  Widget _buildFallback(Color bgColor, Color fgColor) {
    return Container(
      width: radius * 2,
      height: radius * 2,
      decoration: BoxDecoration(
        color: bgColor,
        shape: borderRadius != null ? BoxShape.rectangle : BoxShape.circle,
        borderRadius: borderRadius,
      ),
      child: Center(
        child: Text(
          _initials,
          style: GoogleFonts.prompt(
            color: fgColor,
            fontSize: radius * 0.65,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final bgColor = backgroundColor ?? AppTheme.primary.withValues(alpha: 0.12);
    final fgColor = textColor ?? AppTheme.primary;
    final size = radius * 2;

    final normalizedUrl = ApiConfig.normalizeStorageUrl(imageUrl);
    if (normalizedUrl == null) {
      return _buildFallback(bgColor, fgColor);
    }

    final shape = borderRadius != null
        ? ClipRRect(borderRadius: borderRadius!, child: _buildImage(size, bgColor, fgColor, normalizedUrl))
        : ClipOval(child: _buildImage(size, bgColor, fgColor, normalizedUrl));

    return SizedBox(width: size, height: size, child: shape);
  }

  Widget _buildImage(double size, Color bgColor, Color fgColor, String url) {
    return CachedNetworkImage(
      imageUrl: url,
      width: size,
      height: size,
      fit: BoxFit.cover,
      httpHeaders: const {
        'Accept': 'image/webp,image/apng,image/*,*/*;q=0.8',
      },
      placeholder: (_, __) => _buildFallback(bgColor, fgColor),
      errorWidget: (_, __, ___) => _buildFallback(bgColor, fgColor),
    );
  }
}
