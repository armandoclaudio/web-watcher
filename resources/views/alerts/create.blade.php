<x-layouts.app>
    <div class="mb-6">
        <a href="{{ route('alerts.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back</a>
        <h1 class="text-xl font-semibold mt-2">New Alert</h1>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6 max-w-lg">
        <form method="POST" action="{{ route('alerts.store') }}">
            @csrf

            <div class="mb-4">
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">Feed URL</label>
                <input
                    id="url"
                    name="url"
                    type="url"
                    value="{{ old('url') }}"
                    placeholder="https://example.com/feed.xml"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('url') border-red-400 @enderror"
                />
                @error('url')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="keywords" class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
                <input
                    id="keywords"
                    name="keywords"
                    type="text"
                    value="{{ old('keywords') }}"
                    placeholder="e.g. Laravel"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 @error('keywords') border-red-400 @enderror"
                />
                @error('keywords')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                Create Alert
            </button>
        </form>
    </div>
</x-layouts.app>
