import 'dart:io';
import 'package:dio/dio.dart';
import 'package:open_filex/open_filex.dart';
import 'package:path_provider/path_provider.dart';
import 'package:flutter/foundation.dart';
import 'api_service.dart';

class PdfService {
  static final ApiService _apiService = ApiService();

  static Future<void> downloadAndOpenPdf(int requestId, String fileName) async {
    try {
      final appDocDir = await getApplicationDocumentsDirectory();
      final String savePath = "${appDocDir.path}/$fileName.pdf";

      final response = await _apiService.client.get(
        '/leave-requests/$requestId/pdf',
        options: Options(
          responseType: ResponseType.bytes,
          followRedirects: false,
        ),
      );

      final file = File(savePath);
      await file.writeAsBytes(response.data);

      await OpenFilex.open(savePath);
    } catch (e) {
      debugPrint("Error downloading/opening PDF: $e");
      rethrow;
    }
  }

  static Future<void> downloadAndOpenGuardChangePdf(
    int requestId,
    String fileName,
  ) async {
    try {
      final appDocDir = await getApplicationDocumentsDirectory();
      final String savePath = "${appDocDir.path}/$fileName.pdf";

      final response = await _apiService.client.get(
        '/guard-change-requests/$requestId/pdf',
        options: Options(
          responseType: ResponseType.bytes,
          followRedirects: false,
        ),
      );

      final file = File(savePath);
      await file.writeAsBytes(response.data);

      await OpenFilex.open(savePath);
    } catch (e) {
      debugPrint("Error downloading/opening Guard Change PDF: $e");
      rethrow;
    }
  }
}
