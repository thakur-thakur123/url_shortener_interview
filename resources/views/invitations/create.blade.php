<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $user->isSuperAdmin() ? 'Invite an Admin (new company)' : 'Invite a user to '.$user->company->name }}
        </h2>
    </x-slot>

    <x-panel>
        <form method="POST" action="{{ route('invitations.store') }}" class="max-w-md space-y-4">
            @csrf

            @if($user->isSuperAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700">New company name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            @if($user->isAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="Member">Member</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
            @endif

            <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                Send Invitation
            </button>
        </form>

        <h3 class="mb-2 mt-8 text-lg font-semibold">Invitations you sent</h3>
        @if($invitations->isEmpty())
            <p class="text-sm text-gray-600">None yet.</p>
        @else
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Email</th>
                        <th class="px-3 py-2 text-left">Role</th>
                        <th class="px-3 py-2 text-left">Company</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Link</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invitations as $invitation)
                        <tr class="border-t border-gray-200">
                            <td class="px-3 py-2">{{ $invitation->email }}</td>
                            <td class="px-3 py-2">{{ $invitation->role->value }}</td>
                            <td class="px-3 py-2">{{ $invitation->company->name }}</td>
                            <td class="px-3 py-2">{{ $invitation->accepted_at ? 'Accepted' : 'Pending' }}</td>
                            <td class="break-all px-3 py-2">
                                @unless($invitation->accepted_at)
                                    <a class="text-indigo-600 underline" href="{{ $invitation->link() }}">{{ $invitation->link() }}</a>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-panel>
</x-app-layout>
