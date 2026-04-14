<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Customers/Index', [
            'customers' => Customer::query()
                ->with('properties')
                ->orderBy('id', 'desc')
                ->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Customers/CreateUser');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
                'phone' => ['nullable', 'string', 'max:255'],
                'address' => ['nullable', 'string', 'max:255'],
                'suburb' => ['nullable', 'string', 'max:255'],
                'state' => ['nullable', 'string', 'max:255'],
                'postcode' => ['nullable', 'string', 'max:255'],
                'properties' => ['nullable', 'array'],
                'properties.*.name' => ['nullable', 'string', 'max:255'],
                'properties.*.address' => ['nullable', 'string', 'max:255'],
                'properties.*.suburb' => ['nullable', 'string', 'max:255'],
                'properties.*.state' => ['nullable', 'string', 'max:255'],
                'properties.*.postcode' => ['nullable', 'string', 'max:255'],
                'properties.*.property_type' => ['nullable', 'string', 'max:255'],
                'properties.*.contact_name' => ['nullable', 'string', 'max:255'],
                'properties.*.contact_phone' => ['nullable', 'string', 'max:255'],
                'properties.*.contact_email' => ['nullable', 'email', 'max:255'],
                'properties.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'properties.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            ]);

            \Log::info('Customer store starting', ['data' => $validated]);

            $customer = Customer::create(collect($validated)->except('properties')->toArray());

            if (! empty($validated['properties'])) {
                $customer->properties()->createMany(
                    collect($validated['properties'])->map(function ($p) {
                        $prop = collect($p)->except('_location')->toArray();
                        if (isset($prop['latitude'])) $prop['latitude'] = $prop['latitude'] === null || $prop['latitude'] === '' ? null : (float) $prop['latitude'];
                        if (isset($prop['longitude'])) $prop['longitude'] = $prop['longitude'] === null || $prop['longitude'] === '' ? null : (float) $prop['longitude'];
                        return $prop;
                    })->toArray()
                );
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            \Log::error('Customer store failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Failed to save customer: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): Response
    {
        return Inertia::render('Customers/Show', [
            'customer' => $customer->load('properties'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): Response
    {
        return Inertia::render('Customers/CreateUser', [
            'customer' => $customer->load('properties'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('customers')->ignore($customer)],
                'phone' => ['nullable', 'string', 'max:255'],
                'address' => ['nullable', 'string', 'max:255'],
                'suburb' => ['nullable', 'string', 'max:255'],
                'state' => ['nullable', 'string', 'max:255'],
                'postcode' => ['nullable', 'string', 'max:255'],
                'properties' => ['nullable', 'array'],
                'properties.*.id' => ['nullable', 'integer'],
                'properties.*.name' => ['nullable', 'string', 'max:255'],
                'properties.*.address' => ['nullable', 'string', 'max:255'],
                'properties.*.suburb' => ['nullable', 'string', 'max:255'],
                'properties.*.state' => ['nullable', 'string', 'max:255'],
                'properties.*.postcode' => ['nullable', 'string', 'max:255'],
                'properties.*.property_type' => ['nullable', 'string', 'max:255'],
                'properties.*.contact_name' => ['nullable', 'string', 'max:255'],
                'properties.*.contact_phone' => ['nullable', 'string', 'max:255'],
                'properties.*.contact_email' => ['nullable', 'email', 'max:255'],
                'properties.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'properties.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            ]);

            \Log::info('Customer update starting', ['id' => $customer->id, 'data' => $validated]);

            $customer->update(collect($validated)->except('properties')->toArray());

            $existingIds = [];

            foreach ($validated['properties'] ?? [] as $propertyData) {
                $updateData = collect($propertyData)->except(['_location', 'id'])->toArray();
                
                if (isset($updateData['latitude'])) $updateData['latitude'] = $updateData['latitude'] === null || $updateData['latitude'] === '' ? null : (float) $updateData['latitude'];
                if (isset($updateData['longitude'])) $updateData['longitude'] = $updateData['longitude'] === null || $updateData['longitude'] === '' ? null : (float) $updateData['longitude'];

                if (! empty($propertyData['id'])) {
                    $customer->properties()->where('id', $propertyData['id'])->update($updateData);
                    $existingIds[] = (int) $propertyData['id'];
                } else {
                    $property = $customer->properties()->create($updateData);
                    $existingIds[] = (int) $property->id;
                }
            }

            $customer->properties()->whereNotIn('id', $existingIds)->delete();

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Customer update failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Failed to update customer: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
