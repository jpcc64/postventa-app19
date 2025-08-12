<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        .input-pestana {
            @apply w-full rounded-md border border-gray-400 bg-gray-50 shadow-sm p-2 transition focus:border-blue-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-white to-slate-200 text-slate-800 flex flex-col min-h-screen antialiased">
    <div class="container mx-auto mt-10">
