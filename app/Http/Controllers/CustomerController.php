<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Customers/Index', [
            'customers' => Customer::query()
                ->with('properties')
                ->when($request->query('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'filters' => [
                'search' => $request->query('search', ''),
            ],
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
    public function store(CustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $customer = Customer::create(collect($validated)->except('properties')->toArray());

        if (! empty($validated['properties'])) {
            $customer->properties()->createMany(
                collect($validated['properties'])->map(function (array $p): array {
                    return collect($p)->except('_location')->toArray();
                })->toArray()
            );
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
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
    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validated();

        $customer->update(collect($validated)->except('properties')->toArray());

        $existingIds = [];

        foreach ($validated['properties'] ?? [] as $propertyData) {
            $updateData = collect($propertyData)->except(['_location', 'id'])->toArray();

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
