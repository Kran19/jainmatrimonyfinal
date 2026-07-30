@extends('layouts.admin')

@section('title', 'Registration Form Setup - Admin Panel')
@section('header_title', 'Registration Form Manager')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <p class="text-gray-500 text-sm">Configure field visibility, requirements, and manage custom questions on candidate registration.</p>
    </div>
    <button type="submit" form="visibilityForm" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-sm flex items-center">
        <i class="fa-solid fa-floppy-disk mr-2"></i> Save Visibility Setup
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Standard & Custom Fields List -->
    <div class="lg:col-span-2 space-y-6">
        <form id="visibilityForm" method="POST" action="{{ route('admin.registration-fields.visibility') }}">
            @csrf
            
            @foreach ($groupedFields as $groupName => $groupFields)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="bg-slate-50/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-folder text-slate-400"></i> {{ $groupName }}
                    </h4>
                </div>
                <div>
                    <ul class="divide-y divide-gray-100">
                        @foreach ($groupFields as $field)
                        <li class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 px-6 hover:bg-slate-50/30 transition duration-150">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-semibold text-gray-800">{{ $field->field_label }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Type: <span class="font-mono">{{ $field->field_type }}</span></p>
                            </div>
                            <div class="flex items-center gap-6">
                                @if (in_array($field->field_type, ['dropdown', 'radio', 'checkbox']))
                                    <button type="button" class="text-xs text-indigo-600 font-bold hover:bg-indigo-50 bg-indigo-50/50 px-2.5 py-1.5 rounded-lg border border-indigo-100 flex items-center gap-1 transition" onclick='openEditOptionsModal({!! json_encode($field) !!})'>
                                        <i class="fa-solid fa-list-check"></i> Edit Options
                                    </button>
                                @endif
                                
                                @if ($field->is_core)
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-bold border border-emerald-100">Core (Always Required)</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-1.5">
                                            <input type="checkbox" name="visible_fields[]" value="{{ $field->id }}" id="vis_{{ $field->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ $field->is_visible ? 'checked' : '' }}>
                                            <label for="vis_{{ $field->id }}" class="text-xs font-semibold text-gray-500 select-none cursor-pointer">Visible</label>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <input type="checkbox" name="required_fields[]" value="{{ $field->id }}" id="req_{{ $field->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ $field->is_required ? 'checked' : '' }}>
                                            <label for="req_{{ $field->id }}" class="text-xs font-semibold text-gray-500 select-none cursor-pointer">Required</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach

            <!-- Custom fields listed as a group if any -->
            @if(count($customFields) > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="bg-slate-50/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-indigo-500"></i> Custom Fields (Additional Info)
                    </h4>
                </div>
                <div>
                    <ul class="divide-y divide-gray-100">
                        @foreach ($customFields as $field)
                        <li class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 px-6 hover:bg-slate-50/30 transition duration-150">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-semibold text-gray-800">{{ $field->field_label }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Section: <span class="font-bold text-indigo-600">{{ $field->field_group }}</span> | Type: <span class="font-mono">{{ $field->field_type }}</span></p>
                            </div>
                            <div class="flex items-center gap-4">
                                @if (in_array($field->field_type, ['dropdown', 'radio', 'checkbox']))
                                    <button type="button" class="text-xs text-indigo-600 font-bold hover:bg-indigo-50 bg-indigo-50/50 px-2.5 py-1.5 rounded-lg border border-indigo-100 flex items-center gap-1 transition" onclick='openEditOptionsModal({!! json_encode($field) !!})'>
                                        <i class="fa-solid fa-list-check"></i> Edit Options
                                    </button>
                                @endif

                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1.5">
                                        <input type="checkbox" name="visible_fields[]" value="{{ $field->id }}" id="vis_{{ $field->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ $field->is_visible ? 'checked' : '' }}>
                                        <label for="vis_{{ $field->id }}" class="text-xs font-semibold text-gray-500 select-none cursor-pointer">Visible</label>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <input type="checkbox" name="required_fields[]" value="{{ $field->id }}" id="req_{{ $field->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ $field->is_required ? 'checked' : '' }}>
                                        <label for="req_{{ $field->id }}" class="text-xs font-semibold text-gray-500 select-none cursor-pointer">Required</label>
                                    </div>
                                </div>

                                <button type="button" onclick="confirmDeleteField('{{ route('admin.registration-fields.destroy', $field->id) }}')" class="text-red-500 hover:text-red-600 transition text-xs font-bold flex items-center gap-1 ml-2">
                                    <i class="fa-solid fa-trash-can"></i> Delete
                                </button>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </form>
    </div>

    <!-- Custom Fields Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-circle-plus text-indigo-600 text-lg"></i> Add Custom Field
                </h4>
            </div>
            <div class="p-6">
                <form class="space-y-4" method="POST" action="{{ route('admin.registration-fields.store') }}">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Field Label *</label>
                        <input type="text" name="field_label" required placeholder="e.g. Diet Preference" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Form Section *</label>
                        <select name="field_group" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white" required>
                            <option value="Personal Details">Personal Details (Step 2)</option>
                            <option value="Professional Details">Professional Details (Step 3)</option>
                            <option value="Family Details">Family Details (Step 4)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Field Type *</label>
                        <select name="field_type" id="field_type" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white" required onchange="toggleOptionsInput(this.value)">
                            <option value="text">Text Input</option>
                            <option value="number">Number Input</option>
                            <option value="textarea">Textarea Block</option>
                            <option value="date">Date Picker</option>
                            <option value="dropdown">Dropdown Select</option>
                            <option value="radio">Radio Buttons</option>
                            <option value="checkbox">Checkboxes</option>
                        </select>
                    </div>

                    <!-- Options inputs for choice-based types -->
                    <div id="optionsContainer" class="hidden space-y-3">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Field Options *</label>
                        <div id="dynamicOptionList" class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="dynamic_options[]" placeholder="Option 1" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm">
                                <button type="button" onclick="removeOptionField(this)" class="text-red-500 hover:text-red-600"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>
                        <button type="button" onclick="addOptionField()" class="text-xs text-indigo-600 font-bold hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-circle-plus"></i> Add Option
                        </button>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_required" id="is_required" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 mr-2">
                        <label for="is_required" class="text-sm font-semibold text-gray-700 select-none cursor-pointer">Make this field Required</label>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-sm mt-4">
                        Create Custom Field
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Options Modal -->
<div id="optionsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Edit Options for: <span id="modalFieldName" class="text-indigo-600">Field</span></h3>
            <button class="text-gray-400 hover:text-gray-600 transition" onclick="closeOptionsModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="editOptionsForm" method="POST" action="" class="space-y-4">
                @csrf
                <div id="editOptionsList" class="space-y-2 max-h-60 overflow-y-auto pr-2">
                    <!-- Loaded dynamically -->
                </div>
                <button type="button" onclick="addEditOptionField()" class="text-xs text-indigo-600 font-bold hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-circle-plus"></i> Add Option Choice
                </button>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" class="px-5 py-2 text-sm font-bold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-slate-50 transition" onclick="closeOptionsModal()">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">Save Options</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Dynamic Options for creation
function toggleOptionsInput(val) {
    const container = document.getElementById('optionsContainer');
    if (['dropdown', 'radio', 'checkbox'].includes(val)) {
        container.classList.remove('hidden');
        const list = document.getElementById('dynamicOptionList');
        if (list.children.length === 0) {
            addOptionField();
        }
    } else {
        container.classList.add('hidden');
    }
}

function addOptionField() {
    const list = document.getElementById('dynamicOptionList');
    const index = list.children.length + 1;
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="dynamic_options[]" placeholder="Option ${index}" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm">
        <button type="button" onclick="removeOptionField(this)" class="text-red-500 hover:text-red-600"><i class="fa-solid fa-trash-can"></i></button>
    `;
    list.appendChild(div);
}

function removeOptionField(btn) {
    const list = document.getElementById('dynamicOptionList');
    if (list.children.length > 1) {
        btn.parentElement.remove();
    }
}

// Options Modal for editing
const optionsModal = document.getElementById('optionsModal');
const editOptionsForm = document.getElementById('editOptionsForm');

function openEditOptionsModal(field) {
    document.getElementById('modalFieldName').textContent = field.field_label;
    editOptionsForm.action = `/admin/registration-fields/${field.id}/options`;
    
    const list = document.getElementById('editOptionsList');
    list.innerHTML = '';
    
    let options = field.field_options ? field.field_options.split(',') : [];
    
    if (options.length === 0) {
        options = [''];
    }
    
    options.forEach(opt => {
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
            <input type="text" name="edit_dynamic_options[]" value="${opt.trim()}" placeholder="Option choice" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm">
            <button type="button" onclick="removeEditOptionField(this)" class="text-red-500 hover:text-red-600"><i class="fa-solid fa-trash-can"></i></button>
        `;
        list.appendChild(div);
    });
    
    optionsModal.classList.remove('hidden');
}

function addEditOptionField() {
    const list = document.getElementById('editOptionsList');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="edit_dynamic_options[]" placeholder="New Option choice" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm">
        <button type="button" onclick="removeEditOptionField(this)" class="text-red-500 hover:text-red-600"><i class="fa-solid fa-trash-can"></i></button>
    `;
    list.appendChild(div);
}

function removeEditOptionField(btn) {
    const list = document.getElementById('editOptionsList');
    if (list.children.length > 1) {
        btn.parentElement.remove();
    }
}

function closeOptionsModal() {
    optionsModal.classList.add('hidden');
}

function confirmDeleteField(url) {
    Swal.fire({
        title: 'Delete custom field?',
        text: 'Are you sure you want to delete this custom field? All data submitted by users for this field will also be deleted permanently.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#3b82f6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
