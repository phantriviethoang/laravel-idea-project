<!doctype html>
<html lang="en" data-theme="sunset">

<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    />
    <title>Idea</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background text-foreground">
    <x-layout.nav />

    <main class="mx-auto max-w-7xl px-6 pb-10">
        {{ $slot }}
    </main>

    @session('success')
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition.opacity.duration.300ms
            class="bg-primary absolute bottom-4 right-4 rounded-lg px-4 py-3"
        >
            {{ $value }}
        </div>
    @endsession
</body>

</html>
