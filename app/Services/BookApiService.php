<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BookApiService
{
    protected $apiUrl;
    protected $provider;

    public function __construct()
    {
        $this->apiUrl = config('services.book_api.url') ?? env('BOOK_API_URL', 'https://openlibrary.org');
        $this->provider = env('BOOK_API_PROVIDER', 'openlibrary');
    }

    /**
     * Search books by title menggunakan Open Library API
     */
    public function searchByTitle($title, $limit = 10)
    {
        try {
            $cacheKey = 'book_search_' . md5($title);
            
            return Cache::remember($cacheKey, 3600, function () use ($title, $limit) {
                $response = Http::get($this->apiUrl . '/search.json', [
                    'q' => 'title:' . $title,
                    'limit' => $limit,
                    'fields' => 'key,title,author_name,first_publish_year,publisher,cover_i,isbn,subject,description'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatOpenLibraryResponse($data);
                }

                Log::error('Open Library API Error: ' . $response->body());
                return $this->getMockData($title);
            });

        } catch (\Exception $e) {
            Log::error('Open Library API Exception: ' . $e->getMessage());
            return $this->getMockData($title);
        }
    }

    /**
     * Search by ISBN menggunakan Open Library API
     */
    public function searchByISBN($isbn)
    {
        try {
            $cacheKey = 'book_isbn_' . md5($isbn);
            
            return Cache::remember($cacheKey, 3600, function () use ($isbn) {
                $response = Http::get($this->apiUrl . '/api/books', [
                    'bibkeys' => 'ISBN:' . $isbn,
                    'format' => 'json',
                    'jscmd' => 'data'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatOpenLibraryISBNResponse($data, $isbn);
                }

                Log::error('Open Library ISBN Error: ' . $response->body());
                return null;
            });

        } catch (\Exception $e) {
            Log::error('Open Library ISBN Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Search by author
     */
    public function searchByAuthor($author, $limit = 10)
    {
        try {
            $cacheKey = 'book_author_' . md5($author);
            
            return Cache::remember($cacheKey, 3600, function () use ($author, $limit) {
                $response = Http::get($this->apiUrl . '/search.json', [
                    'q' => 'author:' . $author,
                    'limit' => $limit,
                    'fields' => 'key,title,author_name,first_publish_year,publisher,cover_i,isbn,subject,description'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatOpenLibraryResponse($data);
                }

                return null;
            });

        } catch (\Exception $e) {
            Log::error('Open Library Author Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get book details by Open Library key
     */
    public function getBookDetails($key)
    {
        try {
            $response = Http::get($this->apiUrl . '/api/books', [
                'bibkeys' => 'OLID:' . $key,
                'format' => 'json',
                'jscmd' => 'data'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatOpenLibraryDetailResponse($data, $key);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Open Library Detail Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download book cover dari Open Library
     */
    public function downloadCover($coverId, $bookId)
    {
        try {
            if (empty($coverId)) return null;
            // Support passing either a numeric cover ID or a full URL
            if (filter_var($coverId, FILTER_VALIDATE_URL)) {
                $coverUrl = $coverId;
            } else {
                $coverUrl = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
            }

            $response = Http::get($coverUrl);
            if ($response->successful() && $response->body()) {
                $path = public_path('uploads/covers/' . $bookId . '.jpg');
                file_put_contents($path, $response->body());
                return 'uploads/covers/' . $bookId . '.jpg';
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Cover Download Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format Open Library search response
     */
    protected function formatOpenLibraryResponse($data)
    {
        if (!isset($data['docs']) || empty($data['docs'])) {
            return ['items' => []];
        }

        $items = [];
        foreach ($data['docs'] as $doc) {
            $items[] = [
                'id' => str_replace('/works/', '', $doc['key'] ?? ''),
                'judul' => $doc['title'] ?? 'Judul tidak tersedia',
                'penulis' => is_array($doc['author_name'] ?? null) ? implode(', ', $doc['author_name']) : ($doc['author_name'] ?? 'Penulis tidak diketahui'),
                'penerbit' => is_array($doc['publisher'] ?? null) ? implode(', ', $doc['publisher']) : ($doc['publisher'] ?? 'Penerbit tidak diketahui'),
                'tahun_terbit' => $doc['first_publish_year'] ?? null,
                'deskripsi' => $doc['description'] ?? 'Deskripsi tidak tersedia',
                'cover' => isset($doc['cover_i']) ? "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-M.jpg" : null,
                'cover_id' => $doc['cover_i'] ?? null,
                'isbn' => is_array($doc['isbn'] ?? null) ? $doc['isbn'][0] : ($doc['isbn'] ?? null),
                'subject' => is_array($doc['subject'] ?? null) ? implode(', ', array_slice($doc['subject'], 0, 3)) : ($doc['subject'] ?? ''),
                'key' => $doc['key'] ?? null,
                'source' => 'Open Library'
            ];
        }

        return ['items' => $items];
    }

    /**
     * Format Open Library ISBN response
     */
    protected function formatOpenLibraryISBNResponse($data, $isbn)
    {
        $key = 'ISBN:' . $isbn;
        if (!isset($data[$key])) {
            return null;
        }

        $book = $data[$key];
        return [
            'items' => [[
                'id' => $book['key'] ?? null,
                'judul' => $book['title'] ?? 'Judul tidak tersedia',
                'penulis' => is_array($book['authors'] ?? null) ? implode(', ', array_column($book['authors'], 'name')) : ($book['authors'] ?? 'Penulis tidak diketahui'),
                'penerbit' => is_array($book['publishers'] ?? null) ? implode(', ', array_column($book['publishers'], 'name')) : ($book['publishers'] ?? 'Penerbit tidak diketahui'),
                'tahun_terbit' => $book['publish_date'] ?? null,
                'deskripsi' => $book['description'] ?? 'Deskripsi tidak tersedia',
                'cover' => isset($book['cover']['large']) ? $book['cover']['large'] : null,
                'cover_id' => isset($book['cover']['id']) ? $book['cover']['id'] : null,
                'isbn' => $isbn,
                'source' => 'Open Library'
            ]]
        ];
    }

    /**
     * Format Open Library detail response
     */
    protected function formatOpenLibraryDetailResponse($data, $key)
    {
        $fullKey = 'OLID:' . $key;
        if (!isset($data[$fullKey])) {
            return null;
        }

        $book = $data[$fullKey];
        return [
            'id' => $book['key'] ?? null,
            'judul' => $book['title'] ?? 'Judul tidak tersedia',
            'penulis' => is_array($book['authors'] ?? null) ? implode(', ', array_column($book['authors'], 'name')) : ($book['authors'] ?? 'Penulis tidak diketahui'),
            'penerbit' => is_array($book['publishers'] ?? null) ? implode(', ', array_column($book['publishers'], 'name')) : ($book['publishers'] ?? 'Penerbit tidak diketahui'),
            'tahun_terbit' => $book['publish_date'] ?? null,
            'deskripsi' => $book['description'] ?? 'Deskripsi tidak tersedia',
            'cover' => isset($book['cover']['large']) ? $book['cover']['large'] : null,
            'isbn' => $book['identifiers']['isbn_13'][0] ?? $book['identifiers']['isbn_10'][0] ?? null,
            'source' => 'Open Library'
        ];
    }

    /**
     * Mock data for testing
     */
    protected function getMockData($query)
    {
        $mockBooks = [
            'laravel' => [
                [
                    'id' => 'mock_1',
                    'judul' => 'Laravel: Up & Running - A Framework for Building Modern PHP Apps',
                    'penulis' => 'Matt Stauffer',
                    'penerbit' => "O'Reilly Media",
                    'tahun_terbit' => 2023,
                    'deskripsi' => 'Buku panduan lengkap tentang Laravel framework. Cocok untuk developer PHP yang ingin membangun aplikasi modern.',
                    'cover' => null,
                    'source' => 'Mock Data'
                ],
                [
                    'id' => 'mock_2',
                    'judul' => 'Laravel Design Patterns - Best Practices for Building Robust Applications',
                    'penulis' => 'Todd Chaffee',
                    'penerbit' => 'Packt Publishing',
                    'tahun_terbit' => 2022,
                    'deskripsi' => 'Pola desain dalam Laravel untuk membangun aplikasi yang scalable dan maintainable.',
                    'cover' => null,
                    'source' => 'Mock Data'
                ]
            ],
            'php' => [
                [
                    'id' => 'mock_3',
                    'judul' => 'PHP 8 Programming - Modern PHP Development',
                    'penulis' => 'Peter MacIntyre',
                    'penerbit' => 'Apress',
                    'tahun_terbit' => 2022,
                    'deskripsi' => 'Pemrograman PHP 8 terbaru dengan fitur-fitur modern.',
                    'cover' => null,
                    'source' => 'Mock Data'
                ]
            ],
            'clean code' => [
                [
                    'id' => 'mock_4',
                    'judul' => 'Clean Code - A Handbook of Agile Software Craftsmanship',
                    'penulis' => 'Robert C. Martin',
                    'penerbit' => 'Prentice Hall',
                    'tahun_terbit' => 2008,
                    'deskripsi' => 'Buku tentang penulisan kode yang bersih dan best practices dalam software development.',
                    'cover' => null,
                    'source' => 'Mock Data'
                ]
            ],
            'design pattern' => [
                [
                    'id' => 'mock_5',
                    'judul' => 'Design Patterns - Elements of Reusable Object-Oriented Software',
                    'penulis' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                    'penerbit' => 'Addison-Wesley Professional',
                    'tahun_terbit' => 1994,
                    'deskripsi' => 'Buku klasik tentang design patterns dalam object-oriented programming.',
                    'cover' => null,
                    'source' => 'Mock Data'
                ]
            ],
            'programming' => [
                [
                    'id' => 'mock_6',
                    'judul' => 'The Pragmatic Programmer - Your Journey to Mastery',
                    'penulis' => 'David Thomas, Andrew Hunt',
                    'penerbit' => 'Addison-Wesley Professional',
                    'tahun_terbit' => 2019,
                    'deskripsi' => 'Buku tentang praktik terbaik dalam software development.',
                    'cover' => null,
                    'source' => 'Mock Data'
                ]
            ]
        ];

        $results = [];
        $queryLower = strtolower($query);
        
        foreach ($mockBooks as $key => $books) {
            if (strpos($key, $queryLower) !== false || strpos($queryLower, $key) !== false) {
                $results = array_merge($results, $books);
            }
        }

        if (empty($results)) {
            foreach ($mockBooks as $books) {
                $results = array_merge($results, $books);
            }
        }

        return ['items' => array_slice($results, 0, 10)];
    }
}