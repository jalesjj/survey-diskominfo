<?php
// app/Http/Controllers/CriteriaController.php

namespace App\Http\Controllers;

use App\Models\Criteria;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    private function checkAdminAuth()
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }
        return null;
    }

    /**
     * List semua kriteria
     */
    public function index()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $criterias = Criteria::withCount('questions')->orderBy('criteria_name')->get();

        return view('admin.criterias.index', compact('criterias'));
    }

    /**
     * Form tambah kriteria
     */
    public function create()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        return view('admin.criterias.create');
    }

    /**
     * Simpan kriteria baru
     */
    public function store(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $request->validate([
            'criteria_name'   => 'required|string|max:255|unique:criterias,criteria_name',
            'criteria_weight' => 'required|numeric|min:0.1|max:10',
            'criteria_type'   => 'required|in:benefit,cost',
        ], [
            'criteria_name.unique' => 'Nama kriteria sudah ada, gunakan nama lain.',
        ]);

        Criteria::create($request->only('criteria_name', 'criteria_weight', 'criteria_type'));

        return redirect()->route('admin.criterias.index')
                         ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    /**
     * Form edit kriteria
     */
    public function edit($id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $criteria = Criteria::withCount('questions')->findOrFail($id);

        return view('admin.criterias.edit', compact('criteria'));
    }

    /**
     * Update kriteria — semua pertanyaan yang pakai otomatis ikut karena relasi FK
     */
    public function update(Request $request, $id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $criteria = Criteria::findOrFail($id);

        $request->validate([
            'criteria_name'   => 'required|string|max:255|unique:criterias,criteria_name,' . $id,
            'criteria_weight' => 'required|numeric|min:0.1|max:10',
            'criteria_type'   => 'required|in:benefit,cost',
        ], [
            'criteria_name.unique' => 'Nama kriteria sudah dipakai oleh kriteria lain.',
        ]);

        $criteria->update($request->only('criteria_name', 'criteria_weight', 'criteria_type'));

        return redirect()->route('admin.criterias.index')
                         ->with('success', 'Kriteria berhasil diperbarui. Semua pertanyaan yang menggunakan kriteria ini otomatis mengikuti perubahan.');
    }

    /**
     * Hapus kriteria — ditolak jika masih dipakai pertanyaan
     */
    public function destroy($id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $criteria = Criteria::withCount('questions')->findOrFail($id);

        if ($criteria->isInUse()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', "Kriteria \"{$criteria->criteria_name}\" tidak bisa dihapus karena masih digunakan oleh {$criteria->questions_count} pertanyaan. Pindahkan pertanyaan tersebut ke kriteria lain terlebih dahulu.");
        }

        $criteria->delete();

        return redirect()->route('admin.criterias.index')
                         ->with('success', 'Kriteria berhasil dihapus.');
    }
}