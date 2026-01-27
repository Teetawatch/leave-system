import 'package:flutter/material.dart';
import '../models/employee_contact.dart';
import '../services/api_service.dart';

class DirectoryProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<EmployeeContact> _allContacts = [];
  List<EmployeeContact> _filteredContacts = [];

  bool _isLoading = false;
  String? _error;

  String _searchQuery = '';
  String _selectedDepartment = 'ทั้งหมด';

  final List<String> departments = [
    'ทั้งหมด',
    'แผนกปกครอง',
    'แผนกศึกษา',
    'แผนกสนับสนุน',
    'ฝ่ายธุรการ',
    'ฝ่ายการเงิน',
  ];

  List<EmployeeContact> get contacts => _filteredContacts;
  String get selectedDepartment => _selectedDepartment;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchContacts() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      // Use the known working endpoint from Guard Change feature
      final response = await _apiService.getGuardChangeUsers();

      final List<dynamic> usersJson;
      if (response.data is Map && (response.data as Map).containsKey('data')) {
        usersJson = response.data['data'] ?? [];
      } else if (response.data is List) {
        usersJson = response.data;
      } else {
        usersJson = [];
      }

      _allContacts = usersJson.map((json) {
        // Manually parse to avoid strict validation in User.fromJson
        // and to handle potential missing fields in the list API
        final id = json['id']?.toString() ?? '';
        final name = json['name']?.toString() ?? '-';
        final rank = json['rank']?.toString() ?? '';
        final fullName = rank.isNotEmpty ? '$rank $name' : name;

        final position = json['position']?.toString() ?? '-';
        // Map department to ensure it matches the filter tabs
        final rawDept = json['department']?.toString();
        final department = _mapDepartment(rawDept);
        final phone =
            json['phone_number']?.toString() ?? json['tel']?.toString() ?? '-';
        final line = json['line_id']?.toString();

        String? avatar = json['avatar_url'];
        // Fix for emulator
        if (avatar != null && avatar.contains('localhost')) {
          avatar = avatar.replaceFirst('localhost', '10.0.2.2');
        }

        return EmployeeContact(
          id: id,
          name: fullName,
          position: position,
          department: department,
          phoneNumber: phone,
          lineId: line,
          avatarUrl: avatar,
        );
      }).toList();

      _filter();
    } catch (e) {
      debugPrint('Error fetching contacts: $e');
      _error = 'ไม่สามารถโหลดรายชื่อได้';
      _allContacts = [];
      _filter();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void setDepartment(String department) {
    _selectedDepartment = department;
    _filter();
  }

  void search(String query) {
    _searchQuery = query;
    _filter();
  }

  void _filter() {
    _filteredContacts = _allContacts.where((contact) {
      final matchesQuery =
          contact.name.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          contact.department.toLowerCase().contains(
            _searchQuery.toLowerCase(),
          ) ||
          contact.position.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          contact.phoneNumber.contains(_searchQuery);

      final matchesDepartment =
          _selectedDepartment == 'ทั้งหมด' ||
          contact.department == _selectedDepartment;

      return matchesQuery && matchesDepartment;
    }).toList();
    notifyListeners();
  }

  String _mapDepartment(String? raw) {
    if (raw == null || raw.isEmpty) return 'แผนกสนับสนุน'; // Default fallback

    final input = raw.toLowerCase().trim();

    // Check for exact keys or English terms
    if (input.contains('govern') || input.contains('ปกครอง')) {
      return 'แผนกปกครอง';
    }
    if (input.contains('edu') ||
        input.contains('study') ||
        input.contains('train') ||
        input.contains('ศึกษา')) {
      return 'แผนกศึกษา';
    }
    if (input.contains('support') ||
        input.contains('up') ||
        input.contains('supply') ||
        input.contains('สนับสนุน')) {
      return 'แผนกสนับสนุน';
    }
    if (input.contains('admin') || input.contains('ธุรการ')) {
      return 'ฝ่ายธุรการ';
    }
    if (input.contains('fin') ||
        input.contains('account') ||
        input.contains('การเงิน')) {
      return 'ฝ่ายการเงิน';
    }

    // If already one of the valid thai keys, return it (handled by contains above mostly, but strict check)
    return 'แผนกสนับสนุน'; // Fallback to Support if unknown
  }
}
