<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    public function index()
    {
        $books = Book::with('category')->paginate(10);
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
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'kategori_id' => 'nullable|exists:categories,id',
            'stok' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();
        $data['kode_buku'] = 'BK-' . strtoupper(Str::random(8));
        $data['available_stock'] = $request->stok;

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/covers'), $filename);
            $data['cover'] = 'uploads/covers/' . $filename;
        }

        Book::create($data);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
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
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'kategori_id' => 'nullable|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        // Update available_stock jika stok berubah
        if ($request->has('stok')) {
            $data['available_stock'] = $request->stok;
        }

        if ($request->hasFile('cover')) {
            if ($book->cover && file_exists(public_path($book->cover))) {
                unlink(public_path($book->cover));
            }

            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/covers'), $filename);
            $data['cover'] = 'uploads/covers/' . $filename;
        }

        $book->update($data);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diupdate.');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Book $book)
    {
        if ($book->cover && file_exists(public_path($book->cover))) {
            unlink(public_path($book->cover));
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
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
     * Display the specified book.
     */
    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return view('books.show', compact('book'));
    }
    /**
     * Import book from API.
     */
    public function importFromApi(Request $request)
    {
        $request->validate([
            'api_id' => 'required|string',
            'judul' => 'required|string',
            'penulis' => 'required|string',
            'penerbit' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer',
            'kategori_id' => 'nullable|exists:categories,id',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:1'
        ]);

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

        // Download cover dari API jika ada dan bookApi tersedia
        if ($request->cover_url && $this->bookApi) {
            $coverPath = $this->bookApi->downloadCover($request->cover_url, $book->id);
            if ($coverPath) {
                $book->update(['cover' => $coverPath]);
            }
        }


        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diimport dari API');
    }
}