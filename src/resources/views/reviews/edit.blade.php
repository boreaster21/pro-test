<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $review->store->name }} - 口コミ編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             <form action="{{ route('reviews.update', $review) }}" method="POST" enctype="multipart/form-data">
                 @method('PUT')
                 @include('reviews._form', ['store' => $review->store, 'review' => $review])
            </form>
        </div>
    </div>
</x-app-layout> 
