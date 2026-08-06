<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommitteeController extends Controller
{
    /**
     * Display a listing of the committee members.
     */
    public function index()
    {
        $members = CommitteeMember::orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15);
            
        return view('admin.cms.committee.index', compact('members'));
    }

    /**
     * Store a newly created committee member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:150',
            'designation_en' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'photo' => 'nullable|image|max:10240', // 10MB max
            'sort_order' => 'nullable|integer',
        ]);

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                try {
                    $file = $request->file('photo');
                    $filename = time() . '_committee_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $filename);
                    $photoPath = 'uploads/' . $filename;
                } catch (\Exception $uploadErr) {
                    $path = $request->file('photo')->getRealPath();
                    $type = $request->file('photo')->getClientMimeType();
                    $data = file_get_contents($path);
                    $photoPath = 'data:' . $type . ';base64,' . base64_encode($data);
                }
            }

            CommitteeMember::create([
                'name' => $request->name,
                'name_en' => $request->name_en,
                'designation' => $request->designation ?? 'Committee Member',
                'designation_en' => $request->designation_en ?? 'Committee Member',
                'description' => $request->description,
                'description_en' => $request->description_en,
                'photo' => $photoPath,
                'sort_order' => $request->sort_order ?? 0,
                'status' => true,
            ]);

            return back()->with('success', 'Committee member added successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to add committee member: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified committee member.
     */
    public function update(Request $request, CommitteeMember $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:150',
            'designation_en' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'name_en' => $request->name_en,
                'designation' => $request->designation,
                'designation_en' => $request->designation_en,
                'description' => $request->description,
                'description_en' => $request->description_en,
                'sort_order' => $request->sort_order ?? 0,
                'status' => $request->has('status'),
            ];

            if ($request->hasFile('photo')) {
                try {
                    $file = $request->file('photo');
                    $filename = time() . '_committee_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $filename);
                    $updateData['photo'] = 'uploads/' . $filename;
                } catch (\Exception $uploadErr) {
                    $path = $request->file('photo')->getRealPath();
                    $type = $request->file('photo')->getClientMimeType();
                    $data = file_get_contents($path);
                    $updateData['photo'] = 'data:' . $type . ';base64,' . base64_encode($data);
                }
            }

            $member->update($updateData);

            return back()->with('success', 'Committee member updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update committee member: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of a committee member.
     */
    public function toggle(CommitteeMember $member)
    {
        $member->update(['status' => !$member->status]);
        return back()->with('success', 'Committee member status updated.');
    }

    /**
     * Remove the specified committee member from storage.
     */
    public function destroy(CommitteeMember $member)
    {
        $member->delete();
        return back()->with('success', 'Committee member deleted successfully.');
    }
}
