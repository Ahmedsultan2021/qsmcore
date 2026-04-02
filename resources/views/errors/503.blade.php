@extends('errors.layout')

@section('title', 'Maintenance')

@section('content')
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 mb-5 mx-auto" aria-hidden="true">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>
    <p class="text-4xl font-bold tracking-tight text-brand-sky mb-1">503</p>
    <h1 class="text-xl font-semibold text-brand-navy mb-2">Maintenance in progress</h1>
    <p class="text-brand-muted text-sm leading-relaxed mb-2 max-w-sm mx-auto">
        {{ config('app.name', 'QSMCore') }} is temporarily unavailable while we apply updates or improvements.
    </p>
    @php
        $retryAfter = null;
        if (isset($exception) && $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
            $retryAfter = $exception->getHeaders()['Retry-After'] ?? null;
        }
    @endphp
    @if($retryAfter)
        <p class="text-xs text-brand-muted mb-8">
            You can try again after approximately {{ $retryAfter }} seconds.
        </p>
    @else
        <p class="text-xs text-brand-muted mb-8">
            Please refresh this page in a few minutes.
        </p>
    @endif
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="javascript:location.reload()" class="inline-flex justify-center items-center rounded-xl bg-brand-sky px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-sky focus:ring-offset-2 transition-colors">
            Refresh
        </a>
        <a href="{{ url('/') }}" class="inline-flex justify-center items-center rounded-xl border border-brand-border bg-white px-5 py-2.5 text-sm font-semibold text-brand-navy hover:bg-brand-bg focus:outline-none focus:ring-2 focus:ring-brand-sky/30 transition-colors">
            Home
        </a>
    </div>
@endsection
