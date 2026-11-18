<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Create a short URL</h2>
    </x-slot>

    <x-panel>
        <form method="POST" action="{{ route('urls.store') }}" class="max-w-xl space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Original URL</label>
                <input type="url" name="original_url" value="{{ old('original_url') }}" required
                       placeholder="https://example.com/very/long/link"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                Generate
            </button>
        </form>
    </x-panel>
</x-app-layout>
