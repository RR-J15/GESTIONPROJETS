<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Notifications</h2>
           <form method="POST" action="{{ route('notifications.destroyAll') }}"
                onsubmit="return confirm('Supprimer toutes les notifications ?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                class="flex items-center space-x-2 px-4 py-2 bg-red-50 hover:bg-red-100
                       text-red-600 text-sm font-medium rounded-xl transition-all border    border-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5  4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Tout supprimer</span>
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg divide-y divide-gray-100">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $typeIcons = [
                        'task_assigned'        => '📋',
                        'deadline_soon'        => '⏰',
                        'quote_status_changed' => '📄',
                    ];
                    $icon = $typeIcons[$notification->data['type']] ?? '🔔';
                @endphp
                <div class="flex items-start justify-between p-4 {{ $isUnread ? 'bg-indigo-50' : '' }}">
                    <div class="flex items-start space-x-3">
                        <span class="text-xl">{{ $icon }}</span>
                        <div>
                            <p class="text-sm {{ $isUnread ? 'font-semibold text-gray-900' : 'text-gray-700' }}">
                                {{ $notification->data['message'] }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}"
                                   class="text-xs text-indigo-600 hover:underline mt-1 inline-block">
                                    Voir →
                                </a>
                            @endif
                        </div>
                    </div>
                    @if($isUnread)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="text-xs text-gray-400 hover:text-gray-600 flex-shrink-0">
                                ✓ Lu
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="p-10 text-center text-gray-400">
                    Aucune notification.
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-4">{{ $notifications->links() }}</div>
        @endif
    </div>
</x-app-layout>