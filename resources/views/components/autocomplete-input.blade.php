@props([
    'name' => 'q',
    'value' => '',
    'placeholder' => 'Search...',
    'endpoint' => '',
    'label' => 'Search',
])

<div
    x-data="{
        query: '{{ $value }}',
        results: [],
        show: false,
        selected: -1,
        search(q) {
            if (q.length < 1) {
                this.results = [];
                this.show = false;
                return;
            }
            fetch('{{ $endpoint }}?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    this.results = data;
                    this.show = data.length > 0;
                    this.selected = -1;
                })
                .catch(() => {
                    this.results = [];
                    this.show = false;
                });
        },
        choose(item) {
            this.query = item.value;
            this.show = false;
            this.results = [];
        },
        navigate(e) {
            if (!this.show || this.results.length === 0) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selected = Math.min(this.selected + 1, this.results.length - 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selected = Math.max(this.selected - 1, 0);
            } else if (e.key === 'Enter' && this.selected >= 0) {
                e.preventDefault();
                this.choose(this.results[this.selected]);
            } else if (e.key === 'Escape') {
                this.show = false;
            }
        }
    }"
    @click.outside="show = false"
    class="relative"
>
    <span class="text-xs font-medium text-slate-600">{{ $label }}</span>
    <input
        type="text"
        name="{{ $name }}"
        x-model="query"
        x-on:input="search($event.target.value)"
        x-on:keydown="navigate($event)"
        x-on:focus="search($event.target.value)"
        class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700"
        placeholder="{{ $placeholder }}"
    >
    <div
        x-cloak
        x-show="show"
        x-transition.opacity.out
        class="absolute z-30 mt-1 w-full overflow-hidden rounded-md border border-slate-200 bg-white shadow-lg"
    >
        <template x-for="(item, i) in results" :key="i">
            <button
                type="button"
                x-on:click="choose(item)"
                x-on:mouseenter="selected = i"
                :class="selected === i ? 'bg-slate-100' : 'bg-white'"
                class="block w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50"
                x-text="item.label"
            ></button>
        </template>
    </div>
</div>