<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $store->name }} - 口コミ投稿
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('reviews.store', $store) }}" method="POST" enctype="multipart/form-data">
                 @include('reviews._form', ['store' => $store])
            </form>
        </div>
    </div>
</x-app-layout> 
