export interface Snapshot {
    id: number;

    followers_count: number;
    following_count: number;
    posts_count: number;

    captured_at: string;

    delta: number;
}

export interface Profile {
    id: number;
    username: string;
    status: string;

    followers_count: number | null;
    following_count: number | null;
    posts_count: number | null;

    profile_picture_url: string | null;
    bio: string | null;

    last_refreshed_at: string | null;

    snapshots: Snapshot[];
}
