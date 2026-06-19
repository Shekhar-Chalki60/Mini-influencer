<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreProfileRequest;
use App\Jobs\FetchProfileJob;
use App\Models\Profile;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(Request $request): Response
    {
        $profiles = Profile::query()->when($request->q, fn ($query, $search) =>
                $query->where('username', 'ILIKE', "%{$search}%")
            )->when(
                $request->status,
                fn ($query, $status) => $query->where('status', $status)
            )->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Profiles/Index', ['profiles' => $profiles, 'filters' => $request->only(['q', 'status',]),]);
    }

    public function create(): Response
    {
        return Inertia::render('Profiles/Create');
    }

    public function store(StoreProfileRequest $request)
    {
        $profile = Profile::create([
            'username' => str($request->username)->lower()->replace('@', ''),
            'status' => Profile::STATUS_PENDING,
        ]);
        FetchProfileJob::dispatch($profile);
        return redirect()->route('profiles.show', $profile);
    }

    public function show(Profile $profile): Response
    {
        $profile->load(['snapshots' => fn ($query) => $query
                ->latest('captured_at')
                ->limit(50),
        ]);
        $snapshots = $profile->snapshots->values();
        $snapshots = $snapshots->map(function ($snapshot, $index) use ($profile) {
            $previous = $profile->snapshots[$index + 1] ?? null;
            return [
                ...$snapshot->toArray(),
                'delta' => $previous ? $snapshot->followers_count - $previous->followers_count : 0,
            ];
        });
        return Inertia::render('Profiles/Show', ['profile' => [
                ...$profile->toArray(),
                'snapshots' => $snapshots,
            ],
        ]);
    }

    public function refresh(Profile $profile)
    {
        FetchProfileJob::dispatch($profile);
        return back();
    }
}
