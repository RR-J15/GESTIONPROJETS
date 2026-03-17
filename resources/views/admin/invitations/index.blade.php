<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Invitations</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
        @endif

        {{-- Formulaire création --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Nouvelle invitation</h3>
            <form action="{{ route('admin.invitations.store') }}" method="POST"
                  class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="email@exemple.com"
                           class="w-full border-gray-300 rounded-lg shadow-sm text-sm
                                  focus:ring-indigo-500 focus:border-indigo-500
                                  @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                    <select name="role" required>
                        <option value="">-- Choisir un rôle --</option>
                        <option value="admin">Administrateur</option>
                        <option value="membre">Membre</option>
                    </select>
                </div>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    Inviter
                </button>
            </form>
        </div>

        {{-- Liste invitations --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expire le</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lien + QR</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invitations as $invitation)
                        @php
                            $statusColor = $invitation->isUsed()
                                ? 'bg-green-100 text-green-700'
                                : ($invitation->isExpired()
                                    ? 'bg-red-100 text-red-700'
                                    : 'bg-blue-100 text-blue-700');
                            $statusLabel = $invitation->isUsed()
                                ? 'Utilisée'
                                : ($invitation->isExpired() ? 'Expirée' : 'Active');
                            $inviteUrl = route('invite.show', $invitation->token);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $invitation->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $invitation->role === 'chef_projet' ? 'Chef de projet' : 'Membre' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $invitation->expires_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($invitation->isValid())
                                    {{-- Lien copiable --}}
                                    <div class="flex items-center space-x-3">
                                        <input type="text" readonly value="{{ $inviteUrl }}"
                                               onclick="this.select(); document.execCommand('copy'); alert('Lien copié !');"
                                               class="text-xs border-gray-200 rounded px-2 py-1 w-40 cursor-pointer
                                                      bg-gray-50 text-gray-600 truncate">

                                        {{-- QR Code --}}
                                        <div class="flex-shrink-0">
                                            {!! QrCode::size(60)->generate($inviteUrl) !!}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(!$invitation->isUsed())
                                    <form action="{{ route('admin.invitations.destroy', $invitation) }}"
                                          method="POST"
                                          onsubmit="return confirm('Supprimer cette invitation ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 hover:underline text-sm">
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                Aucune invitation pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($invitations->hasPages())
                <div class="px-6 py-4 border-t">{{ $invitations->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>