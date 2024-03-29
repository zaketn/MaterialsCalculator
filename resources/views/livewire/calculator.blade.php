{{--<div class="container mx-auto">--}}

    <div class="container">
        <div class="grid grid-cols-3 gap-x-10">
            <div class="col-span-2 p-5 rounded">
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
                    @if(!empty($variations))
                        <button
                            wire:click="clearAll"
                            type="text"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-3 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Очистить
                        </button>
                    @endif
                </div>

                @if(!empty($variations))
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

                @if(!empty($userInputs))
                    <form wire:submit="calculate">
                        @foreach($userInputs as $i => $userInput)
                            <div wire:key="{{ $userInput['slug'] }}_{{ rand(PHP_INT_MIN, PHP_INT_MAX) }}">
                                @if(class_exists($userInput['type']))
                                    <label for="{{ $userInput['slug'] }}"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $userInput['name'] }}
                                    </label>
{{--                                    <input type="hidden" name="userInputs.{{ $i }}.[type]" value="{{ $userInput['type'] }}">--}}
                                    <select
                                        wire:model="userInputs.{{ $i }}.modelId"
                                        id="{{ $userInput['slug'] }}"
                                        name="{{ $userInput['slug'] }}"
                                        class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option selected></option>
                                        @foreach($userInput['type']::all() as $value)
                                            <option wire:key="{{ $value->id }}"
                                                    value="{{ $value->id }}">{{ $value->name }}</option>
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
                            Расчитать
                        </button>
                    </form>
                @endif
            </div>

            @if(!empty($this->error))
                <div class="p-5 rounded">
                    <p class="font-bold">Ошибка!</p>
                    <p class="text-red-500">{{ $this->error }}</p>
                </div>
            @elseif(!empty($this->calculated))
                <div class="p-5 rounded text-black dark:text-white">
                    @foreach($this->calculated as $parameterName => $calculatedParameter)
                        @continue($parameterName === \App\Models\Component::SUMMARY_COMPONENT_NAME)

                        <div wire:key="{{ $parameterName }}_usual">
                            <p class="font-bold">{{ $parameterName }}</p>
                            @foreach($calculatedParameter as $formulaName => $formulaValue)
                                <div wire:key="{{ $formulaName }}">
                                    <p>{{ $formulaName }}
                                        : {{ is_array($formulaValue) ? 'Формула не задана.' : round($formulaValue, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    @if(!empty($this->calculated[\App\Models\Component::SUMMARY_COMPONENT_NAME]))
                        <div class="mt-5">
                            <p class="font-bold">{{ \App\Models\Component::SUMMARY_COMPONENT_NAME }} : {{ number_format($this->calculated[\App\Models\Component::SUMMARY_COMPONENT_NAME], 0, ',', '&nbsp;') }}</p>
                        </div>
                    @endif

                    <div class="flex flex-col gap-2 justify-center mt-6">
                        @if($bitrixDealId || app()->isLocal())
                            <div>
                                <label for="first_name" class="block mb-2 font-medium text-gray-900 dark:text-white">Введите название для каталога</label>
                                <input wire:model="nameForCatalog" type="text" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Труба (базовая)" required />
                            </div>
                            <button wire:click="saveCalculations" type="button"
                                    class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Отправить в Bitrix
                            </button>
                        @endif

                            @if(!empty($bitrixSendStatus))
                                <div>
                                    @foreach($bitrixSendStatus as $status)
                                        <p class="{{ $status['class'] }} font-bold">{{ $status['text'] }}</p>
                                    @endforeach
                                </div>
                            @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
{{--</div>--}}
