class NewsModel {
  final int id;
  final String title;
  final String excerpt;
  final String content;
  final String? attachmentUrl;
  final String createdAt;
  final String? imageUrl;

  NewsModel({
    required this.id,
    required this.title,
    required this.excerpt,
    required this.content,
    this.attachmentUrl,
    required this.createdAt,
    this.imageUrl,
  });

  factory NewsModel.fromJson(Map<String, dynamic> json) {
    // Extract image from embedded if available, otherwise use a placeholder
    String? img;
    if (json['_embedded'] != null &&
        json['_embedded']['wp:featuredmedia'] != null &&
        json['_embedded']['wp:featuredmedia'].isNotEmpty) {
      img = json['_embedded']['wp:featuredmedia'][0]['source_url'];
    }
    // If no image found, we can leave it null or provide default.
    // The UI handles null imageUrl.
    // If we want a default:
    // img ??= 'https://nass.ac.th/wp-content/uploads/2023/06/cropped-logo-nass-1.png';

    return NewsModel(
      id: json['id'] ?? 0,
      title: json['title']['rendered'] ?? 'ข่าวสารใหม่',
      excerpt: json['excerpt']['rendered'] ?? '',
      content: json['content']['rendered'] ?? '',
      attachmentUrl: json['link'],
      createdAt: json['date'] ?? DateTime.now().toIso8601String(),
      imageUrl:
          img ??
          'https://nass.ac.th/wp-content/uploads/2023/06/cropped-logo-nass-1.png',
    );
  }
}
