<div x-data="{{ str_replace('-', '_', $parameter->slug) . '_formulaBuilder' }}" class="mt-3"
     xmlns:x-moonshine="http://www.w3.org/1999/html">
    <div class="flex flex-col gap-2 mb-6">
        <div class="btn-group mt-3">
            <p>Вводные параметры</p>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($characteristics as $characteristic)
                    <button @click.prevent="addInput" value="{{ '[' . $characteristic['slug'] . ']' }}"
                            class="btn btn-success">{{ $characteristic['name'] }}</button>
                @endforeach
            </div>
        </div>
        <div class="btn-group mt-3">
            <p>Расчётные параметры</p>
            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($parameters as $anotherParameter)
                    @continue($anotherParameter === $parameter)
                    <button @click.prevent="addInput" value="{{ '{' . $anotherParameter['slug'] . '}' }}"
                            class="btn btn-warning">{{ $anotherParameter['name'] }}</button>
                @endforeach
            </div>
        </div>
        <template x-for="buttonGroup of predefinedButtons" :key="buttonGroup.title">
            <div class="btn-group mt-3">
                <p x-text="buttonGroup.title"></p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <template x-for="button in buttonGroup.items" :key="button.value">
                        <button @click.prevent="addInput" :value="button.value" :class="buttonGroup.class"
                                x-text="button.inner"></button>
                    </template>
                </div>
            </div>
        </template>
        <div class="btn-group mt-3">
            <p>Добавьте своё значение</p>
            <div class="flex gap-2 mt-2">
                <x-moonshine::form.input
                    x-ref="customInput"
                />
                <button @click.prevent="addCustomInput" type="text" class="btn btn form_submit_button">Добавить</button>
            </div>
        </div>
    </div>
    <x-moonshine::box>
        <div class="flex flex-wrap gap-2 expression-inputs">
            <template x-for="(inputValue, index) of inputs" x-if="inputs.length > 0" :key="index">
                <template x-if="inputValue">
                    <div class="flex expression-input">
                        <button @click.prevent="" type="text" class="btn btn-primary" :value="inputValue.slug"
                                x-text="inputValue.inner" style="margin-bottom: 0"></button>
                    </div>
                </template>
            </template>
            <template x-if="inputs.length === 0">
                <p>Постройте выражение с помощью кнопок выше.</p>
            </template>
        </div>

        <x-moonshine::form.input
            class="hidden"
            name="{{ $parameter->slug. '[formula]' }}"
            x-model="JSON.stringify(inputs)"
            @change="console.log(inputs)"
        />

        <div class="flex mt-3" x-show="inputs.length > 0">
            <button @click.prevent="clearInputs" type="text" class="btn form_submit_button">Очистить</button>
        </div>
    </x-moonshine::box>

    <x-moonshine::box class="mt-3">
        <div class="flex flex-wrap gap-2 expression-inputs">
            {!! actionBtn('Удалить параметр', route('parameter.delete', ['id' => $parameter->id]))
                    ->withConfirm('Удалить параметр', 'Вы действительно хотите удалить параметр?', 'Да', method:'DELETE')
                    ->error()
            !!}
        </div>
    </x-moonshine::box>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data(`{{ str_replace('-', '_', $parameter->slug) . '_formulaBuilder' }}`, () => ({
            inputs: {!! $parameter->formula ?? '[]' !!},

            predefinedButtons: [
                {
                    title: 'Числа',
                    class: 'btn btn-primary',
                    items: [
                        {
                            inner: "3.14",
                            value: "3.14",
                        },
                        {
                            inner: "10",
                            value: "10",
                        },
                        {
                            inner: "10 000",
                            value: "10000",
                        },
                        {
                            inner: "100 000",
                            value: "100000",
                        }
                    ]
                },
                {
                    title: 'Действия',
                    class: 'btn btn-secondary',
                    items: [
                        {
                            inner: '+',
                            value: '+'
                        },
                        {
                            inner: '-',
                            value: '-'
                        },
                        {
                            inner: '*',
                            value: '*'
                        },
                        {
                            inner: '/',
                            value: '/'
                        },
                        {
                            inner: '(',
                            value: '('
                        },
                        {
                            inner: ')',
                            value: ')'
                        },
                    ]
                }
            ],

            addInput(e) {
                const slug = e.target.value
                const inner = e.target.innerHTML ?? e.target.value

                this.inputs.push({
                    slug: slug,
                    inner: inner
                })
            },

            addCustomInput(e) {
                const value = this.$refs.customInput.value.replace(',', '.')

                if (typeof value != "string") return
                if (isNaN(value) && isNaN(parseFloat(value))) return

                this.inputs.push({
                    slug: value,
                    inner: value
                })
            },

            clearInputs() {
                this.inputs = []
            }
        }))
    })
</script>
