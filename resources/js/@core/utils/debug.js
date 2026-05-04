/**
 * Debug logging utility
 * Silently ignores calls in production - no-op stub to prevent console errors
 * 
 * @param {...any} args - Arguments to log
 */
export function debugLog(...args) {
    // No-op in production - ignores all debug logging
    // This prevents console errors when debug calls remain in code
}
