import { Link, useForm } from '@inertiajs/react';

export default function Create() {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        username: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/profiles');
    };

    return (
        <div className="mx-auto max-w-2xl p-8">
            <div className="mb-8">
                <h1 className="text-3xl font-bold">
                    Add Influencer
                </h1>
                <p className="mt-2 text-sm text-muted-foreground">
                    Add an Instagram profile to the watchlist.
                    The profile data will be fetched in the background
                    through a queued job.
                </p>
            </div>
            <div className="rounded-2xl border bg-white p-8 shadow-sm dark:bg-neutral-950">
                <form onSubmit={submit} className="space-y-6">
                    <div>
                        <label className="mb-2 block text-sm font-medium">
                            Instagram Username
                        </label>
                        <input type="text" value={data.username}
                            onChange={(e) => setData('username', e.target.value)}
                            placeholder="@cristiano"
                            className="w-full rounded-lg border px-4 py-3 outline-none focus:ring-2 focus:ring-black dark:bg-neutral-900"
                        />
                        <p className="mt-2 text-xs text-muted-foreground">
                            Enter only the username.
                            We automatically remove @ and normalize it.
                        </p>
                        {errors.username && (
                            <p className="mt-2 text-sm text-red-500">
                                {errors.username}
                            </p>
                        )}
                    </div>
                    <div className="rounded-xl border bg-gray-50 p-4 dark:bg-neutral-900">
                        <h3 className="mb-2 font-semibold">
                            What happens next?
                        </h3>
                        <ul className="space-y-1 text-sm text-muted-foreground">
                            <li>
                                • Profile gets added to watchlist
                            </li>
                            <li>
                                • FetchProfileJob is queued
                            </li>
                            <li>
                                • Apify fetches latest public data
                            </li>
                            <li>
                                • Snapshot history is stored
                            </li>
                        </ul>
                    </div>
                    <div className="flex items-center gap-3">
                        <button type="submit" disabled={processing}
                            className="rounded-lg bg-black px-5 py-3 text-white transition hover:opacity-90 disabled:opacity-50"
                        >
                            {processing
                                ? 'Adding Profile...'
                                : 'Add To Watchlist'}
                        </button>
                        <Link href="/profiles" className="rounded-lg border px-5 py-3">
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
}
