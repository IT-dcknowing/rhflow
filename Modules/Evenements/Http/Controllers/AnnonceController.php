<?php

namespace Modules\Evenements\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Evenements\Models\Announcement;
use Modules\Employees\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class AnnonceController extends Controller
{
    /**
     * Affiche la liste des annonces
     */
    public function index()
    {
        $announcements = Announcement::with(['branch', 'department'])
            ->where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(10);
        
        $branches = Branch::where('company_id', auth()->user()->company_id)->get();
        $departments = Department::where('company_id', auth()->user()->company_id)->get();
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();

        return view('evenements::announcements.index', compact('announcements', 'branches', 'departments', 'employees'));
    }

    /**
     * Affiche le formulaire de création d'une annonce
     */
    public function create()
    {
        $branches = Branch::where('company_id', auth()->user()->company_id)->get();
        $departments = Department::where('company_id', auth()->user()->company_id)->get();
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();
        
        return view('evenements::announcements.create', compact('branches', 'departments', 'employees'));
    }

    /**
     * Enregistre une nouvelle annonce
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'employees' => 'required|array',
            'employees.*' => 'exists:employee_months,id',
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'branch_id' => $validated['branch_id'],
            'department_id' => $validated['department_id'],
            'description' => $validated['description'],
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->id(),
        ]);

        $announcement->employees()->attach($validated['employees'], [
            'company_id' => auth()->user()->company_id
        ]);

        return redirect()->route('company.annonces.index')
            ->with('success', 'Annonce créée avec succès.');
    }

    /**
     * Affiche les détails d'une annonce
     */
    public function show(Announcement $annonce)
    {
        $this->authorize('view', $annonce);
        $annonce->load(['branch', 'department', 'employees']);
        
        return view('evenements::announcements.show', compact('annonce'));
    }

    /**
     * Affiche le formulaire de modification d'une annonce
     */
    public function edit(Announcement $annonce)
    {
        $this->authorize('update', $annonce);
        
        $branches = Branch::where('company_id', auth()->user()->company_id)->get();
        $departments = Department::where('company_id', auth()->user()->company_id)->get();
        $employees = Employee::where('company_id', auth()->user()->company_id)->get();
        $selectedEmployees = $annonce->employees->pluck('id')->toArray();
        
        return view('evenements::announcements.edit', compact(
            'annonce', 'branches', 'departments', 'employees', 'selectedEmployees'
        ));
    }

    /**
     * Met à jour une annonce existante
     */
    public function update(Request $request, Announcement $annonce)
    {
        $this->authorize('update', $annonce);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'employees' => 'required|array',
            'employees.*' => 'exists:employee_months,id',
        ]);

        $annonce->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'branch_id' => $validated['branch_id'],
            'department_id' => $validated['department_id'],
            'description' => $validated['description'],
            'updated_by' => auth()->id(),
        ]);

        $annonce->employees()->syncWithPivotValues(
            $validated['employees'], 
            ['company_id' => auth()->user()->company_id]
        );

        return redirect()->route('company.annonces.show', $annonce->id)
            ->with('success', 'Annonce mise à jour avec succès.');
    }

    /**
     * Supprime une annonce
     */
    public function destroy(Announcement $annonce)
    {
        $this->authorize('delete', $annonce);
        
        $annonce->employees()->detach();
        $annonce->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Annonce supprimée avec succès.'
        ]);
    }
}
