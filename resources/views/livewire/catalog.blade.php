<div class="flex justify-between gap-4 p-4">
    <div class="w-1/2 p-4">
        <div class="flex">
            <h2 class="m- 0 font-bold text-2xl mb-3">Каталог</h2>
        </div>
        <hr class="my-4 border-gray-500">
        <div data-accordion="collapse">
        @foreach($this->products as $product)
                <div class="product-accordion" id="accordion-folder-parent-{{ $product->id }}">
                    <h2 id="accordion-collapse-heading-{{ $product->id }}">
                        <button
                            type="button"
                            class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-gray-200 rounded focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-collapse-body-{{ $product->id }}"
                            aria-expanded="false"
                            aria-controls="accordion-collapse-body-{{ $product->id }}">
                            <span>{{ $product->name }}</span>
                            <svg data-accordion-icon class="w-3 h-3 shrink-0" style="rotate: 180deg;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                            </svg>
                        </button>
                    </h2>
                    <div id="accordion-collapse-body-{{ $product->id }}" class="hidden"
                         aria-labelledby="accordion-collapse-heading-{{ $product->id }}">
                        <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                            @foreach($this->catalogs[$product->id] as $catalog)
                                <div>
                                    <button
                                        x-data="draggableButton"
                                        draggable="true"
                                        x-on:dragstart="dragStart"
                                        x-on:dragend="dragEnd"
                                        type="button"
                                        data-catalog-id="{{ $catalog->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-popover-target="popover-{{ $catalog->id }}"
                                        data-popover-placement="left"
                                        type="button"
                                        class="cursor-move w-full text-white mb-3 me-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        {{ $catalog->name }}
                                    </button>
                                    <div data-popover id="popover-{{ $catalog->id }}" role="tooltip"
                                         class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
                                        <div
                                            class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $catalog->variation->name }}</h3>
                                        </div>
                                        <div class="px-3 py-2">
                                            @foreach($catalog->characteristics as $characteristic)
                                                <p>{{ $characteristic->name }}: {{ $characteristic->pivot->value }}</p>
                                            @endforeach
                                        </div>
                                        <div data-popper-arrow></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
        @endforeach
        </div>
    </div>
    <form class="w-1/2 p-4 text-right" x-data="catalogSection" wire:submit="sendOffer" method="post">
        <div class="flex justify-between">
            <template class="m-0" x-if="showButtons === true">
                <div>
                    <button
                        type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Сформировать КП
                    </button>
                    <button
                        @click="clearAll"
                        type="button"
                        class="py-2.5 px-5 me-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Очистить
                    </button>
                </div>
            </template>

            <h2 @click="isEmpty" class="font-bold mb-3 text-2xl">Составление КП</h2>
        </div>
        <hr class="my-4 border-gray-500">
        <div x-ref="dropSection" class="bg-white h-5/6 border border-dashed border-8 rounded p-4 dark:bg-gray-700"
             x-on:dragover.prevent
             x-on:dragenter.self="dragEnter"
             x-on:dragleave.self="dragLeave"
             x-on:drop.self="drop"
        >
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('draggableButton', () => ({
            accordion: '',
            parent: null,
            index: null,

            dragStart: function (event) {
                event.target.classList.add('selectedItem')
                this.accordion = event.target.closest('.product-accordion')

                event.dataTransfer.setData('text/plain', JSON.stringify({
                    accordionId: this.accordion.id,
                    catalogId: event.target.dataset.catalogId,
                    productName: event.target.dataset.productName
                }))
            },

            dragEnd: function (event) {
                event.target.classList.remove('selectedItem')
            }
        }))

        Alpine.data('catalogSection', () => ({
            showButtons: false,

            drop: function (event) {
                const dto = JSON.parse(event.dataTransfer.getData('text/plain'));
                document.getElementById(dto.accordionId).classList.add('hidden')

                const draggableButton = document.querySelector('.selectedItem')
                const divider = this.createProductDivider(dto.productName, dto.catalogId)

                divider.forEach((element) => {
                    this.$refs.dropSection.append(element);
                })

                this.$wire.selectedCatalogs.push(dto.catalogId)

                this.$refs.dropSection.append(draggableButton.cloneNode(true));
                this.$refs.dropSection.classList.remove('bg-blue-600')

                this.checkChildren()
            },

            dragEnter: function () {
                this.$refs.dropSection.classList.remove('bg-gray-700')
                this.$refs.dropSection.classList.add('bg-blue-600')
            },

            dragLeave: function () {
                this.$refs.dropSection.classList.remove('bg-blue-600')
                this.$refs.dropSection.classList.add('bg-gray-700')

            },

            checkChildren: function () {
                this.showButtons = this.$refs.dropSection.childElementCount > 0
            },

            clearAll: function () {
                const accordions = document.querySelectorAll('.product-accordion')

                for (let accordion of accordions) {
                    accordion.classList.remove('hidden')
                }

                while (this.$refs.dropSection.childElementCount > 0) {
                    this.$refs.dropSection.firstElementChild.remove();
                }

                this.$wire.selectedCatalogs = []

                this.checkChildren()
            },

            createProductDivider: function (productName, catalogId) {
                const product = document.createElement(`p`)
                product.innerHTML = productName
                product.classList.add('text-left')
                product.classList.add('font-bold')

                const hr = document.createElement(`hr`)
                hr.classList.add('my-2')
                hr.classList.add('border-gray-500')

                return [product, hr]
            }
        }))
    })
</script>
