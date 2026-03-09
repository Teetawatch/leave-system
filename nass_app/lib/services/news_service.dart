import 'package:http/http.dart' as http;
import 'package:html/parser.dart' as html_parser;

class NewsItem {
  final String title;
  final String? imageUrl;
  final String? link;
  final String? date;
  final String? excerpt;

  NewsItem({
    required this.title,
    this.imageUrl,
    this.link,
    this.date,
    this.excerpt,
  });
}

class NewsService {
  static const String _baseUrl = 'https://nass.ac.th';

  static Future<List<NewsItem>> fetchNews() async {
    try {
      final response = await http.get(
        Uri.parse(_baseUrl),
        headers: {
          'User-Agent': 'Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36',
          'Accept': 'text/html,application/xhtml+xml',
        },
      ).timeout(const Duration(seconds: 10));

      if (response.statusCode != 200) return _fallbackNews();

      final document = html_parser.parse(response.body);
      final items = <NewsItem>[];

      // Try to find news/post elements from the site
      // Look for common WordPress/CMS patterns
      final postElements = document.querySelectorAll(
        'article, .post, .news-item, .entry, .card, .item-post, .td-module-thumb, .tdb_module_loop'
      );

      const maxItems = 6;
      final seenLinks = <String>{};
      final seenTitles = <String>{};

      if (postElements.isNotEmpty) {
        for (final el in postElements) {
          if (items.length >= maxItems) break;

          final titleEl = el.querySelector('h1, h2, h3, h4, .entry-title, .td-module-title');
          final imgEl = el.querySelector('img');
          final linkEl = el.querySelector('a[href]');
          final dateEl = el.querySelector('time, .date, .entry-date, .td-post-date');
          final excerptEl = el.querySelector('p, .excerpt, .entry-content, .td-excerpt');

          final title = titleEl?.text.trim() ?? linkEl?.text.trim() ?? '';
          if (title.isEmpty || title.length < 5) continue;

          String? imgUrl = imgEl?.attributes['data-src'] ??
              imgEl?.attributes['data-lazy-src'] ??
              imgEl?.attributes['data-original'] ??
              imgEl?.attributes['data-img-url'] ??
              imgEl?.attributes['data-full-url'] ??
              imgEl?.attributes['src'];
          imgUrl = _normalizeImageUrl(imgUrl);

          String? link = linkEl?.attributes['href'];
          if (link != null && link.startsWith('/')) {
            link = '$_baseUrl$link';
          }

          // Deduplicate by link and title
          final dedupeKey = link ?? title;
          if (seenLinks.contains(dedupeKey)) continue;
          if (seenTitles.contains(title)) continue;
          seenLinks.add(dedupeKey);
          seenTitles.add(title);

          items.add(NewsItem(
            title: title,
            imageUrl: imgUrl,
            link: link,
            date: dateEl?.text.trim(),
            excerpt: excerptEl?.text.trim(),
          ));
        }
      }

      // Fallback: look for any links with images
      if (items.isEmpty) {
        final allLinks = document.querySelectorAll('a[href]');
        for (final a in allLinks) {
          if (items.length >= maxItems) break;

          final img = a.querySelector('img');
          final title = a.attributes['title'] ?? a.text.trim();
          if (title.isEmpty || title.length < 10 || img == null) continue;

          String? imgUrl = img.attributes['data-src'] ??
              img.attributes['data-lazy-src'] ??
              img.attributes['data-original'] ??
              img.attributes['data-img-url'] ??
              img.attributes['src'];
          imgUrl = _normalizeImageUrl(imgUrl);

          String? link = a.attributes['href'];
          if (link != null && link.startsWith('/')) {
            link = '$_baseUrl$link';
          }

          if (imgUrl != null && imgUrl.contains('logo')) continue;

          // Deduplicate
          final dedupeKey = link ?? title;
          if (seenLinks.contains(dedupeKey)) continue;
          if (seenTitles.contains(title)) continue;
          seenLinks.add(dedupeKey);
          seenTitles.add(title);

          items.add(NewsItem(
            title: title.length > 80 ? '${title.substring(0, 80)}...' : title,
            imageUrl: imgUrl,
            link: link,
          ));
        }
      }

      return items.isNotEmpty ? items : _fallbackNews();
    } catch (e) {
      return _fallbackNews();
    }
  }

  static String? _normalizeImageUrl(String? url) {
    if (url == null || url.isEmpty) return null;
    // Filter out base64 placeholders and tiny tracker pixels
    if (url.startsWith('data:')) return null;
    if (url.contains('data:image/gif') || url.contains('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAE')) return null;
    // Normalize protocol-relative and relative URLs
    if (url.startsWith('//')) return 'https:$url';
    if (url.startsWith('/')) return '$_baseUrl$url';
    return url;
  }

  static List<NewsItem> _fallbackNews() {
    return [
      NewsItem(
        title: 'ยินดีต้อนรับสู่โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ',
        imageUrl: null,
        link: 'https://nass.ac.th',
        date: '',
        excerpt: 'ติดตามข่าวสารและกิจกรรมของโรงเรียนนายเรืออากาศฯ ได้ที่เว็บไซต์ nass.ac.th',
      ),
    ];
  }
}
