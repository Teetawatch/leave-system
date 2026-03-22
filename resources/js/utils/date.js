/**
 * Central date/time formatting utilities for Thai locale
 * Usage: import { thaiDate, thaiDateTime, thaiMonth, thaiDay, thaiYear, thaiFullDate } from '@/utils/date';
 */

const THAI_MONTHS_LONG  = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
const THAI_MONTHS_SHORT = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

/**
 * Parse a date string safely.
 * - YYYY-MM-DD is treated as LOCAL midnight (no UTC shift).
 * - ISO datetime strings (with T) are parsed normally.
 */
function parseDate(value) {
    if (!value) return null;
    if (value instanceof Date) return value;
    const s = String(value);
    // Pure date string — force local timezone
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return new Date(s + 'T00:00:00');
    return new Date(s);
}

/** "22 มีนาคม 2569" */
export function thaiFullDate(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '-';
    return `${d.getDate()} ${THAI_MONTHS_LONG[d.getMonth()]} ${d.getFullYear() + 543}`;
}

/** "22 มี.ค. 2569" */
export function thaiDate(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '-';
    return `${d.getDate()} ${THAI_MONTHS_SHORT[d.getMonth()]} ${d.getFullYear() + 543}`;
}

/** "22 มีนาคม 2569 14:24" */
export function thaiDateTime(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '-';
    const hh = String(d.getHours()).padStart(2, '0');
    const mm = String(d.getMinutes()).padStart(2, '0');
    return `${d.getDate()} ${THAI_MONTHS_LONG[d.getMonth()]} ${d.getFullYear() + 543} ${hh}:${mm}`;
}

/** "14:24" */
export function thaiTime(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '-';
    return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
}

/** Short month "มี.ค." */
export function thaiMonth(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '';
    return THAI_MONTHS_SHORT[d.getMonth()];
}

/** Long month "มีนาคม" */
export function thaiMonthLong(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '';
    return THAI_MONTHS_LONG[d.getMonth()];
}

/** Day number */
export function thaiDay(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '';
    return d.getDate();
}

/** Buddhist year e.g. 2569 */
export function thaiYear(value) {
    const d = parseDate(value);
    if (!d || isNaN(d)) return '';
    return d.getFullYear() + 543;
}
