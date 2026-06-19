interface Props {
    status: string;
}

export default function StatusBadge({
    status,
}: Props) {
    const styles = {
        fetched:
            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',

        fetching:
            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',

        pending:
            'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',

        failed:
            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    };

    return (
        <span
            className={`rounded-full px-3 py-1 text-xs font-medium ${
                styles[
                    status as keyof typeof styles
                ] || styles.pending
            }`}
        >
            {status}
        </span>
    );
}
