<div class="container mx-auto">
    <h1 class="text-3xl mb-4 font-semibold">Калькулятор материалов</h1>

    <div class="container">
        <div class="grid grid-cols-3 gap-x-10">
            <div class="col-span-2 bg-gray-100 p-5 rounded">
                <label for="countries"
                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Выберите товар
                </label>
                <div class="flex gap-2">
                    <select id="products"
                            wire:model.live="selectedProductId"
                            class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected></option>
                        @foreach($products as $product)
                            <option wire:key="{{ $product->id }}"
                                    value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @if($variations)
                        <button
                            wire:click="clearAll"
                            type="text"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-3 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Очистить
                        </button>
                    @endif
                </div>

                @if($variations)
                    <label for="countries"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Выберите вариацию
                    </label>
                    <select id="products"
                            wire:model.live="selectedVariationId"
                            class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected></option>
                        @foreach($variations as $variation)
                            <option wire:key="{{ $variation->id }}"
                                    value="{{ $variation->id }}">{{ $variation->name }}</option>
                        @endforeach
                    </select>
                @endif

                @if($userInputs)
                    <form wire:submit="calculate">
                        @foreach($userInputs as $i => $userInput)
                            <div wire:key="{{ $userInput['slug'] }}">
                                @if(class_exists($userInput['type']))
                                    <label for="{{ $userInput['slug'] }}"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $userInput['name'] }}
                                    </label>
                                    <select
                                        wire:model="userInputs.{{ $i }}.value"
                                        id="{{ $userInput['slug'] }}"
                                        name="{{ $userInput['slug'] }}"
                                        class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option selected></option>
                                        @foreach($userInput['type']::all() as $value)
                                            <option wire:key="{{ $value->id }}"
                                                    value="{{ $value->price }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div>
                                        <label for="{{ $userInput['slug'] }}"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $userInput['name'] }}</label>
                                        <input
                                            wire:model="userInputs.{{ $i }}.value"
                                            type="{{ $userInput['type'] }}"
                                            id="{{ $userInput['slug'] }}"
                                            @if($userInput['type'] === 'number')
                                                step="0.1"
                                            @endif
                                            name="{{ $userInput['slug'] }}"
                                            class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Сохранить
                        </button>
                    </form>
                @endif
            </div>

            @if($calculated)
                <div class="bg-gray-100 p-5 rounded">
                    @foreach($calculated as $parameterName => $calculatedParameter)
                        @continue($parameterName === \App\Models\Component::SUMMARY_COMPONENT_NAME)

                        <div wire:key="{{ $parameterName }}_usual">
                            <p class="font-bold">{{ $parameterName }}</p>
                            @foreach($calculatedParameter as $formulaName => $formulaValue)
                                <div wire:key="{{ $formulaName }}">
                                    <p>{{ $formulaName }}
                                        : {{ is_array($formulaValue) ? 'Формула не задана.' : $formulaValue }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    @isset($calculated[\App\Models\Component::SUMMARY_COMPONENT_NAME])
                        <div class="mt-5">
                            <p class="font-bold">{{ \App\Models\Component::SUMMARY_COMPONENT_NAME }} : {{ number_format($calculated[\App\Models\Component::SUMMARY_COMPONENT_NAME], 0, ',', '&nbsp;') }}</p>
                        </div>
                    @endisset

                    <div class="flex gap-2 items-center">
                        @if($bitrixDealId)
                            <button wire:click="sendToBitrix" type="text"
                                    class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Отправить в Bitrix
                            </button>
                        @endif

                        @if(!empty($bitrixSendStatus))
                            <p class="{{ $bitrixSendStatus['class'] }}">{{ $bitrixSendStatus['text'] }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
