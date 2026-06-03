@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="space-y-space-lg" x-data="{ showAddModal: false, showEditModal: false, editStudent: {}, loading: false }" @submit.document="loading = true">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
                <div class="h-4 bg-surface-container-high rounded skeleton w-32"></div>
            </div>
            <div class="h-10 bg-surface-container-high rounded skeleton w-32"></div>
        </div>
        <div class="h-16 bg-surface-container-low rounded-xl skeleton w-full"></div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-4 shadow-sm">
            <div class="space-y-3">
                @for($i = 0; $i < 6; $i++)
                <div class="grid grid-cols-6 gap-4">
                    <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                    <div class="h-4 bg-surface-container-low rounded skeleton col-span-2"></div>
                    <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                    <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                    <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-space-lg">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-h1 text-primary text-xl font-bold">Kelola Data Siswa</h2>
                <p class="text-on-surface-variant font-body-sm mt-0.5">Total: {{ $students->count() }} siswa terdaftar</p>
            </div>
            <button @click="showAddModal = true" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah Siswa
            </button>
        </div>

        <!-- Filter -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col sm:flex-row gap-3" x-data>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
                    class="flex-1 border border-outline-variant bg-surface rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"/>
                <div class="w-full sm:w-48">
                    <x-custom-select 
                        name="classroom" 
                        :options="$classrooms->map(fn($c) => ['value' => $c->name, 'label' => $c->name])->toArray()" 
                        :selected="request('classroom', '')" 
                        placeholder="Semua Kelas"
                    />
                </div>
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white px-4 py-2 rounded-lg text-xs font-bold cursor-pointer transition-colors">Filter</button>
            </form>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-2.5">
            @forelse($students as $student)
            @php $idx = $loop->iteration; @endphp
            <div class="mobile-card-item">
                <div class="flex gap-3 items-start">
                    <!-- Photo -->
                    <div class="shrink-0">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-10 h-10 rounded-full object-cover border border-outline-variant/30">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold {{ $student->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-primary text-sm truncate">{{ $student->name }}</p>
                        <p class="text-[11px] text-outline mt-0.5">NIS: {{ $student->nis }} • {{ $student->classroom->name }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border shrink-0 {{ $student->gender === 'L' ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-pink-50 border-pink-200 text-pink-600' }}">
                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
                <div class="flex items-center justify-end mt-1.5 pt-1.5 border-t border-outline-variant/20 gap-1">
                    <a href="{{ route('admin.students.show', $student) }}" class="p-1.5 hover:bg-primary/10 rounded-lg text-primary transition-colors">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                    </a>
                    <button @click="editStudent = {{ json_encode($student) }}; showEditModal = true" class="p-1.5 hover:bg-primary/10 rounded-lg text-primary transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}" x-ref="deleteFormMobile{{ $student->id }}" @submit.prevent.stop="triggerConfirm('Hapus Data Siswa', 'Apakah Anda yakin ingin menghapus data siswa {{ $student->name }} secara permanen?', 'Ya, Hapus', 'danger', () => { loading = true; $refs.deleteFormMobile{{ $student->id }}.submit(); })" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 hover:bg-red-50 rounded-lg text-red-500 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-on-surface-variant">
                <span class="material-symbols-outlined text-[48px] text-outline/30 block mb-2">group</span>
                Belum ada data siswa.
            </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block dense-table-wrapper shadow-sm">
            <table class="w-full text-left border-collapse dense-table" style="min-width: 700px;">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Jenis Kelamin</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse($students as $student)
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="text-on-surface-variant font-medium">{{ $loop->iteration }}</td>
                        <td>
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-9 h-9 rounded-full object-cover border border-outline-variant/30">
                            @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-[10px] font-bold {{ $student->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="font-semibold">{{ $student->name }}</span>
                            <span class="block text-[11px] text-outline">NIS: {{ $student->nis }}</span>
                        </td>
                        <td>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $student->gender === 'L' ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-pink-50 border-pink-200 text-pink-600' }}">
                                {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td>{{ $student->classroom->name }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.students.show', $student) }}" class="p-1.5 hover:bg-primary/10 rounded-lg text-primary transition-colors" title="Detail">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </a>
                                <button @click="editStudent = {{ json_encode($student) }}; showEditModal = true" class="p-1.5 hover:bg-primary/10 rounded-lg text-primary transition-colors cursor-pointer" title="Edit">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" x-ref="deleteForm{{ $student->id }}" @submit.prevent.stop="triggerConfirm('Hapus Data Siswa', 'Apakah Anda yakin ingin menghapus data siswa {{ $student->name }} secara permanen?', 'Ya, Hapus', 'danger', () => { loading = true; $refs.deleteForm{{ $student->id }}.submit(); })" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 hover:bg-red-50 rounded-lg text-red-500 transition-colors cursor-pointer" title="Hapus">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-on-surface-variant">Belum ada data siswa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div x-show="showAddModal" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showAddModal = false" x-cloak>
        <div class="bg-surface-container-lowest rounded-2xl p-6 w-full max-w-lg shadow-xl max-h-[90vh] overflow-y-auto">
            <h3 class="font-h2 text-primary font-bold text-lg mb-4">Tambah Siswa Baru</h3>
            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <!-- Photo Upload -->
                <div>
                    <label class="block text-xs font-bold mb-1">Foto Profil <span class="text-outline font-normal">(opsional)</span></label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                        class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary cursor-pointer"/>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">NIS</label>
                        <input type="text" name="nis" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Kelas</label>
                        <select name="classroom_id" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border border-outline-variant rounded-lg text-xs font-bold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/95 text-white rounded-lg text-xs font-bold cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showEditModal = false" x-cloak>
        <div class="bg-surface-container-lowest rounded-2xl p-6 w-full max-w-lg shadow-xl max-h-[90vh] overflow-y-auto">
            <h3 class="font-h2 text-primary font-bold text-lg mb-4">Edit Siswa</h3>
            <form :action="'/admin/students/' + editStudent.id" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf @method('PUT')
                <!-- Photo Upload -->
                <div>
                    <label class="block text-xs font-bold mb-1">Foto Profil <span class="text-outline font-normal">(opsional, kosongkan jika tidak diubah)</span></label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                        class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary cursor-pointer"/>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">NIS</label>
                        <input type="text" name="nis" x-model="editStudent.nis" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Nama</label>
                        <input type="text" name="name" x-model="editStudent.name" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Gender</label>
                        <select name="gender" x-model="editStudent.gender" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Kelas</label>
                        <select name="classroom_id" x-model="editStudent.classroom_id" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface">
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-outline-variant rounded-lg text-xs font-bold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/95 text-white rounded-lg text-xs font-bold cursor-pointer">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
