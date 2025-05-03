<button {{ $attributes->merge(['type' => 'submit', 'class' => 'c-button c-button--primary']) }}>
    {{ $slot }}
</button>
