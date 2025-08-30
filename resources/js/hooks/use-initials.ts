/**
 * Generates initials from a full name.
 *
 * @param {string | undefined} fullName - The full name to generate initials from.
 * @returns {string} The generated initials.
 */
export function getInitials(fullName?: string): string {
  if (!fullName)
    return "";

  const names = fullName.trim().split(" ");

  if (names.length === 0)
    return "";
  if (names.length === 1)
    return names[0].charAt(0).toUpperCase();

  return `${names[0].charAt(0)}${names[names.length - 1].charAt(0)}`.toUpperCase();
}
