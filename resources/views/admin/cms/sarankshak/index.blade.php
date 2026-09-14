@extends('layouts.admin')

@section('title', 'Manage Our Sarankshak - Admin Panel')
@section('header_title', 'Our Sarankshak Members')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Sarankshak Members list -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Active Sarankshak Members</h3>
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
                                    {{ $member->designation ?? 'Our Sarankshak Member' }}
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
                            <form action="{{ route('admin.cms.sarankshak.toggle', $member->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded text-xs font-bold transition
                                    {{ $member->status ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    {{ $member->status ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.cms.sarankshak.destroy', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this Sarankshak member?');">
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
                <div class="p-6 text-center text-gray-500 text-sm">No Sarankshak members added yet.</div>
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
                Add Our Sarankshak Member
            </h3>

            <form action="{{ route('admin.cms.sarankshak.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Name (Hindi) *</label>
                    <input type="text" name="name" required placeholder="जैसे: नरेन्द्र जैन" value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('name') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Name (English)</label>
                    <input type="text" name="name_en" placeholder="e.g. Narendra Jain" value="{{ old('name_en') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('name_en') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Designation (HI)</label>
                        <input type="text" name="designation" placeholder="Our Sarankshak Member" value="{{ old('designation', 'Our Sarankshak Member') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Designation (EN)</label>
                        <input type="text" name="designation_en" placeholder="Our Sarankshak Member" value="{{ old('designation_en', 'Our Sarankshak Member') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Description (Hindi)</label>
                    <textarea name="description" rows="3" placeholder="सदस्य के बारे में हिन्दी में विवरण..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Description (English)</label>
                    <textarea name="description_en" rows="3" placeholder="English description..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description_en') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 items-center">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Photo</label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition duration-150 text-sm shadow-md shadow-primary/20 mt-2">
                    Add Member
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Edit Member Modal -->
<div id="editMemberModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-lg w-full p-6 m-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
            <h3 class="font-bold text-gray-800 text-lg">Edit Sarankshak Member</h3>
            <button onclick="closeEditMemberModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editMemberForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Name (Hindi) *</label>
                <input type="text" name="name" id="edit_name" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Name (English)</label>
                <input type="text" name="name_en" id="edit_name_en"
                    class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Designation (HI)</label>
                    <input type="text" name="designation" id="edit_designation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Designation (EN)</label>
                    <input type="text" name="designation_en" id="edit_designation_en"
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Description (Hindi)</label>
                <textarea name="description" id="edit_description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Description (English)</label>
                <textarea name="description_en" id="edit_description_en" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3 items-center">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_sort_order"
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">New Photo (Optional)</label>
                    <input type="file" name="photo" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="status" id="edit_status" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                <label for="edit_status" class="text-xs font-bold text-gray-700">Active Member</label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeEditMemberModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition text-sm shadow-md shadow-primary/20">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditMemberModal(member) {
        document.getElementById('editMemberForm').action = `/admin/cms/sarankshak/${member.id}`;
        document.getElementById('edit_name').value = member.name || '';
        document.getElementById('edit_name_en').value = member.name_en || '';
        document.getElementById('edit_designation').value = member.designation || '';
        document.getElementById('edit_designation_en').value = member.designation_en || '';
        document.getElementById('edit_description').value = member.description || '';
        document.getElementById('edit_description_en').value = member.description_en || '';
        document.getElementById('edit_sort_order').value = member.sort_order ?? 0;
        document.getElementById('edit_status').checked = !!member.status;

        document.getElementById('editMemberModal').classList.remove('hidden');
    }

    function closeEditMemberModal() {
        document.getElementById('editMemberModal').classList.add('hidden');
    }
</script>
@endsection
