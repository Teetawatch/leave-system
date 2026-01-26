import 'package:flutter/material.dart';
import '../models/news_model.dart';
import '../services/api_service.dart';

class NewsProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<NewsItem> _newsList = [];
  bool _isLoading = false;

  List<NewsItem> get newsList => _newsList;
  bool get isLoading => _isLoading;

  Future<void> fetchLatestNews() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.getLatestNews();
      final List list = response.data;
      _newsList = list.map((e) => NewsItem.fromJson(e)).toList();
    } catch (e) {
      debugPrint('Error fetching news: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
