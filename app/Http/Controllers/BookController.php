<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    protected $bookApi;

    public function __construct(BookApiService $bookApi = null)
    {
        $this->bookApi = $bookApi;
    }

    /**
     * Display a listing of the books.
     */
    public function index(Request $request)
    {
        $query = Book::with('category');
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('penulis', 'LIKE', "%{$search}%")
                  ->orWhere('kode_buku', 'LIKE', "%{$search}%");
            });
        }
        
        $books = $query->paginate(10);
        
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'penulis' => 'required|string|max:255',
                'penerbit' => 'nullable|string|max:255',
                'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
                'kategori_id' => 'nullable|exists:categories,id',
                'stok' => 'required|integer|min:1',
                'deskripsi' => 'nullable|string',
                'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Prepare data
            $data = $validator->validated();
            $data['kode_buku'] = 'BK-' . strtoupper(Str::random(8));
            $data['available_stock'] = $request->stok;

            // Handle cover upload
            if ($request->hasFile('cover')) {
                $coverPath = $this->uploadCover($request->file('cover'));
                if ($coverPath) {
                    $data['cover'] = $coverPath;
                }
            }

            // Create book
            $book = Book::create($data);

            return redirect()->route('books.index')
                ->with('success', 'Buku "' . $book->judul . '" berhasil ditambahkan.');

        } catch (\Exception $e) {
            Log::error('Error creating book: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified book.
     */
    public function show($id)
    {
        $book = Book::with('category', 'loans')->findOrFail($id);
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, Book $book)
    {
        try {
            // Validasi
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'penulis' => 'required|string|max:255',
                'penerbit' => 'nullable|string|max:255',
                'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
                'kategori_id' => 'nullable|exists:categories,id',
                'stok' => 'required|integer|min:0',
                'deskripsi' => 'nullable|string',
                'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Prepare data
            $data = $validator->validated();
            $data['available_stock'] = $request->stok;

            // Handle cover upload
            if ($request->hasFile('cover')) {
                // Delete old cover
                $this->deleteCover($book);
                
                // Upload new cover
                $coverPath = $this->uploadCover($request->file('cover'));
                if ($coverPath) {
                    $data['cover'] = $coverPath;
                }
            }

            // Update book
            $book->update($data);

            return redirect()->route('books.index')
                ->with('success', 'Buku "' . $book->judul . '" berhasil diupdate.');

        } catch (\Exception $e) {
            Log::error('Error updating book: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Book $book)
    {
        try {
            // Delete cover
            $this->deleteCover($book);
            
            // Delete book
            $book->delete();

            return redirect()->route('books.index')
                ->with('success', 'Buku berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting book: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove book cover only.
     */
    public function removeCover(Book $book)
    {
        try {
            $this->deleteCover($book);
            $book->update(['cover' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Cover berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error removing cover: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus cover'
            ], 500);
        }
    }

    /**
     * Search books from external API.
     */
    public function searchApi(Request $request)
    {
        $q = $request->get('q');
        $isbn = $request->get('isbn');

        if (!$this->bookApi) {
            return response()->json(['error' => 'Book API service not available'], 500);
        }

        try {
            if ($isbn) {
                $results = $this->bookApi->searchByISBN($isbn);
            } elseif ($q) {
                $results = $this->bookApi->searchByTitle($q);
            } else {
                return response()->json(['error' => 'Query or ISBN required'], 400);
            }

            if (!$results) {
                return response()->json(['items' => []]);
            }

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Error searching API: ' . $e->getMessage());
            return response()->json(['error' => 'API search failed'], 500);
        }
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        $categories = Category::all();
        return view('books.import', compact('categories'));
    }

    /**
     * Import book from API.
     */
    public function importFromApi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'api_id' => 'required|string',
                'judul' => 'required|string',
                'penulis' => 'required|string',
                'penerbit' => 'nullable|string',
                'tahun_terbit' => 'nullable|integer',
                'kategori_id' => 'nullable|exists:categories,id',
                'deskripsi' => 'nullable|string',
                'stok' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $book = Book::create([
                'kode_buku' => 'BK-' . strtoupper(Str::random(8)),
                'judul' => $request->judul,
                'penulis' => $request->penulis,
                'penerbit' => $request->penerbit,
                'tahun_terbit' => $request->tahun_terbit,
                'kategori_id' => $request->kategori_id,
                'deskripsi' => $request->deskripsi,
                'stok' => $request->stok,
                'available_stock' => $request->stok
            ]);

            // Download cover from API if available
            if ($request->cover_url && $this->bookApi) {
                $coverPath = $this->bookApi->downloadCover($request->cover_url, $book->id);
                if ($coverPath) {
                    $book->update(['cover' => $coverPath]);
                }
            }

            return redirect()->route('books.index')
                ->with('success', 'Buku berhasil diimport dari API');

        } catch (\Exception $e) {
            Log::error('Error importing book: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Upload cover image.
     */
    private function uploadCover($file)
    {
        try {
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Move file to public/uploads/covers
            $path = public_path('uploads/covers');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            $file->move($path, $filename);
            
            return 'uploads/covers/' . $filename;
            
        } catch (\Exception $e) {
            Log::error('Error uploading cover: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete cover image.
     */
    private function deleteCover($book)
    {
        try {
            if ($book->cover && file_exists(public_path($book->cover))) {
                unlink(public_path($book->cover));
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error deleting cover: ' . $e->getMessage());
        }
        return false;
    }
}