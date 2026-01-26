class NewsItem {
  final int id;
  final String title;
  final String excerpt;
  final String content;
  final String link;
  final String date;
  final String imageUrl;

  NewsItem({
    required this.id,
    required this.title,
    required this.excerpt,
    required this.content,
    required this.link,
    required this.date,
    required this.imageUrl,
  });

  factory NewsItem.fromJson(Map<String, dynamic> json) {
    // Extract image from embedded if available, otherwise use a placeholder
    String img =
        'https://nass.ac.th/wp-content/uploads/2023/06/cropped-logo-nass-1.png';
    if (json['_embedded'] != null &&
        json['_embedded']['wp:featuredmedia'] != null &&
        json['_embedded']['wp:featuredmedia'].isNotEmpty) {
      img = json['_embedded']['wp:featuredmedia'][0]['source_url'] ?? img;
    }

    return NewsItem(
      id: json['id'] ?? 0,
      title: json['title']['rendered'] ?? 'ข่าวสารใหม่',
      excerpt: json['excerpt']['rendered'] ?? '',
      content: json['content']['rendered'] ?? '',
      link: json['link'] ?? '',
      date: json['date'] ?? '',
      imageUrl: img,
    );
  }
}
