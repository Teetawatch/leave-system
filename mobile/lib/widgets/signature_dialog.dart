import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:signature/signature.dart';
import '../config/app_theme.dart';

class SignatureDialog extends StatefulWidget {
  final bool isApprove;
  final String? savedSignatureUrl;

  const SignatureDialog({
    super.key,
    required this.isApprove,
    this.savedSignatureUrl,
  });

  @override
  State<SignatureDialog> createState() => _SignatureDialogState();
}

class _SignatureDialogState extends State<SignatureDialog> {
  final SignatureController _controller = SignatureController(
    penStrokeWidth: 3,
    penColor: Colors.black,
    exportBackgroundColor: Colors.white,
  );

  final TextEditingController _commentController = TextEditingController();
  bool _useSavedSignature = false;

  @override
  void dispose() {
    _controller.dispose();
    _commentController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final hasSavedSignature = widget.savedSignatureUrl != null;

    return AlertDialog(
      scrollable: true,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(28)),
      title: Text(
        widget.isApprove ? 'ยืนยันการอนุมัติ' : 'ระบุเหตุผลการปฏิเสธ',
        style: const TextStyle(fontWeight: FontWeight.w900),
      ),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          TextField(
            controller: _commentController,
            decoration: InputDecoration(
              hintText: widget.isApprove
                  ? 'ความคิดเห็นเพิ่มเติม (ถ้ามี)'
                  : 'ระบุเหตุผลที่ปฏิเสธ',
              filled: true,
              fillColor: AppTheme.background,
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(16),
                borderSide: BorderSide.none,
              ),
            ),
            maxLines: 3,
          ),
          if (widget.isApprove) ...[
            const SizedBox(height: 24),
            const Text(
              'ลายเซ็นต์ผู้อนุมัติ',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
                color: AppTheme.textMain,
              ),
            ),
            const SizedBox(height: 8),
            if (hasSavedSignature)
              CheckboxListTile(
                contentPadding: EdgeInsets.zero,
                title: const Text(
                  'ใช้ลายเซ็นต์ที่มีอยู่',
                  style: TextStyle(fontSize: 14),
                ),
                value: _useSavedSignature,
                onChanged: (val) {
                  setState(() {
                    _useSavedSignature = val ?? false;
                  });
                },
                controlAffinity: ListTileControlAffinity.leading,
                activeColor: AppTheme.primary,
              ),
            if (!_useSavedSignature) ...[
              Container(
                width: double.infinity,
                decoration: BoxDecoration(
                  border: Border.all(color: AppTheme.border),
                  borderRadius: BorderRadius.circular(16),
                  color: Colors.white,
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(16),
                  child: Signature(
                    controller: _controller,
                    height: 150,
                    backgroundColor: Colors.white,
                  ),
                ),
              ),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  TextButton.icon(
                    onPressed: () => _controller.clear(),
                    icon: const Icon(Icons.clear, size: 18),
                    label: const Text('ล้าง'),
                    style: TextButton.styleFrom(
                      foregroundColor: AppTheme.error,
                    ),
                  ),
                ],
              ),
            ] else if (hasSavedSignature) ...[
              Container(
                height: 150,
                width: double.infinity,
                decoration: BoxDecoration(
                  border: Border.all(color: AppTheme.border),
                  borderRadius: BorderRadius.circular(16),
                  color: Colors.white,
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(16),
                  child: Image.network(
                    widget.savedSignatureUrl!,
                    fit: BoxFit.contain,
                    errorBuilder: (context, error, stackTrace) =>
                        const Center(child: Text('ไม่สามารถโหลดลายเซ็นต์ได้')),
                  ),
                ),
              ),
              const SizedBox(height: 12),
            ],
          ],
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text(
            'ยกเลิก',
            style: TextStyle(color: AppTheme.textSub),
          ),
        ),
        ElevatedButton(
          onPressed: () async {
            String? signatureBase64;

            if (widget.isApprove && !_useSavedSignature) {
              if (_controller.isEmpty) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('โปรดเซ็นชื่อก่อนอนุมัติ')),
                );
                return;
              }
              final Uint8List? data = await _controller.toPngBytes();
              if (data != null) {
                signatureBase64 = 'data:image/png;base64,${base64Encode(data)}';
              }
            }

            if (!context.mounted) return;
            Navigator.pop(context, {
              'comment': _commentController.text,
              'signature': signatureBase64,
              'useSavedSignature': _useSavedSignature,
            });
          },
          style: ElevatedButton.styleFrom(
            backgroundColor: widget.isApprove
                ? AppTheme.success
                : AppTheme.error,
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
          child: const Text(
            'ยืนยัน',
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
        ),
      ],
    );
  }
}
