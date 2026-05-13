<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Alerts</h1>
        <span class="text-sm text-gray-500">{{ $alerts->count() }} {{ Str::plural('alert', $alerts->count()) }}</span>
    </div>

    @if ($alerts->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="mb-4">No alerts yet.</p>
            <a href="{{ route('alerts.create') }}" class="text-sm text-gray-900 underline">Create your first alert</a>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100">
            @foreach ($alerts as $alert)
                <div class="flex items-center gap-4 px-4 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $alert->url }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $alert->keywords }}</p>
                    </div>
                    <div class="shrink-0 text-xs text-gray-400 w-20 text-right">
                        {{ $alert->web_notifications_count }} {{ Str::plural('match', $alert->web_notifications_count) }}
                    </div>
                    <div class="shrink-0 flex items-center gap-3">
                        <a href="{{ route('alerts.edit', $alert) }}" class="text-sm text-gray-600 hover:text-gray-900">Edit</a>
                        <form method="POST" action="{{ route('alerts.destroy', $alert) }}" onsubmit="return confirm('Delete this alert?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
