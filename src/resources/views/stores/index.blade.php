<x-app-layout>
    <x-slot name="header">
        <h2 class="p-store-index__title">
            {{ __('店舗一覧') }}
        </h2>
    </x-slot>

    <div class="p-store-index__content-wrapper">
        <div class="p-store-index__card-container">
            <div class="p-store-index__content">

                <div class="p-store-index__grid">
                    @foreach ($stores as $store)
                        <x-store-card :store="$store" />
                    @endforeach
                </div>

                <div class="p-store-index__pagination c-pagination">
                    {{ $stores->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>

</x-app-layout> 