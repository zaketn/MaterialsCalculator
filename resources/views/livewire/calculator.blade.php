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

        @if($components)
            {{-- Inputs --}}
        @endif
    </div>
</div>
