import 'package:flutter/material.dart';
import '../models/news_model.dart';
import '../services/api_service.dart';

class NewsProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<NewsModel> _newsList = [];
  bool _isLoading = false;

  List<NewsModel> get newsList => _newsList;
  bool get isLoading => _isLoading;

  Future<void> fetchLatestNews() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.getLatestNews();
      final List list = response.data;
      _newsList = list.map((e) => NewsModel.fromJson(e)).toList();
    } catch (e) {
      debugPrint('Error fetching news: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
