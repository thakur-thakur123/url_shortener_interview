<x-guest-layout>
    <h2 class="mb-2 text-lg font-semibold text-gray-800">Join {{ $invitation->company->name }}</h2>
    <p class="mb-4 text-sm text-gray-600">
        You were invited as <strong>{{ $invitation->role->value }}</strong> ({{ $invitation->email }}).
    </p>

    @if($errors->any())
        <ul class="mb-4 list-inside list-disc rounded bg-red-100 p-3 text-sm text-red-800">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Your name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Password (min 8 characters)</label>
            <input type="password" name="password" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirm password</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
            Create account
        </button>
    </form>
</x-guest-layout>
