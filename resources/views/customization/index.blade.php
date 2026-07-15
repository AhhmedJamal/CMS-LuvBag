@extends('layouts.admin')

@section('title' , __('dashboard.customization') )

@section('content')
    <div class="container">
        <div class="card">
            <div class="flex items-center gap-3 mb-4">
                <i class="fa-solid fa-layer-group p-2 w-12! h-full flex items-center justify-center bg-primary/20 rounded-full text-primary text-xl"></i>
                <div>
                    <h1 class="text-2xl font-bold text-neutral-900">{{ __('dashboard.welcome_customization') }}</h1>
                </div>
            </div>
        </div>
    </div>
@endsection
