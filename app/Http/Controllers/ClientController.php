<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;

class ClientController extends Controller
{
    // Liste tous les clients
    public function index()
    {
        $clients = Client::withCount(['quotes', 'projects'])
                         ->orderBy('company_name')
                         ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    // Formulaire création
    public function create()
    {
        return view('clients.create');
    }

    // Enregistre un nouveau client
    public function store(StoreClientRequest $request)
    {
        Client::create($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    // Détail d'un client
    public function show(Client $client)
    {
        $client->load([
            'quotes'   => fn($q) => $q->latest()->take(5),
            'projects' => fn($q) => $q->latest()->take(5),
        ]);

        return view('clients.show', compact('client'));
    }

    // Formulaire édition
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    // Met à jour un client
    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client mis à jour avec succès.');
    }

    // Supprime un client
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}