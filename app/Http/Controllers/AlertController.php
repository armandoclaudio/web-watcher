<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlertRequest;
use App\Http\Requests\UpdateAlertRequest;
use App\Models\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        $alerts = Alert::withCount('webNotifications')->latest()->get();

        return view('alerts.index', compact('alerts'));
    }

    public function create(): View
    {
        return view('alerts.create');
    }

    public function store(StoreAlertRequest $request): RedirectResponse
    {
        Alert::create($request->validated());

        return redirect()->route('alerts.index')->with('success', 'Alert created.');
    }

    public function edit(Alert $alert): View
    {
        return view('alerts.edit', compact('alert'));
    }

    public function update(UpdateAlertRequest $request, Alert $alert): RedirectResponse
    {
        $alert->update($request->validated());

        return redirect()->route('alerts.index')->with('success', 'Alert updated.');
    }

    public function destroy(Alert $alert): RedirectResponse
    {
        $alert->delete();

        return redirect()->route('alerts.index')->with('success', 'Alert deleted.');
    }
}
