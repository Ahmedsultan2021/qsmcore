@extends('errors.layout')

@section('title', 'Page not found')

@section('content')
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-sky/10 text-brand-sky mb-5 mx-auto" aria-hidden="true">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <p class="text-4xl font-bold tracking-tight text-brand-sky mb-1">404</p>
    <h1 class="text-xl font-semibold text-brand-navy mb-2">Page not found</h1>
    <p class="text-brand-muted text-sm leading-relaxed mb-8 max-w-sm mx-auto">
        This URL is not part of {{ config('app.name', 'QSMCore') }}. Check the link or use the options below to continue.
    </p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ url('/') }}" class="inline-flex justify-center items-center rounded-xl bg-brand-sky px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-sky focus:ring-offset-2 transition-colors">
            Home
        </a>
        <a href="{{ url('/companies/login') }}" class="inline-flex justify-center items-center rounded-xl border border-brand-border bg-white px-5 py-2.5 text-sm font-semibold text-brand-navy hover:bg-brand-bg focus:outline-none focus:ring-2 focus:ring-brand-sky/30 transition-colors">
            Employee login
        </a>
    </div>
@endsection
