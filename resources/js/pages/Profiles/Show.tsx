import { Link, router } from '@inertiajs/react';
import { Profile } from '@/types/profile';
import { relativeDate }
from '@/lib/date';
import { useState } from 'react';

interface Props {
    profile: Profile;
}

export default function Show({ profile, }: Props) {
    const refreshProfile = () => {
        router.post(`/profiles/${profile.id}/refresh`);
    };
    const [loading, setLoading] = useState(false);

    return (
        <div className="mx-auto max-w-7xl p-8">
            <div className="mb-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div className="flex items-center gap-5">
                    <img
                        src={
                            profile.profile_picture_url ? `/proxy-image?url=${encodeURIComponent(profile.profile_picture_url)}` : 'https://placehold.co/120'
                        }
                        alt={profile.username} className="h-24 w-24 rounded-full border object-cover"
                        onError={(e) => {
                            e.currentTarget.src = 'https://placehold.co/120';
                        }}
                    />
                    <div>
                        <h1 className="text-3xl font-bold">@{profile.username}</h1>
                        <p className="mt-2 max-w-xl text-sm text-muted-foreground">{profile.bio ||'No bio available'}</p>
                        <div className="mt-3">
                            <StatusBadge status={profile.status}/>
                        </div>
                    </div>
                </div>
                <div className="flex gap-3">
                    <button onClick={refreshProfile} className="rounded-lg bg-black px-5 py-2 text-white">
                        Refresh Now
                    </button>
                    <Link href="/profiles" className="rounded-lg border px-5 py-2">
                        Back
                    </Link>
                </div>
            </div>
            <div className="mb-10 grid gap-4 md:grid-cols-4">
                <StatCard title="Followers" value={formatNumber(profile.followers_count)}/>
                <StatCard title="Following" value={formatNumber(profile.following_count)}/>
                <StatCard title="Posts" value={formatNumber(profile.posts_count)}/>
                <StatCard title="Last Refresh"
                    value = { profile.last_refreshed_at ? new Date(profile.last_refreshed_at).toLocaleDateString() : '-'}
                />
            </div>
            <div className="overflow-hidden rounded-xl border bg-white dark:bg-neutral-950">
                <div className="border-b p-5">
                    <h2 className="text-lg font-semibold">
                        Snapshot History
                    </h2>
                    <p className="text-sm text-muted-foreground">
                        Historical follower growth
                    </p>
                </div>
                <table className="w-full">
                    <thead className="bg-gray-100 dark:bg-neutral-900">
                        <tr>
                            <th className="p-4 text-left">Date</th>
                            <th className="p-4 text-left">Followers</th>
                            <th className="p-4 text-left">Delta</th>
                            <th className="p-4 text-left">Following</th>
                            <th className="p-4 text-left">Posts</th>
                        </tr>
                    </thead>
                    <tbody>
                        {profile.snapshots.map((snapshot) => (
                                <tr key={snapshot.id} className="border-t">
                                    <td className="p-4">
                                        {relativeDate(snapshot.captured_at)}
                                    </td>
                                    <td className="p-4">
                                        {snapshot.followers_count.toLocaleString()}
                                    </td>
                                    <td className={`p-4 font-semibold ${
                                            snapshot.delta > 0 ? 'text-green-600' : snapshot.delta < 0 ? 'text-red-600' : ''
                                        }`}
                                    >
                                        {snapshot.delta > 0 ? '+': ''}
                                        {snapshot.delta.toLocaleString()}
                                    </td>
                                    <td className="p-4">
                                        {snapshot.following_count.toLocaleString()}
                                    </td>
                                    <td className="p-4">
                                        {snapshot.posts_count.toLocaleString()}
                                    </td>
                                </tr>
                            )
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

function StatCard({ title, value, }: {
    title: string;
    value: string;
}) {
    return (
        <div className="rounded-xl border bg-white p-5 shadow-sm dark:bg-neutral-950">
            <div className="text-sm text-muted-foreground">{title}</div>
            <div className="mt-2 text-2xl font-bold">{value}</div>
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
        <span
            className={`rounded-full px-3 py-1 text-xs font-medium ${
                colors[status as keyof typeof colors]
            }`}>
            {status}
        </span>
    );
}

function formatNumber(value: number | null): string {
    if (!value) return '-';
    return value.toLocaleString();
}
