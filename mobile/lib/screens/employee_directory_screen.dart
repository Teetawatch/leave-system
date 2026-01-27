import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/directory_provider.dart';
import '../config/app_theme.dart';
import '../models/employee_contact.dart';

class EmployeeDirectoryScreen extends StatefulWidget {
  const EmployeeDirectoryScreen({super.key});

  @override
  State<EmployeeDirectoryScreen> createState() =>
      _EmployeeDirectoryScreenState();
}

class _EmployeeDirectoryScreenState extends State<EmployeeDirectoryScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<DirectoryProvider>(context, listen: false).fetchContacts();
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _makePhoneCall(String phoneNumber) async {
    final Uri launchUri = Uri(
      scheme: 'tel',
      path: phoneNumber.replaceAll(RegExp(r'-|\s'), ''),
    );
    try {
      if (await canLaunchUrl(launchUri)) {
        await launchUrl(launchUri);
      } else {
        throw 'Could not launch $launchUri';
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('ไม่สามารถโทรออกได้: $phoneNumber'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: Text(
          'สมุดรายชื่อ',
          style: GoogleFonts.kanit(fontSize: 20, fontWeight: FontWeight.bold),
        ),
        centerTitle: true,
        elevation: 0,
      ),
      body: Column(
        children: [
          // Search Bar
          Container(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
            color: Colors.white,
            child: TextField(
              controller: _searchController,
              onChanged: (value) {
                Provider.of<DirectoryProvider>(
                  context,
                  listen: false,
                ).search(value);
              },
              decoration: InputDecoration(
                hintText: 'ค้นหาชื่อ, แผนก หรือเบอร์โทร...',
                hintStyle: GoogleFonts.sarabun(
                  color: AppTheme.textSub,
                  fontWeight: FontWeight.w300,
                  fontSize: 14,
                ),
                prefixIcon: const Icon(Icons.search, color: AppTheme.textSub),
                filled: true,
                fillColor: AppTheme.background,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
                contentPadding: const EdgeInsets.symmetric(vertical: 0),
              ),
            ),
          ),

          // Department Filters
          Container(
            height: 60,
            color: Colors.white,
            child: Consumer<DirectoryProvider>(
              builder: (context, provider, child) {
                return ListView.separated(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 8,
                  ),
                  scrollDirection: Axis.horizontal,
                  itemCount: provider.departments.length,
                  separatorBuilder: (_, __) => const SizedBox(width: 8),
                  itemBuilder: (context, index) {
                    final dept = provider.departments[index];
                    final isSelected = dept == provider.selectedDepartment;
                    return ChoiceChip(
                      label: Text(
                        dept,
                        style: GoogleFonts.sarabun(
                          fontSize: 14,
                          fontWeight: isSelected
                              ? FontWeight.bold
                              : FontWeight.normal,
                          color: isSelected ? Colors.white : AppTheme.textSub,
                        ),
                      ),
                      selected: isSelected,
                      onSelected: (selected) {
                        if (selected) {
                          provider.setDepartment(dept);
                        }
                      },
                      selectedColor: AppTheme.primary,
                      backgroundColor: AppTheme.background,
                      side: BorderSide.none,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                      ),
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                    );
                  },
                );
              },
            ),
          ),

          // Contact List
          Expanded(
            child: Consumer<DirectoryProvider>(
              builder: (context, provider, child) {
                if (provider.isLoading) {
                  return const Center(child: CircularProgressIndicator());
                }

                if (provider.error != null) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.error_outline,
                          size: 48,
                          color: AppTheme.error,
                        ),
                        const SizedBox(height: 16),
                        Text(
                          provider.error!,
                          style: GoogleFonts.sarabun(
                            color: AppTheme.textSub,
                            fontSize: 16,
                          ),
                        ),
                        const SizedBox(height: 16),
                        ElevatedButton(
                          onPressed: provider.fetchContacts,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppTheme.primary,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                            ),
                          ),
                          child: Text(
                            'ลองใหม่',
                            style: GoogleFonts.kanit(color: Colors.white),
                          ),
                        ),
                      ],
                    ),
                  );
                }

                final contacts = provider.contacts;

                if (contacts.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.person_off_outlined,
                          size: 64,
                          color: AppTheme.textSub.withOpacity(0.5),
                        ),
                        const SizedBox(height: 16),
                        Text(
                          'ไม่พบรายชื่อที่ค้นหา',
                          style: GoogleFonts.sarabun(
                            fontSize: 16,
                            color: AppTheme.textSub,
                          ),
                        ),
                      ],
                    ),
                  );
                }

                return ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    ...contacts
                        .map((contact) => _buildContactCard(contact))
                        ,
                    const SizedBox(height: 24),
                    Center(
                      child: Text(
                        'END OF DIRECTORY',
                        style: GoogleFonts.sarabun(
                          fontSize: 12,
                          color: AppTheme.textSub.withOpacity(0.5),
                          fontWeight: FontWeight.w500,
                          letterSpacing: 1.2,
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),
                  ],
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildContactCard(EmployeeContact contact) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            // Avatar
            CircleAvatar(
              radius: 28,
              backgroundColor: AppTheme.background,
              backgroundImage: contact.avatarUrl != null
                  ? NetworkImage(contact.avatarUrl!)
                  : null,
              child: contact.avatarUrl == null
                  ? Text(
                      contact.name[0],
                      style: GoogleFonts.kanit(
                        fontSize: 20,
                        color: AppTheme.primary,
                        fontWeight: FontWeight.bold,
                      ),
                    )
                  : null,
            ),
            const SizedBox(width: 16),
            // Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    contact.name,
                    style: GoogleFonts.kanit(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: AppTheme.textMain,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    contact.position.toUpperCase(),
                    style: GoogleFonts.sarabun(
                      fontSize: 12,
                      color: AppTheme.primary.withOpacity(0.8),
                      fontWeight: FontWeight.w600,
                      letterSpacing: 0.5,
                    ),
                  ),
                ],
              ),
            ),
            // Actions
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                _buildActionButton(
                  icon: Icons.phone,
                  color: AppTheme.primary,
                  onTap: () => _makePhoneCall(contact.phoneNumber),
                ),
                const SizedBox(width: 12),
                _buildActionButton(
                  icon: Icons.chat_bubble_outline,
                  color: Colors.blue,
                  onTap: () {
                    // Implement chat functionality or show snackbar
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text('เริ่มการสนทนากับ ${contact.name}'),
                        duration: const Duration(seconds: 1),
                      ),
                    );
                  },
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildActionButton({
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(50),
        child: Container(
          width: 44,
          height: 44,
          decoration: BoxDecoration(
            color: color.withOpacity(0.1), // Light background
            shape: BoxShape.circle,
            border: Border.all(color: color.withOpacity(0.2), width: 1),
          ),
          child: Icon(icon, color: color, size: 20),
        ),
      ),
    );
  }
}
