<div x-data="formulaBuilder" class="mt-3">
    <h1>Редактор формулы</h1>

    <div class="flex flex-col gap-2 mb-6">
        <div class="btn-group mt-3">
            <p>Операнды</p>
            <div class="flex gap-2 mt-2">
                <template x-for="(inner, value) in operations" :key="value">
                    <button @click.prevent="addInput" :value="value" class="btn btn-secondary" x-text="inner"></button>
                </template>
            </div>
        </div>
        <div class="btn-group mt-3">
            <p>Значения</p>
            <div class="flex gap-2 mt-2">
                <template x-for="(inner, value) in numbers" :key="value">
                    <button @click.prevent="addInput" :value="value" class="btn btn-primary" x-text="inner"></button>
                </template>
            </div>
        </div>
    </div>
    <x-moonshine::box>
        <div class="flex gap-2 expression-inputs">
            <template x-for="(inputValue, inputId) of inputs" x-if="inputs.length" :key="inputId">
                <template x-if="inputValue">
                    <div class="flex relative expression-input"
                         x-data="expressionInput"
                         @click="toggleCloseButton(true)"
                         @click.outside="toggleCloseButton(false)"
                    >
                        <input type="text" class="form-input" :value="inputValue" style="margin-bottom: 0">
                        <div x-show="isCloseButtonHidden" @click="removeElement" class="input-cross">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" aria-hidden="true" class="text-current w-4 h-4  ">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                            </svg>
                        </div>
                    </div>
                </template>
            </template>
            <template x-if="inputs.length === 0">
                <p>Постройте выражение с помощью кнопок выше.</p>
            </template>
        </div>
    </x-moonshine::box>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formulaBuilder', () => ({
            inputs: [],

            numbers: {
                "2": "2",
                "4": "4",
                "6": "6",
                "8": "8",
                "10": "10",
                "12": "12",
            },

            operations: {
                '+': '+',
                '-': '-',
                '*': '*',
                '/': '/',
                '(': '(',
                ')': ')',
            },

            addInput(e) {
                this.inputs.push(e.target.value)
            }
        }))

        Alpine.data('expressionInput', () => ({
            isCloseButtonHidden: false,

            toggleCloseButton(value) {
                this.isCloseButtonHidden = value
            },

            removeElement(e) {
                console.log(this.inputs)
                e.target.closest('.expression-input').remove()
            }
        }))
    })
</script>
