<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </x-slot>

    <x-panel>
        <p class="mb-4 leading-7">
            Logged in as <strong>{{ $user->name }}</strong> ({{ $user->email }})<br>
            Role: <strong>{{ $user->role->value }}</strong><br>
            Company: <strong>{{ $user->company?->name ?? '— (no company)' }}</strong>
        </p>

        @if($user->isSuperAdmin())
            <p class="mb-6 text-sm">
                <a class="text-indigo-600 underline" href="{{ route('invitations.create') }}">Invite an Admin to a new company</a>
                &middot;
                <a class="text-indigo-600 underline" href="{{ route('urls.index') }}">View all short URLs</a>
            </p>

            <h3 class="mb-2 text-lg font-semibold">Companies</h3>
            @if($companies->isEmpty())
                <p class="text-sm text-gray-600">No companies yet.</p>
            @else
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Company</th>
                            <th class="px-3 py-2 text-left">Users</th>
                            <th class="px-3 py-2 text-left">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr class="border-t border-gray-200">
                                <td class="px-3 py-2">{{ $company->name }}</td>
                                <td class="px-3 py-2">{{ $company->users_count }}</td>
                                <td class="px-3 py-2">{{ $company->created_at->toDateString() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @else
            <p class="text-sm">
                <a class="text-indigo-600 underline" href="{{ route('urls.create') }}">Create a short URL</a>
                &middot;
                <a class="text-indigo-600 underline" href="{{ route('urls.index') }}">View short URLs</a>
                @if($user->isAdmin())
                    &middot; <a class="text-indigo-600 underline" href="{{ route('invitations.create') }}">Invite a user to my company</a>
                @endif
            </p>
        @endif
    </x-panel>
</x-app-layout>
