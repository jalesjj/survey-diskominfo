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
        if (!session('admin_id') && !session('admin_user') && !session('admin')) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai admin terlebih dahulu.');
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
        $activePeriod = SurveyPeriod::getActivePeriod();

        return view('admin.criterias.index', compact('criterias', 'activePeriod'));
    }

    /**
     * Form tambah kriteria
     */
    public function create()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $activePeriod = SurveyPeriod::getActivePeriod();

        return view('admin.criterias.create', compact('activePeriod'));
    }

    /**
     * Simpan kriteria baru
     */
    public function store(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Blokir jika ada periode aktif
        if (SurveyPeriod::getActivePeriod()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Tidak dapat menambah kriteria saat periode survei sedang aktif.');
        }

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
        $activePeriod = SurveyPeriod::getActivePeriod();

        return view('admin.criterias.edit', compact('criteria', 'activePeriod'));
    }

    /**
     * Update kriteria — semua pertanyaan yang pakai otomatis ikut karena relasi FK
     */
    public function update(Request $request, $id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Blokir jika ada periode aktif
        if (SurveyPeriod::getActivePeriod()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Tidak dapat mengubah kriteria saat periode survei sedang aktif.');
        }

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
     * Hapus kriteria
     */
    public function destroy($id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Blokir jika ada periode aktif
        if (SurveyPeriod::getActivePeriod()) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', 'Tidak dapat menghapus kriteria saat periode survei sedang aktif.');
        }

        $criteria = Criteria::withCount('questions')->findOrFail($id);

        if ($criteria->questions_count > 0) {
            return redirect()->route('admin.criterias.index')
                             ->with('error', "Kriteria \"{$criteria->criteria_name}\" tidak bisa dihapus karena masih digunakan oleh {$criteria->questions_count} pertanyaan.");
        }

        $criteria->delete();

        return redirect()->route('admin.criterias.index')
                         ->with('success', 'Kriteria berhasil dihapus.');
    }
}