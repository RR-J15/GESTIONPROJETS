<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Poste</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrit le</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}"
                                             class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center
                                                    justify-center text-indigo-600 text-xs font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->position ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.role', $user) }}" method="POST"
                                          class="flex items-center space-x-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role"
                                                onchange="this.form.submit()"
                                                class="border-gray-300 rounded-lg text-sm
                                                       focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="admin"       {{ $user->role->value === 'admin'       ? 'selected' : '' }}>Admin</option>
                                            <option value="chef_projet" {{ $user->role->value === 'chef_projet' ? 'selected' : '' }}>Chef de projet</option>
                                            <option value="membre"      {{ $user->role->value === 'membre'      ? 'selected' : '' }}>Membre</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">
                                        Admin (vous)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 hover:underline text-sm">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>