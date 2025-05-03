@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'c-input-error']) }}>
        @foreach ((array) $messages as $message)
            <p>{{ $message }}</p>
        @endforeach
    </div>
@endif
