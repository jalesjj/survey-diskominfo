<?php
// app/Http/Controllers/CriteriaController.php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\SurveyPeriod;
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
     * Cek apakah sistem sedang terkunci (ada periode aktif)
     */
    private function isLocked(): bool
    {
        return SurveyPeriod::where('is_active', true)->exists();
    }

    /**
     * List semua kriteria
     */
    public function index()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $criterias     = Criteria::withCount('questions')->orderBy('criteria_name')->get();
        $isLocked      = $this->isLocked();
        $activePeriod  = SurveyPeriod::where('is_active', true)->first();

        return view('admin.criterias.index', compact('criterias', 'isLocked', 'activePeriod'));
    }

    /**
     * Form tambah kriteria — ditolak jika sistem locked
     */
    public function create()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        if ($this->isLocked()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Sistem sedang terkunci. Kriteria tidak dapat ditambah selama periode survey aktif.');
        }

        return view('admin.criterias.create');
    }

    /**
     * Simpan kriteria baru — ditolak jika sistem locked
     */
    public function store(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        if ($this->isLocked()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Sistem sedang terkunci. Kriteria tidak dapat ditambah selama periode survey aktif.');
        }

        $request->validate([
            'criteria_name'   => 'required|string|max:255|unique:criterias,criteria_name',
            'criteria_weight' => 'required|integer|in:2,4,6,8,10',
            'criteria_type'   => 'required|in:benefit,cost',
        ], [
            'criteria_name.unique'   => 'Nama kriteria sudah ada, gunakan nama lain.',
            'criteria_weight.in'     => 'Bobot harus salah satu dari: 2, 4, 6, 8, atau 10.',
        ]);

        Criteria::create($request->only('criteria_name', 'criteria_weight', 'criteria_type'));

        return redirect()->route('admin.criterias.index')
                         ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    /**
     * Form edit kriteria — ditolak jika sistem locked
     */
    public function edit($id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        if ($this->isLocked()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Sistem sedang terkunci. Kriteria tidak dapat diedit selama periode survey aktif.');
        }

        $criteria = Criteria::withCount('questions')->findOrFail($id);

        return view('admin.criterias.edit', compact('criteria'));
    }

    /**
     * Update kriteria — ditolak jika sistem locked
     */
    public function update(Request $request, $id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        if ($this->isLocked()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Sistem sedang terkunci. Kriteria tidak dapat diubah selama periode survey aktif.');
        }

        $criteria = Criteria::findOrFail($id);

        $request->validate([
            'criteria_name'   => 'required|string|max:255|unique:criterias,criteria_name,' . $id,
            'criteria_weight' => 'required|integer|in:2,4,6,8,10',
            'criteria_type'   => 'required|in:benefit,cost',
        ], [
            'criteria_name.unique' => 'Nama kriteria sudah dipakai oleh kriteria lain.',
            'criteria_weight.in'   => 'Bobot harus salah satu dari: 2, 4, 6, 8, atau 10.',
        ]);

        $criteria->update($request->only('criteria_name', 'criteria_weight', 'criteria_type'));

        return redirect()->route('admin.criterias.index')
                         ->with('success', 'Kriteria berhasil diperbarui. Semua pertanyaan yang menggunakan kriteria ini otomatis mengikuti perubahan.');
    }

    /**
     * Hapus kriteria — ditolak jika sistem locked atau masih dipakai
     */
    public function destroy($id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        if ($this->isLocked()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Sistem sedang terkunci. Kriteria tidak dapat dihapus selama periode survey aktif.');
        }

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