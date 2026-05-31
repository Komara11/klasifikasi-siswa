@props([
    'name' => '',
    'options' => [], // array of ['value' => ..., 'label' => ...]
    'selected' => '',
    'placeholder' => '-- Pilih --',
    'onchange' => '',
    'required' => false,
    'searchable' => false,
])

<div x-data="{
    open: false,
    search: '',
    selected: '{{ $selected }}',
    selectedLabel: '',
    options: {{ json_encode($options) }},
    get filteredOptions() {
        if (!this.search) return this.options;
        return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
    },
    init() {
        const match = this.options.find(o => String(o.value) === String(this.selected));
        this.selectedLabel = match ? match.label : '';
    },
    selectOption(option) {
        this.selected = option.value;
        this.selectedLabel = option.label;
        this.open = false;
        this.search = '';
        this.$refs.hiddenInput.value = option.value;
        this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        {{ $onchange }}
    },
    clear() {
        this.selected = '';
        this.selectedLabel = '';
        this.$refs.hiddenInput.value = '';
        this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
}" @click.outside="open = false" class="relative w-full">
    <!-- Hidden native input for form submission -->
    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" :value="selected" {{ $required ? 'required' : '' }}>
    
    <!-- Trigger Button -->
    <button type="button" @click="open = !open" 
        class="w-full flex items-center justify-between border border-outline-variant bg-surface-container-lowest rounded-xl px-3.5 py-2.5 text-sm text-left focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all hover:border-primary/40 cursor-pointer"
        :class="open ? 'ring-2 ring-primary/20 border-primary' : ''">
        <span :class="selectedLabel ? 'text-on-surface font-medium' : 'text-on-surface-variant/60'" x-text="selectedLabel || '{{ $placeholder }}'"></span>
        <span class="material-symbols-outlined text-[18px] text-on-surface-variant/60 transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute z-50 mt-1.5 w-full bg-surface-container-lowest border border-outline-variant/60 rounded-xl shadow-xl overflow-hidden" x-cloak>
        
        @if($searchable)
        <!-- Search Input -->
        <div class="p-2 border-b border-outline-variant/30">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[16px]">search</span>
                <input type="text" x-model="search" @click.stop placeholder="Cari..." 
                    class="w-full pl-8 pr-3 py-2 text-xs border border-outline-variant/50 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary outline-none bg-surface-container-low" x-ref="searchInput">
            </div>
        </div>
        @endif
        
        <!-- Options List -->
        <ul class="max-h-52 overflow-y-auto py-1 custom-scrollbar">
            <template x-if="filteredOptions.length === 0">
                <li class="px-3.5 py-2.5 text-xs text-on-surface-variant/60 text-center">Tidak ditemukan</li>
            </template>
            <template x-for="option in filteredOptions" :key="option.value">
                <li @click="selectOption(option)" 
                    class="px-3.5 py-2.5 text-sm cursor-pointer transition-all flex items-center justify-between hover:bg-primary/5"
                    :class="String(selected) === String(option.value) ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface'">
                    <span x-text="option.label"></span>
                    <span x-show="String(selected) === String(option.value)" class="material-symbols-outlined text-primary text-[16px]">check</span>
                </li>
            </template>
        </ul>
    </div>
</div>
