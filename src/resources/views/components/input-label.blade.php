@props(['value'])

<label {{ $attributes->merge(['class' => 'c-label']) }}>
    {{ $value ?? $slot }}
</label>
