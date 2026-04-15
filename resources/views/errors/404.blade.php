<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>404 - Page Not Found</title>
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col items-center">
<!-- Subtle Background Element for Depth -->
<div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-[10%] -left-[5%] w-[40%] h-[40%] rounded-full bg-primary-container/5 blur-[120px]"></div>
    <div class="absolute bottom-[0%] right-[0%] w-[30%] h-[30%] rounded-full bg-tertiary-container/5 blur-[100px]"></div>
</div>
<!-- Main Content Canvas -->
<main class="relative z-10 w-full max-w-4xl px-8 flex flex-col text-center" style="margin-top: 10vh;">
    <!-- Large Editorial Typography -->
    <div class="relative mb-8" style="min-height: 260px;">
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="material-symbols-outlined text-primary mb-4" style="font-size: 56px; font-variation-settings: 'FILL' 1;">
                explore_off
            </span>
            <p class="text-on-surface font-bold tracking-tight" style="font-size: 2.5rem;">
                Oops! Page Not Found.
            </p>
        </div>
    </div>
    <!-- Descriptive Body -->
    <div class="mx-auto mb-12" style="max-width: 28rem;">
        <p class="text-on-surface-variant leading-relaxed" style="font-size: 1.125rem;">
            The page you are looking for has been moved, deleted, or never existed in the first place. Let's get you back to the dashboard.
        </p>
    </div>
    <!-- Primary CTA -->
    <div class="group relative">
        <!-- Tonal Shadow for Elevation -->
        <div class="justify-center relative inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: rgba(138, 81, 0, 0.2); filter: blur(24px);"></div>
        <a href="{{ route('dashboard') }}" class="justify-center relative items-center gap-3 bg-primary-container text-on-primary-container font-semibold rounded-lg shadow-sm transition-all duration-200 no-underline" style="padding: 1.25rem 2.5rem;">
            <span class="tracking-wide">Back to Dashboard</span>
        </a>
    </div>
</main>
</body>
</html>