export function useDate() {
    const formatDate = (dateString) => {
        if (!dateString) return null;
        if (dateString.includes("T")) {
            return dateString.split("T")[0];
        }
        if (dateString.includes(" ")) {
            return dateString.split(" ")[0];
        }
        return dateString; // fallback
    };

    return { formatDate };
}
