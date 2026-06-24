<?php
namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::paginate(10);
        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:members',
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'required|string'
        ]);

        Member::create([
            'kode_member' => 'MB-' . strtoupper(Str::random(8)),
            'nama' => $request->nama,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'tanggal_daftar' => Carbon::now(),
            'status' => 'aktif'
        ]);

        return redirect()->route('members.index')
                        ->with('success', 'Member berhasil ditambahkan.');
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $member->update($request->all());

        return redirect()->route('members.index')
                        ->with('success', 'Member berhasil diupdate.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index')
                        ->with('success', 'Member berhasil dihapus.');
    }
}