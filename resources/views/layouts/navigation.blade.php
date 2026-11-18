{{-- Replaces Breeze's default navigation (plain HTML + Tailwind, no Breeze components needed) --}}
@php $navUser = auth()->user(); @endphp
<nav class="border-b border-gray-100 bg-white">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-5 text-sm font-medium text-gray-600">
            <a href="{{ route('dashboard') }}" class="font-semibold text-gray-900">URL Shortener</a>
            <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a>

            @if($navUser->isSuperAdmin())
                <a href="{{ route('urls.index') }}" class="hover:text-gray-900">All Short URLs</a>
                <a href="{{ route('invitations.create') }}" class="hover:text-gray-900">Invite Admin</a>
            @else
                <a href="{{ route('urls.index') }}" class="hover:text-gray-900">Short URLs</a>
                @if($navUser->canCreateShortUrls())
                    <a href="{{ route('urls.create') }}" class="hover:text-gray-900">Create Short URL</a>
                @endif
                @if($navUser->isAdmin())
                    <a href="{{ route('invitations.create') }}" class="hover:text-gray-900">Invite User</a>
                @endif
            @endif
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                Logout ({{ $navUser->name }})
            </button>
        </form>
    </div>
</nav>
