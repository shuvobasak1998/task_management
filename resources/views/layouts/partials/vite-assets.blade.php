@if (! app()->runningUnitTests())
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
