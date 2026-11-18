{{-- White card used by every page + flash message / validation errors --}}
<div class="py-8">
    <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="bg-white p-6 text-gray-900 shadow-sm sm:rounded-lg">
            @if(session('status'))
                <div class="mb-4 break-all rounded bg-green-100 p-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <ul class="mb-4 list-inside list-disc rounded bg-red-100 p-3 text-sm text-red-800">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>
