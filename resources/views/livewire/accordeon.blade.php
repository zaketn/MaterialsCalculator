<div class="flex justify-between">
    <div class="w-2/6">
        @foreach($this->folders as $characteristicName => $folder)
            <div id="accordion-folder-parent-{{ str()->slug($characteristicName) }}" data-accordion="collapse">
                <h2 id="accordion-collapse-heading-{{ str()->slug($characteristicName) }}">
                    <button type="button"
                            class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-collapse-body-{{ str()->slug($characteristicName) }}"
                            aria-expanded="false"
                            aria-controls="accordion-collapse-body-{{ str()->slug($characteristicName) }}">
                        <span>{{ $folder[1][0]['name'] }}</span>
                        <svg data-accordion-icon class="w-3 h-3  shrink-0" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5 5 1 1 5"/>
                        </svg>
                    </button>
                </h2>
                <div id="accordion-collapse-body-{{ str()->slug($characteristicName) }}" class="hidden"
                     aria-labelledby="accordion-collapse-heading-{{ str()->slug($characteristicName) }}">
                    <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        @if(count($folder) > 1)
                            <livewire:nested-accordeon :accordeons="array_slice($folder, 1)"/>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
