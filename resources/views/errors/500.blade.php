@extends('errors.layout')

@section('title', 'Something went wrong')

@section('content')
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 text-red-600 mb-5 mx-auto" aria-hidden="true">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
    <p class="text-4xl font-bold tracking-tight text-brand-sky mb-1">500</p>
    <h1 class="text-xl font-semibold text-brand-navy mb-2">Something went wrong</h1>
    <p class="text-brand-muted text-sm leading-relaxed mb-8 max-w-sm mx-auto">
        We hit an unexpected error on our side. Please try again in a moment. If the problem continues, contact your administrator.
    </p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="javascript:location.reload()" class="inline-flex justify-center items-center rounded-xl bg-brand-sky px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-sky focus:ring-offset-2 transition-colors">
            Try again
        </a>
        <a href="{{ url('/') }}" class="inline-flex justify-center items-center rounded-xl border border-brand-border bg-white px-5 py-2.5 text-sm font-semibold text-brand-navy hover:bg-brand-bg focus:outline-none focus:ring-2 focus:ring-brand-sky/30 transition-colors">
            Home
        </a>
    </div>
@endsection
