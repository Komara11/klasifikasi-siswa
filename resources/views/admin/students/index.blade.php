@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="space-y-space-lg" x-data="{
    showAddModal: false,
    showEditModal: false,
    editStudent: {},
    loading: false,
    init() {
        @if(request('edit'))
            const students = {{ Js::from($students) }};
            const target = students.find(s => s.id == {{ request('edit', 0) }});
            if (target) {
                this.editStudent = target;
                this.showEditModal = true;
            }
        @endif
    }
}" @submit.document="loading = true">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
                <div class="h-4 bg-surface-container-high rounded skeleton w-64"></div>
            </div>
            <div class="h-10 bg-surface-container-high rounded skeleton w-36"></div>
        </div>
        <div class="h-12 bg-surface-container-low rounded-xl skeleton w-full"></div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-4 shadow-sm">
            @for($i = 0; $i < 5; $i++)
            <div class="grid grid-cols-8 gap-4">
                <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                <div class="h-9 w-9 bg-surface-container-low rounded-full skeleton"></div>
                <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                <div class="h-4 bg-surface-container-low rounded skeleton col-span-2"></div>
                <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                <div class="h-4 bg-surface-container-low rounded skeleton"></div>
                <div class="h-4 bg-surface-container-low rounded skeleton"></div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-space-lg">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-h1 text-primary text-xl font-bold">Kelola Data Siswa</h2>
                <p class="text-on-surface-variant font-body-sm mt-0.5">Kelola dan pantau data seluruh siswa terdaftar di {{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}</p>
            </div>
            <button @click="showAddModal = true" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer shadow-sm">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah Siswa
            </button>
        </div>

        <!-- Search Bar -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm">
            <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col sm:flex-row items-stretch">
                <div class="flex-1 flex items-center gap-2 px-4 py-2.5 border-b sm:border-b-0 sm:border-r border-outline-variant/30">
                    <span class="material-symbols-outlined text-outline text-[18px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, atau kelas siswa..."
                        class="flex-1 bg-transparent text-sm focus:outline-none placeholder:text-outline/60"/>
                </div>
                <div class="flex items-center gap-2 px-3 py-2">
                    <div class="w-full sm:w-40">
                        <x-custom-select 
                            name="classroom" 
                            :options="$classrooms->map(fn($c) => ['value' => $c->name, 'label' => $c->name])->toArray()" 
                            :selected="request('classroom', '')" 
                            placeholder="Semua Kelas"
                        />
                    </div>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-xs font-bold cursor-pointer transition-colors shrink-0">Filter</button>
                </div>
            </form>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-2.5">
            @forelse($students as $student)
            <div class="mobile-card-item">
                <div class="flex gap-3 items-center">
                    <!-- Photo -->
                    <div class="shrink-0">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-10 h-10 rounded-full object-cover border border-outline-variant/30">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $student->gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-on-surface text-sm truncate">{{ $student->name }}</p>
                        <p class="text-[11px] text-outline mt-0.5">NIS: {{ $student->nis }} · {{ $student->classroom->name }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0 {{ $student->gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                        {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
                @if($student->address)
                <p class="text-[11px] text-outline truncate mt-1 pl-[52px]">{{ $student->address }}</p>
                @endif
                <div class="flex items-center justify-end mt-2 pt-2 border-t border-outline-variant/15 gap-1">
                    <a href="{{ route('admin.students.show', $student) }}" class="p-1.5 hover:bg-primary/10 rounded-lg text-primary transition-colors" title="Detail">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                    </a>
                    <button @click="editStudent = {{ json_encode($student) }}; showEditModal = true" class="p-1.5 hover:bg-primary/10 rounded-lg text-primary transition-colors cursor-pointer" title="Edit">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}" x-ref="deleteFormMobile{{ $student->id }}" @submit.prevent.stop="triggerConfirm('Hapus Data Siswa', 'Apakah Anda yakin ingin menghapus data siswa {{ $student->name }} secara permanen?', 'Ya, Hapus', 'danger', () => { loading = true; $refs.deleteFormMobile{{ $student->id }}.submit(); })" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 hover:bg-red-50 rounded-lg text-red-500 transition-colors cursor-pointer" title="Hapus">
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
            <table class="w-full text-left border-collapse dense-table" style="min-width: 850px;">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th class="w-14">Profil</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Gender</th>
                        <th>Kelas</th>
                        <th>Alamat</th>
                        <th class="w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($students as $student)
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="text-on-surface-variant">{{ $loop->iteration }}</td>
                        <td>
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-9 h-9 rounded-full object-cover border border-outline-variant/20">
                            @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center {{ $student->gender === 'L' ? 'bg-blue-50 text-blue-500' : 'bg-pink-50 text-pink-500' }}">
                                    <span class="material-symbols-outlined text-[18px]">person</span>
                                </div>
                            @endif
                        </td>
                        <td class="font-bold text-primary">{{ $student->nis }}</td>
                        <td class="font-semibold text-on-surface">{{ $student->name }}</td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $student->gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td>{{ $student->classroom->name }}</td>
                        <td class="text-on-surface-variant text-xs truncate max-w-[160px]">{{ $student->address ?: '-' }}</td>
                        <td>
                            <div class="flex items-center gap-0.5">
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
                        <td colspan="8" class="text-center py-10 text-on-surface-variant">Belum ada data siswa.</td>
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
                    <div>
                        <label class="block text-xs font-bold mb-1">Tanggal Lahir <span class="text-outline font-normal">(opsional)</span></label>
                        <input type="date" name="birth_date" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold mb-1">Alamat <span class="text-outline font-normal">(opsional)</span></label>
                        <textarea name="address" rows="2" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface resize-none" placeholder="Masukkan alamat siswa..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border border-outline-variant rounded-lg text-xs font-bold cursor-pointer hover:bg-surface transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/95 text-white rounded-lg text-xs font-bold cursor-pointer transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showEditModal = false" x-cloak>
        <div class="bg-surface-container-lowest rounded-2xl p-6 w-full max-w-lg shadow-xl max-h-[90vh] overflow-y-auto">
            <h3 class="font-h2 text-primary font-bold text-lg mb-4">Edit Data Siswa</h3>
            <form :action="'/admin/students/' + editStudent.id" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf @method('PUT')
                <!-- Photo Upload -->
                <div>
                    <label class="block text-xs font-bold mb-1">Foto Profil <span class="text-outline font-normal">(kosongkan jika tidak diubah)</span></label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                        class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary cursor-pointer"/>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">NIS</label>
                        <input type="text" name="nis" x-model="editStudent.nis" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editStudent.name" required class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Jenis Kelamin</label>
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
                    <div>
                        <label class="block text-xs font-bold mb-1">Tanggal Lahir <span class="text-outline font-normal">(opsional)</span></label>
                        <input type="date" name="birth_date" x-model="editStudent.birth_date" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold mb-1">Alamat <span class="text-outline font-normal">(opsional)</span></label>
                        <textarea name="address" rows="2" x-model="editStudent.address" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface resize-none" placeholder="Masukkan alamat siswa..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-outline-variant rounded-lg text-xs font-bold cursor-pointer hover:bg-surface transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/95 text-white rounded-lg text-xs font-bold cursor-pointer transition-colors">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
