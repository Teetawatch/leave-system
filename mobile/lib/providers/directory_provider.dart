import 'package:flutter/material.dart';
import '../models/employee_contact.dart';

class DirectoryProvider with ChangeNotifier {
  List<EmployeeContact> _contacts = [
    EmployeeContact(
      id: '1',
      name: 'พล.ต.ท. สมชาย มุ่งมั่น',
      position: 'ผบ.ศฝร.',
      department: 'ผู้บริหาร',
      phoneNumber: '081-234-5678',
      lineId: 'somchai.m',
      avatarUrl: null,
    ),
    EmployeeContact(
      id: '2',
      name: 'พ.ต.อ. วินัย ใฝ่ดี',
      position: 'รอง ผบ.ศฝร.',
      department: 'ผู้บริหาร',
      phoneNumber: '089-876-5432',
      lineId: 'winai.f',
      avatarUrl: null,
    ),
    EmployeeContact(
      id: '3',
      name: 'ร.ต.อ. กล้าหาญ ชาญชัย',
      position: 'ครูฝึก',
      department: 'ฝอ.1',
      phoneNumber: '086-111-2222',
      lineId: 'kla.han',
      avatarUrl: null,
    ),
    EmployeeContact(
      id: '4',
      name: 'ด.ต. มานะ อดทน',
      position: 'เจ้าหน้าที่ธุรการ',
      department: 'ฝอ.2',
      phoneNumber: '085-555-4444',
      lineId: 'mana.a',
      avatarUrl: null,
    ),
    EmployeeContact(
      id: '5',
      name: 'นาย สมศักดิ์ รักงาน',
      position: 'พนักงานขับรถ',
      department: 'ยานยนต์',
      phoneNumber: '084-333-7777',
      lineId: null,
      avatarUrl: null,
    ),
  ];

  List<EmployeeContact> _filteredContacts = [];
  String _searchQuery = '';

  DirectoryProvider() {
    _filteredContacts = _contacts;
  }

  List<EmployeeContact> get contacts => _filteredContacts;

  void search(String query) {
    _searchQuery = query;
    if (query.isEmpty) {
      _filteredContacts = _contacts;
    } else {
      _filteredContacts = _contacts.where((contact) {
        return contact.name.contains(query) ||
            contact.department.contains(query) ||
            contact.position.contains(query) ||
            contact.phoneNumber.contains(query);
      }).toList();
    }
    notifyListeners();
  }
}
