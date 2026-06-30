<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ubah ke true agar authorized
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book') ? $this->route('book')->id : null;
        
        $rules = [
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'kategori_id' => 'nullable|exists:categories,id',
            'stok' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ];

        // Untuk update (PUT/PATCH), stok minimal 0
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['stok'] = 'required|integer|min:0';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Judul
            'judul.required' => 'Judul buku wajib diisi',
            'judul.string' => 'Judul buku harus berupa teks',
            'judul.max' => 'Judul buku maksimal 255 karakter',

            // Penulis
            'penulis.required' => 'Nama penulis wajib diisi',
            'penulis.string' => 'Nama penulis harus berupa teks',
            'penulis.max' => 'Nama penulis maksimal 255 karakter',

            // Penerbit
            'penerbit.string' => 'Penerbit harus berupa teks',
            'penerbit.max' => 'Penerbit maksimal 255 karakter',

            // Tahun Terbit
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka',
            'tahun_terbit.min' => 'Tahun terbit minimal 1900',
            'tahun_terbit.max' => 'Tahun terbit maksimal ' . date('Y'),

            // Kategori
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid',

            // Stok
            'stok.required' => 'Stok buku wajib diisi',
            'stok.integer' => 'Stok harus berupa angka',
            'stok.min' => 'Stok minimal :min',

            // Deskripsi
            'deskripsi.string' => 'Deskripsi harus berupa teks',

            // Cover
            'cover.image' => 'File yang diupload harus berupa gambar',
            'cover.mimes' => 'Format gambar harus: jpeg, png, jpg, gif, atau webp',
            'cover.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'judul' => 'Judul Buku',
            'penulis' => 'Penulis',
            'penerbit' => 'Penerbit',
            'tahun_terbit' => 'Tahun Terbit',
            'kategori_id' => 'Kategori',
            'stok' => 'Stok',
            'deskripsi' => 'Deskripsi',
            'cover' => 'Cover Buku',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null
        $this->merge([
            'penerbit' => $this->penerbit ?: null,
            'tahun_terbit' => $this->tahun_terbit ?: null,
            'deskripsi' => $this->deskripsi ?: null,
        ]);
    }

    /**
     * Get the validated data with defaults.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        
        // Set default values if not present
        if (!isset($data['available_stock'])) {
            $data['available_stock'] = $data['stok'] ?? 0;
        }
        
        return $data;
    }
}