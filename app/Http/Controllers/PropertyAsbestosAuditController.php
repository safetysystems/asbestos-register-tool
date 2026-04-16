<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyAsbestosAuditRequest;
use App\Models\Customer;
use App\Models\CustomerProperty;
use App\Models\PropertyAsbestosAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyAsbestosAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('AsbestosAudits/Index', [
            'audits' => PropertyAsbestosAudit::query()
                ->with(['property.customer', 'samples'])
                ->when($request->query('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('qr_number', 'like', "%{$search}%")
                            ->orWhere('job_type', 'like', "%{$search}%")
                            ->orWhereHas('property', function ($pq) use ($search) {
                                $pq->where('name', 'like', "%{$search}%")
                                    ->orWhere('address', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'filters' => [
                'search' => $request->query('search', ''),
            ],
            'customers' => Customer::with('properties')->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        $selectedProperty = null;

        if ($request->query('property_id')) {
            $selectedProperty = CustomerProperty::with('customer')
                ->find($request->query('property_id'));
        }

        return Inertia::render('AsbestosAudits/CreateForm', [
            'customers' => Customer::with('properties')->orderBy('name')->get(),
            'selectedProperty' => $selectedProperty,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PropertyAsbestosAuditRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $audit = PropertyAsbestosAudit::create(collect($validated)->except('samples')->toArray());

        foreach (($validated['samples'] ?? []) as $sampleData) {
            $sample = $audit->samples()->create(
                collect($sampleData)->except(['id', 'images'])->toArray()
            );

            foreach (($sampleData['images'] ?? []) as $image) {
                $sample->addMedia($image)->toMediaCollection('sample_images');
            }
        }

        return redirect()->route('asbestosaudits.index')
            ->with('success', 'Asbestos audit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PropertyAsbestosAudit $asbestosaudit): Response
    {
        return Inertia::render('AsbestosAudits/EditForm', [
            'audit' => $asbestosaudit->load(['property.customer', 'samples']),
            'properties' => CustomerProperty::with('customer')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PropertyAsbestosAudit $asbestosaudit): Response
    {
        return Inertia::render('AsbestosAudits/EditForm', [
            'audit' => $asbestosaudit->load(['property.customer', 'samples']),
            'properties' => CustomerProperty::with('customer')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PropertyAsbestosAuditRequest $request, PropertyAsbestosAudit $asbestosaudit): RedirectResponse
    {
        $validated = $request->validated();

        $asbestosaudit->update(collect($validated)->except('samples')->toArray());

        $existingIds = [];

        foreach (($validated['samples'] ?? []) as $sampleData) {
            $updateData = collect($sampleData)->except(['id', 'images', 'delete_media_ids'])->toArray();

            if (! empty($sampleData['id'])) {
                $asbestosaudit->samples()->where('id', $sampleData['id'])->update($updateData);
                $sample = $asbestosaudit->samples()->find($sampleData['id']);
                $existingIds[] = (int) $sampleData['id'];
            } else {
                $sample = $asbestosaudit->samples()->create($updateData);
                $existingIds[] = (int) $sample->id;
            }

            if ($sample) {
                $deleteMediaIds = collect($sampleData['delete_media_ids'] ?? [])
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                if ($deleteMediaIds->isNotEmpty()) {
                    $sample->media()->whereIn('id', $deleteMediaIds)->get()->each->delete();
                }

                foreach (($sampleData['images'] ?? []) as $image) {
                    $sample->addMedia($image)->toMediaCollection('sample_images');
                }
            }
        }

        $asbestosaudit->samples()->whereNotIn('id', $existingIds)->delete();

        if (($validated['redirect_to'] ?? null) === 'auditing') {
            return redirect()->route('auditing.show', $asbestosaudit)
                ->with('success', 'Asbestos audit updated successfully.');
        }

        return redirect()->route('asbestosaudits.index')
            ->with('success', 'Asbestos audit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PropertyAsbestosAudit $asbestosaudit): RedirectResponse
    {
        $asbestosaudit->delete();

        return redirect()->route('asbestosaudits.index')
            ->with('success', 'Asbestos audit deleted successfully.');
    }
}
