export function relativeDate(
    date?: string | null
) {
    if (!date) return '-';

    const now = new Date().getTime();

    const target = new Date(
        date
    ).getTime();

    const diff = Math.floor(
        (now - target) / 1000
    );

    if (diff < 60)
        return `${diff}s ago`;

    if (diff < 3600)
        return `${Math.floor(
            diff / 60
        )}m ago`;

    if (diff < 86400)
        return `${Math.floor(
            diff / 3600
        )}h ago`;

    return `${Math.floor(
        diff / 86400
    )}d ago`;
}
