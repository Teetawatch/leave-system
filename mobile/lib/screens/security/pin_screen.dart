import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/security_provider.dart';
import '../../config/app_theme.dart';

enum PinMode { setup, verify, disable }

class PinScreen extends StatefulWidget {
  final PinMode mode;
  final VoidCallback? onSuccess;
  final VoidCallback? onCancel;

  const PinScreen({
    super.key,
    required this.mode,
    this.onSuccess,
    this.onCancel,
  });

  @override
  State<PinScreen> createState() => _PinScreenState();
}

class _PinScreenState extends State<PinScreen> {
  String _pin = '';
  String _confirmPin = '';
  bool _isConfirming = false;
  String _message = 'กรุณากรอกรหัส PIN 6 หลัก';

  @override
  void initState() {
    super.initState();
    _updateMessage();
    if (widget.mode == PinMode.verify) {
      _tryBiometric();
    }
  }

  void _updateMessage() {
    setState(() {
      if (widget.mode == PinMode.setup) {
        if (_isConfirming) {
          _message = 'ยืนยันรหัส PIN อีกครั้ง';
        } else {
          _message = 'ตั้งรหัส PIN ใหม่ 6 หลัก';
        }
      } else if (widget.mode == PinMode.verify) {
        _message = 'กรุณากรอกรหัส PIN เพื่อเข้าใช้งาน';
      } else if (widget.mode == PinMode.disable) {
        _message = 'กรุณากรอกรหัส PIN เดิมเพื่อยกเลิก';
      }
    });
  }

  Future<void> _tryBiometric() async {
    final securityProvider = Provider.of<SecurityProvider>(
      context,
      listen: false,
    );
    if (securityProvider.isBiometricEnabled) {
      bool success = await securityProvider.authenticateWithBiometrics();
      if (success) {
        widget.onSuccess?.call();
      }
    }
  }

  void _onKeyPressed(String value) {
    if (_pin.length < 6) {
      setState(() {
        _pin += value;
      });
      if (_pin.length == 6) {
        _handlePinComplete();
      }
    }
  }

  void _onDeletePressed() {
    if (_pin.isNotEmpty) {
      setState(() {
        _pin = _pin.substring(0, _pin.length - 1);
      });
    }
  }

  Future<void> _handlePinComplete() async {
    final securityProvider = Provider.of<SecurityProvider>(
      context,
      listen: false,
    );

    switch (widget.mode) {
      case PinMode.verify:
      case PinMode.disable:
        bool valid = await securityProvider.verifyPin(_pin);
        if (valid) {
          if (widget.mode == PinMode.disable) {
            await securityProvider.removePin();
          }
          widget.onSuccess?.call();
        } else {
          _shakeError();
        }
        break;

      case PinMode.setup:
        if (!_isConfirming) {
          setState(() {
            _confirmPin = _pin;
            _pin = '';
            _isConfirming = true;
            _updateMessage();
          });
        } else {
          if (_pin == _confirmPin) {
            await securityProvider.setPin(_pin);
            widget.onSuccess?.call();
          } else {
            _shakeError(resetConfirm: true);
          }
        }
        break;
    }
  }

  void _shakeError({bool resetConfirm = false}) {
    // Simple visual feedback: clear pin and show error temporarily
    setState(() {
      _pin = '';
      if (resetConfirm) {
        _isConfirming = false;
        _confirmPin = '';
      }
      _message = 'รหัส PIN ไม่ถูกต้อง กรุณาลองใหม่';
    });

    Future.delayed(const Duration(seconds: 1), () {
      if (mounted) _updateMessage();
    });
  }

  @override
  Widget build(BuildContext context) {
    final isDarkMode = Theme.of(context).brightness == Brightness.dark;
    final color = isDarkMode ? Colors.white : AppTheme.primary;

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: widget.onCancel != null
          ? AppBar(
              leading: IconButton(
                icon: const Icon(Icons.close),
                onPressed: widget.onCancel,
              ),
              backgroundColor: Colors.transparent,
            )
          : null,
      body: SafeArea(
        child: Column(
          children: [
            const Spacer(),
            Icon(Icons.lock_outline, size: 64, color: color),
            const SizedBox(height: 24),
            Text(
              _message,
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                color: isDarkMode ? Colors.white70 : AppTheme.textSub,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 48),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(6, (index) {
                return Container(
                  margin: const EdgeInsets.symmetric(horizontal: 8),
                  width: 16,
                  height: 16,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: index < _pin.length
                        ? color
                        : (isDarkMode ? Colors.white24 : Colors.black12),
                  ),
                );
              }),
            ),
            const Spacer(),
            _buildKeypad(color),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  Widget _buildKeypad(Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 48),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _buildKey('1', color),
              _buildKey('2', color),
              _buildKey('3', color),
            ],
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _buildKey('4', color),
              _buildKey('5', color),
              _buildKey('6', color),
            ],
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _buildKey('7', color),
              _buildKey('8', color),
              _buildKey('9', color),
            ],
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              widget.mode == PinMode.verify
                  ? IconButton(
                      onPressed: _tryBiometric,
                      icon: Icon(Icons.fingerprint, size: 32, color: color),
                    )
                  : const SizedBox(width: 64),
              _buildKey('0', color),
              IconButton(
                onPressed: _onDeletePressed,
                icon: Icon(Icons.backspace_outlined, size: 32, color: color),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildKey(String value, Color color) {
    return InkWell(
      onTap: () => _onKeyPressed(value),
      borderRadius: BorderRadius.circular(50),
      child: Container(
        width: 72,
        height: 72,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          border: Border.all(color: color.withOpacity(0.2)),
        ),
        child: Text(
          value,
          style: TextStyle(
            fontSize: 28,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
      ),
    );
  }
}
