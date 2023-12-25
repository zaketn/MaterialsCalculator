<div class="container mx-auto">
    <h1 class="text-3xl mb-4 font-semibold">Калькулятор материалов</h1>

    <div class="container">
        <label for="countries"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Выберите товар
        </label>
        <select id="products"
                wire:model.live="selectedProductId"
                class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected></option>
            @foreach($products as $product)
                <option wire:key="{{ $product->id }}" value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>

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
                    <option wire:key="{{ $variation->id }}" value="{{ $variation->id }}">{{ $variation->name }}</option>
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

        @if($calculated)
            @foreach($calculated as $parameterName => $calculatedResult)
                <div wire:key="{{ $parameterName }}">
                    <p>{{ $parameterName }}: {{ $calculatedResult }}</p>
                </div>
            @endforeach
        @endif
    </div>
</div>
