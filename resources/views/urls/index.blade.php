<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            @if($user->isSuperAdmin())
                All short URLs (every company)
            @elseif($user->isAdmin())
                All short URLs in your company
            @else
                Your short URLs
            @endif
        </h2>
    </x-slot>

    <x-panel>
        @if($user->canCreateShortUrls())
            <p class="mb-4 text-sm">
                <a class="text-indigo-600 underline" href="{{ route('urls.create') }}">+ Create short URL</a>
            </p>
        @endif

        @if($urls->isEmpty())
            <p class="text-sm text-gray-600">No short URLs yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Short URL</th>
                            <th class="px-3 py-2 text-left">Original URL</th>
                            @if($user->isSuperAdmin())<th class="px-3 py-2 text-left">Company</th>@endif
                            <th class="px-3 py-2 text-left">Created by</th>
                            <th class="px-3 py-2 text-left">Hits</th>
                            <th class="px-3 py-2 text-left">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($urls as $url)
                            <tr class="border-t border-gray-200">
                                <td class="px-3 py-2">
                                    <a class="text-indigo-600 underline" href="{{ $url->short_link }}" target="_blank">{{ $url->short_link }}</a>
                                </td>
                                <td class="break-all px-3 py-2">{{ $url->original_url }}</td>
                                @if($user->isSuperAdmin())<td class="px-3 py-2">{{ $url->company->name }}</td>@endif
                                <td class="px-3 py-2">{{ $url->user->name }}</td>
                                <td class="px-3 py-2">{{ $url->hits }}</td>
                                <td class="px-3 py-2">{{ $url->created_at->toDateTimeString() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $urls->links() }}</div>
        @endif
    </x-panel>
</x-app-layout>
