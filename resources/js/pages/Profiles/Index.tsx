import { Link, router } from '@inertiajs/react';

interface Props {
    profiles: {
        data: any[];
        links: any[];
    };
    filters: {
        q?: string;
        status?: string;
    };
}

export default function Index({ profiles, filters, }: Props) {
    const search = (value: string, status?: string) => {
        router.get('/profiles',
            {
                q: value,
                status,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    };

    return (
        <div className="mx-auto max-w-7xl p-8">
            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold">
                        Influencer Tracker
                    </h1>
                    <p className="text-muted-foreground">
                        Monitor Instagram profiles
                    </p>
                </div>
                <Link href="/profiles/create" className="rounded-lg bg-black px-4 py-2 text-white">
                    Add Profile
                </Link>
            </div>
            <div className="mb-6 flex gap-3">
                <input defaultValue={filters.q} placeholder="Search username..."
                    className="rounded-lg border px-4 py-2 dark:bg-neutral-900"
                    onChange={(e) => search(e.target.value, filters.status)
                    }
                />
                <select defaultValue={filters.status} className="rounded-lg border px-4 py-2 dark:bg-neutral-900"
                    onChange={(e) => search(filters.q ?? '', e.target.value)
                    }
                >
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="fetching">Fetching</option>
                    <option value="fetched">Fetched</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <div className="overflow-hidden rounded-xl border bg-white dark:bg-neutral-950">
                <table className="w-full">
                    <thead className="bg-gray-100 dark:bg-neutral-900">
                        <tr>
                            <th className="p-4 text-left">Profile</th>
                            <th className="p-4 text-left">Status</th>
                            <th className="p-4 text-left">Followers</th>
                            <th className="p-4 text-left">Last Refresh</th>
                        </tr>
                    </thead>
                    <tbody>
                        {profiles.data.map((profile) => (
                                <tr key={profile.id} className="border-t">
                                    <td className="p-4">
                                        <Link href={`/profiles/${profile.id}`} className="font-medium">
                                            @{profile.username}
                                        </Link>
                                    </td>
                                    <td className="p-4">
                                        <StatusBadge status = { profile.status }/>
                                    </td>
                                    <td className="p-4">
                                        {Number(profile.followers_count ?? 0).toLocaleString()}
                                    </td>
                                    <td className="p-4">
                                        {profile.last_refreshed_at ?? '-'}
                                    </td>
                                </tr>
                            )
                        )}
                    </tbody>
                </table>
            </div>
            <div className="mt-6 flex gap-2">
                {profiles.links.map((link, index) => (
                        <Link key={index} href={link.url ?? '#'}
                            dangerouslySetInnerHTML={{
                                __html : link.label,
                            }}
                            className={`rounded border px-3 py-2 ${
                                link.active ? 'bg-black text-white' : ''
                            }`}
                        />
                    )
                )}
            </div>
        </div>
    );
}

function StatusBadge({ status, }: { status: string; }) {
    const colors = {
        pending : 'bg-yellow-100 text-yellow-800',
        fetching : 'bg-blue-100 text-blue-800',
        fetched : 'bg-green-100 text-green-800',
        failed : 'bg-red-100 text-red-800',
    };
    return (
        <span className={`rounded-full px-3 py-1 text-xs ${
                colors[status as keyof typeof colors]
            }`}>
            {status}
        </span>
    );
}
