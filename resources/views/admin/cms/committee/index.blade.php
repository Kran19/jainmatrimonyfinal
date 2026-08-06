@extends('layouts.admin')

@section('title', 'Manage Committee Members - Admin Panel')
@section('header_title', 'Committee Members')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Committee Members list -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Active Committee Members</h3>
                <span class="text-xs text-gray-400 font-bold bg-slate-100 px-3 py-1 rounded-full">
                    Total: {{ $members->total() }}
                </span>
            </div>
            
            <div class="divide-y divide-gray-100">
                @forelse($members as $member)
                <div class="p-6 flex items-start gap-4 hover:bg-slate-50 transition duration-150">
                    @if($member->photo)
                        @php
                            $imgSrc = (str_starts_with($member->photo, 'data:') || str_starts_with($member->photo, 'http')) ? $member->photo : asset($member->photo);
                        @endphp
                        <img src="{{ $imgSrc }}" alt="Photo" class="w-16 h-16 object-cover rounded-full border-2 border-primary flex-shrink-0">
                    @else
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 flex-shrink-0 border-2 border-slate-200">
                            <i class="fa-solid fa-user text-xl"></i>
                        </div>
                    @endif
                    <div class="flex-grow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-gray-900 text-base">
                                    {{ $member->name }} 
                                    @if($member->name_en)
                                        <span class="text-xs font-normal text-slate-400">({{ $member->name_en }})</span>
                                    @endif
                                </h4>
                                <div class="text-xs text-slate-400 font-bold mt-0.5">
                                    {{ $member->designation ?? 'Committee Member' }}
                                    @if($member->designation_en)
                                        | {{ $member->designation_en }}
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">Order: {{ $member->sort_order }}</span>
                        </div>
                        
                        <!-- Description translation boxes -->
                        <div class="mt-2 text-xs text-gray-600 space-y-1">
                            @if($member->description)
                                <p><strong class="text-slate-400">HI:</strong> {{ Str::limit($member->description, 150) }}</p>
                            @endif
                            @if($member->description_en)
                                <p><strong class="text-slate-400">EN:</strong> {{ Str::limit($member->description_en, 150) }}</p>
                            @endif
                        </div>
                        
                        <div class="mt-3 flex items-center gap-3">
                            <button onclick='openEditMemberModal({!! json_encode($member) !!})' class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
                                Edit Details
                            </button>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.cms.committee.toggle', $member->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded text-xs font-bold transition
                                    {{ $member->status ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    {{ $member->status ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.cms.committee.destroy', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this committee member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-500">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 text-sm">No committee members added.</div>
                @endforelse
            </div>
            
            @if($members->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
                {{ $members->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Add Form -->
    <div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-6">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Add Committee Member
            </h3>
            <form action="{{ route('admin.cms.committee.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="name" class="block font-semibold text-gray-700 mb-1">Name (Hindi) *</label>
                    <input type="text" name="name" id="name" required placeholder="जैसे: नरेन्द्र जैन"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label for="name_en" class="block font-semibold text-gray-700 mb-1">Name (English)</label>
                    <input type="text" name="name_en" id="name_en" placeholder="e.g. Narendra Jain"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="designation" class="block font-semibold text-gray-700 mb-1">Designation (HI)</label>
                        <input type="text" name="designation" id="designation" placeholder="जैसे: समिति सदस्य" value="Committee Member"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="designation_en" class="block font-semibold text-gray-700 mb-1">Designation (EN)</label>
                        <input type="text" name="designation_en" id="designation_en" placeholder="e.g. Committee Member" value="Committee Member"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="description" class="block font-semibold text-gray-700 mb-1">Description (Hindi)</label>
                    <textarea name="description" id="description" rows="3" placeholder="सदस्य के बारे में हिन्दी में विवरण..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label for="description_en" class="block font-semibold text-gray-700 mb-1">Description (English)</label>
                    <textarea name="description_en" id="description_en" rows="3" placeholder="English description..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sort_order" class="block font-semibold text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="0" min="0"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="photo" class="block font-semibold text-gray-700 mb-1">Photo</label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow">
                    Add Member
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Edit Member Modal -->
<div id="editMemberModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 mx-4 max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h3 class="font-bold text-gray-800 text-lg">Edit Committee Member</h3>
            <button type="button" onclick="closeEditMemberModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <form id="editMemberForm" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
            @csrf
            @method('PUT')
            
            <div>
                <label for="edit_name" class="block font-semibold text-gray-700 mb-1">Name (Hindi) *</label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label for="edit_name_en" class="block font-semibold text-gray-700 mb-1">Name (English)</label>
                <input type="text" name="name_en" id="edit_name_en"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_designation" class="block font-semibold text-gray-700 mb-1">Designation (HI)</label>
                    <input type="text" name="designation" id="edit_designation"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="edit_designation_en" class="block font-semibold text-gray-700 mb-1">Designation (EN)</label>
                    <input type="text" name="designation_en" id="edit_designation_en"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="edit_description" class="block font-semibold text-gray-700 mb-1">Description (Hindi)</label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label for="edit_description_en" class="block font-semibold text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" id="edit_description_en" rows="3"
                          class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_sort_order" class="block font-semibold text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_sort_order" min="0"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="edit_photo" class="block font-semibold text-gray-700 mb-1">Photo (Upload new to replace)</label>
                    <input type="file" name="photo" id="edit_photo" accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <div class="flex items-center gap-2 py-1">
                <input type="checkbox" name="status" id="edit_status" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="edit_status" class="font-semibold text-gray-700">Show on website (Active)</label>
            </div>

            <div class="flex justify-end gap-3 border-t pt-4">
                <button type="button" onclick="closeEditMemberModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditMemberModal(member) {
    const form = document.getElementById('editMemberForm');
    form.action = `/admin/cms/committee/${member.id}`;
    
    document.getElementById('edit_name').value = member.name || '';
    document.getElementById('edit_name_en').value = member.name_en || '';
    document.getElementById('edit_designation').value = member.designation || '';
    document.getElementById('edit_designation_en').value = member.designation_en || '';
    document.getElementById('edit_description').value = member.description || '';
    document.getElementById('edit_description_en').value = member.description_en || '';
    document.getElementById('edit_sort_order').value = member.sort_order || 0;
    
    document.getElementById('edit_status').checked = !!member.status;
    
    document.getElementById('editMemberModal').classList.remove('hidden');
}

function closeEditMemberModal() {
    document.getElementById('editMemberModal').classList.add('hidden');
}
</script>
@endsection
