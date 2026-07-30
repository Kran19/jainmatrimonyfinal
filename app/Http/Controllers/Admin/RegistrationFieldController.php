<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegistrationField;

class RegistrationFieldController extends Controller
{
    /**
     * Display a listing of registration fields and dynamic setups.
     */
    public function index()
    {
        // Self-heal core subcast & cast fields if missing in database
        if (\Illuminate\Support\Facades\Schema::hasTable('registration_fields')) {
            if (!RegistrationField::where('field_key', 'subcast')->exists()) {
                RegistrationField::create([
                    'field_group' => 'Personal Details',
                    'field_key' => 'subcast',
                    'field_label' => 'Sub-Cast (उपजाति)',
                    'field_type' => 'dropdown',
                    'field_options' => 'Khandelwal,Agrawal,Oswal,Porwal,Golalare,Humad,Bagherwal,Chaturth,Pancham,Other (अन्य)',
                    'is_custom' => false,
                    'is_visible' => true,
                    'is_required' => false,
                    'is_core' => false,
                    'sort_order' => 2,
                ]);
            }
            if (!RegistrationField::where('field_key', 'cast')->exists()) {
                RegistrationField::create([
                    'field_group' => 'Personal Details',
                    'field_key' => 'cast',
                    'field_label' => 'Cast (जाति)',
                    'field_type' => 'dropdown',
                    'field_options' => 'Digambar Jain,Other',
                    'is_custom' => false,
                    'is_visible' => true,
                    'is_required' => true,
                    'is_core' => false,
                    'sort_order' => 1,
                ]);
            }
        }

        $fields = RegistrationField::orderBy('is_custom', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $groupedFields = [];
        $customFields = [];

        foreach ($fields as $field) {
            if ($field->is_custom) {
                $customFields[] = $field;
            } else {
                $group = $field->field_group ?: 'Additional Information';
                $groupedFields[$group][] = $field;
            }
        }

        return view('admin.cms.registration-fields', compact('groupedFields', 'customFields'));
    }

    /**
     * Store a newly created custom registration field.
     */
    public function store(Request $request)
    {
        $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,number,textarea,dropdown,radio,checkbox,date',
            'field_group' => 'required|string|max:255',
            'dynamic_options' => 'nullable|array',
            'dynamic_options.*' => 'nullable|string',
            'is_required' => 'nullable|boolean',
        ]);

        $label = trim($request->field_label);
        $key = 'custom_' . strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $label));
        $key = trim($key, '_');

        // Check uniqueness of field key
        if (RegistrationField::where('field_key', $key)->exists()) {
            return back()->with('error', "A field with key '$key' already exists. Try a different label.");
        }

        $options = '';
        if (in_array($request->field_type, ['dropdown', 'radio', 'checkbox']) && is_array($request->dynamic_options)) {
            $validOpts = array_filter(array_map('trim', $request->dynamic_options));
            $options = implode(',', $validOpts);
        }

        // Get max sort_order
        $maxSort = RegistrationField::max('sort_order') ?? 0;

        RegistrationField::create([
            'field_group' => $request->field_group,
            'field_key' => $key,
            'field_label' => $label,
            'field_type' => $request->field_type,
            'field_options' => $options,
            'is_custom' => true,
            'is_visible' => true,
            'is_required' => $request->has('is_required'),
            'is_core' => false,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('admin.registration-fields.index')->with('success', 'Custom registration field added successfully.');
    }

    /**
     * Update the options of a select, radio or checkbox field.
     */
    public function updateOptions(Request $request, $id)
    {
        $request->validate([
            'edit_dynamic_options' => 'required|array|min:1',
            'edit_dynamic_options.*' => 'required|string',
        ]);

        $field = RegistrationField::findOrFail($id);
        
        if (!in_array($field->field_type, ['dropdown', 'radio', 'checkbox'])) {
            return back()->with('error', 'Only choice-based fields can have options.');
        }

        $validOpts = array_filter(array_map('trim', $request->edit_dynamic_options));
        $field->update([
            'field_options' => implode(',', $validOpts)
        ]);

        return redirect()->route('admin.registration-fields.index')->with('success', 'Field options updated successfully.');
    }

    /**
     * Bulk save visibility and required preferences.
     */
    public function saveVisibility(Request $request)
    {
        // Reset all visible & required flags first for non-core fields
        RegistrationField::where('is_core', false)->update([
            'is_visible' => false,
            'is_required' => false
        ]);

        // Process visible fields
        if ($request->has('visible_fields') && is_array($request->visible_fields)) {
            foreach ($request->visible_fields as $id) {
                RegistrationField::where('id', $id)->update(['is_visible' => true]);
            }
        }

        // Process required fields
        if ($request->has('required_fields') && is_array($request->required_fields)) {
            foreach ($request->required_fields as $id) {
                RegistrationField::where('id', $id)->update(['is_required' => true]);
            }
        }

        return redirect()->route('admin.registration-fields.index')->with('success', 'Form field visibility configuration updated successfully.');
    }

    /**
     * Remove a custom field.
     */
    public function destroy($id)
    {
        $field = RegistrationField::findOrFail($id);
        if (!$field->is_custom) {
            return back()->with('error', 'Core fields cannot be deleted.');
        }

        $field->delete();
        return redirect()->route('admin.registration-fields.index')->with('success', 'Custom field deleted successfully.');
    }
}
