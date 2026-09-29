{{-- Styles en ligne : l'admin n'a pas de thème Tailwind compilé pour les vues du projet. --}}
@php
    $imageUrl = $getImageUrl();
    $max = $getMaxPoints();
    $maxLabel = $getMaxLabel();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle(@js($getStatePath())),
            max: @js($max),
            dragging: null,
            init() { if (! Array.isArray(this.state)) this.state = [] },
            position(event) {
                const rect = this.$refs.photo.getBoundingClientRect()
                const clamp = (v) => Math.round(Math.min(100, Math.max(0, v)) * 10) / 10
                return { x: clamp((event.clientX - rect.left) / rect.width * 100), y: clamp((event.clientY - rect.top) / rect.height * 100) }
            },
            add(event) {
                if (this.dragging !== null || this.state.length >= this.max) return
                this.state = [...this.state, { ...this.position(event), label: '' }]
                this.$nextTick(() => [...this.$root.querySelectorAll('input[data-hotspot-label]')].at(-1)?.focus())
            },
            move(event) {
                if (this.dragging === null) return
                const points = [...this.state]
                points[this.dragging] = { ...points[this.dragging], ...this.position(event) }
                this.state = points
            },
            remove(index) { this.state = this.state.filter((_, i) => i !== index) },
            rename(index, label) {
                const points = [...this.state]
                points[index] = { ...points[index], label }
                this.state = points
            },
        }"
        x-on:pointermove.window="move($event)"
        x-on:pointerup.window="setTimeout(() => dragging = null)"
    >
        @if (! $imageUrl)
            <p style="padding:.75rem 1rem;border:1px dashed rgb(156 163 175);border-radius:.5rem;font-size:.875rem;color:rgb(107 114 128)">
                Choisissez d'abord la photo du studio, puis enregistrez : vous pourrez ensuite placer les points dessus.
            </p>
        @else
            <p style="font-size:.8125rem;color:rgb(107 114 128);margin-bottom:.5rem">
                Cliquez sur la photo pour ajouter un point, puis écrivez ce qu'il montre (ex. « Console 48 pistes »). Glissez un point pour le déplacer.
                <span x-show="state.length >= max">{{ $max }} points au maximum.</span>
            </p>
            <div x-ref="photo" x-on:click="add($event)"
                style="position:relative;border-radius:.5rem;overflow:hidden;user-select:none;touch-action:none"
                x-bind:style="{ cursor: state.length >= max ? 'default' : 'crosshair' }">
                <img src="{{ $imageUrl }}" alt="" style="display:block;width:100%;pointer-events:none" draggable="false">
                <template x-for="(point, index) in state" :key="index">
                    <button type="button"
                        x-on:click.stop
                        x-on:pointerdown.stop.prevent="dragging = index"
                        x-bind:style="`position:absolute;left:${point.x}%;top:${point.y}%;transform:translate(-50%,-50%);width:1.75rem;height:1.75rem;border-radius:9999px;background:#ff7a1a;color:#000;font-weight:700;font-size:.8rem;border:2px solid #fff;box-shadow:0 0 0 4px rgba(255,122,26,.35);cursor:grab`"
                        x-bind:aria-label="`Point ${index + 1} : ${point.label || 'sans libellé'} (glisser pour déplacer)`"
                        x-text="index + 1"></button>
                </template>
            </div>

            <ol style="margin-top:.75rem;display:grid;gap:.5rem">
                <template x-for="(point, index) in state" :key="index">
                    <li style="display:flex;align-items:center;gap:.5rem">
                        <span style="flex:none;width:1.5rem;height:1.5rem;border-radius:9999px;background:#ff7a1a;color:#000;font-weight:700;font-size:.75rem;display:grid;place-items:center" x-text="index + 1"></span>
                        <input type="text" maxlength="{{ $maxLabel }}" placeholder="Ce que montre ce point"
                            data-hotspot-label
                            x-bind:value="point.label"
                            x-on:input.debounce.300ms="rename(index, $event.target.value)"
                            style="flex:1;border:1px solid rgb(209 213 219);border-radius:.5rem;padding:.4rem .6rem;font-size:.875rem;background:transparent;color:inherit">
                        <button type="button" x-on:click="remove(index)"
                            style="font-size:.8125rem;color:rgb(220 38 38);padding:.25rem .5rem">Supprimer</button>
                    </li>
                </template>
            </ol>
        @endif
    </div>
</x-dynamic-component>
