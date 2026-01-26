import 'package:flutter/material.dart';
import '../config/app_theme.dart';

class AnimatedBackground extends StatefulWidget {
  final Widget? child;

  const AnimatedBackground({super.key, this.child});

  @override
  State<AnimatedBackground> createState() => _AnimatedBackgroundState();
}

class _AnimatedBackgroundState extends State<AnimatedBackground>
    with SingleTickerProviderStateMixin {
  late AnimationController _pulseController;
  late Animation<double> _pulseAnimation;
  final ScrollController _scrollController =
      ScrollController(); // Dummy for now if needed

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2000),
    )..repeat(reverse: true);

    _pulseAnimation = Tween<double>(begin: 1.0, end: 1.05).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _pulseController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    return Stack(
      children: [
        // Base gradient
        Container(
          height: size.height,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Color(0xFFF8FAFF), Color(0xFFFFFFFF), Color(0xFFF0F4FF)],
              stops: [0.0, 0.5, 1.0],
            ),
          ),
        ),

        // Animated blob 1
        AnimatedBuilder(
          animation: _pulseAnimation,
          builder: (context, child) {
            return Positioned(
              top: -80,
              right: -60,
              child: Transform.scale(
                scale: _pulseAnimation.value,
                child: Container(
                  width: 280,
                  height: 280,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: RadialGradient(
                      colors: [
                        AppTheme.primary.withOpacity(0.15),
                        AppTheme.primary.withOpacity(0.05),
                        Colors.transparent,
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        ),

        // Animated blob 2
        AnimatedBuilder(
          animation: _pulseAnimation,
          builder: (context, child) {
            return Positioned(
              top: size.height * 0.4,
              left: -100,
              child: Transform.scale(
                scale: 2 - _pulseAnimation.value,
                child: Container(
                  width: 250,
                  height: 250,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: RadialGradient(
                      colors: [
                        const Color(0xFF6366F1).withOpacity(0.1),
                        const Color(0xFF6366F1).withOpacity(0.03),
                        Colors.transparent,
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        ),

        // Animated blob 3 (Bottom)
        Positioned(
          bottom: -50,
          right: -50,
          child: Container(
            width: 300,
            height: 300,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppTheme.secondary.withOpacity(0.03),
            ),
          ),
        ),

        // Mesh gradient overlay
        Positioned(
          bottom: 0,
          left: 0,
          right: 0,
          child: Container(
            height: 100,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  Colors.transparent,
                  const Color(0xFFEEF2FF).withOpacity(0.3),
                ],
              ),
            ),
          ),
        ),

        if (widget.child != null) SafeArea(child: widget.child!),
      ],
    );
  }
}
