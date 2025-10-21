<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Manager</title>
    @viteReactRefresh
    @vite('resources/js/main.tsx')
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
