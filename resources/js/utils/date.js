/**
 * Thai Date Utils (Production-grade)
 * - Fix timezone: Asia/Bangkok
 * - รองรับ SSR / Browser
 * - กัน timezone shift
 * - รองรับ format flexible
 */

const TZ = 'Asia/Bangkok';

const THAI_MONTHS_LONG  = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
const THAI_MONTHS_SHORT = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

/**
 * ตรวจสอบ valid date
 */
function isValidDate(d) {
    return d instanceof Date && !isNaN(d.getTime());
}

/**
 * Parse date อย่างปลอดภัย
 */
function parseDate(value) {
    if (!value) return null;

    if (value instanceof Date) {
        return isValidDate(value) ? value : null;
    }

    const s = String(value).trim();

    // YYYY-MM-DD → force local (กัน shift)
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) {
        const d = new Date(s + 'T00:00:00');
        return isValidDate(d) ? d : null;
    }

    const d = new Date(s);
    return isValidDate(d) ? d : null;
}

/**
 * ดึง "เวลาประเทศไทย" จาก Date (fix timezone)
 */
function getThaiParts(date) {
    const parts = new Intl.DateTimeFormat('en-GB', {
        timeZone: TZ,
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        hour12: false
    }).formatToParts(date);

    const map = {};
    for (const p of parts) {
        if (p.type !== 'literal') {
            map[p.type] = parseInt(p.value, 10);
        }
    }

    return {
        day: map.day,
        month: map.month - 1,
        year: map.year,
        hour: map.hour,
        minute: map.minute,
        second: map.second
    };
}

/**
 * Core formatter
 */
function formatThai(value, {
    showTime = false,
    shortMonth = false,
    showSeconds = false
} = {}) {
    const d = parseDate(value);
    if (!d) return '-';

    const t = getThaiParts(d);

    const month = shortMonth
        ? THAI_MONTHS_SHORT[t.month]
        : THAI_MONTHS_LONG[t.month];

    const yearBE = t.year + 543;

    if (!showTime) {
        return `${t.day} ${month} ${yearBE}`;
    }

    const hh = String(t.hour).padStart(2, '0');
    const mm = String(t.minute).padStart(2, '0');
    const ss = String(t.second).padStart(2, '0');

    return showSeconds
        ? `${t.day} ${month} ${yearBE} ${hh}:${mm}:${ss}`
        : `${t.day} ${month} ${yearBE} ${hh}:${mm}`;
}

/** =========================
 * Export functions
 * ========================= */

/** "22 มีนาคม 2569" */
export function thaiFullDate(value) {
    return formatThai(value);
}

/** "22 มี.ค. 2569" */
export function thaiDate(value) {
    return formatThai(value, { shortMonth: true });
}

/** "22 มีนาคม 2569 14:24" */
export function thaiDateTime(value) {
    return formatThai(value, { showTime: true });
}

/** "22 มีนาคม 2569 14:24:30" */
export function thaiDateTimeSec(value) {
    return formatThai(value, { showTime: true, showSeconds: true });
}

/** "14:24" */
export function thaiTime(value) {
    const d = parseDate(value);
    if (!d) return '-';

    const t = getThaiParts(d);

    return `${String(t.hour).padStart(2, '0')}:${String(t.minute).padStart(2, '0')}`;
}

/** "14:24:30" */
export function thaiTimeSec(value) {
    const d = parseDate(value);
    if (!d) return '-';

    const t = getThaiParts(d);

    return `${String(t.hour).padStart(2, '0')}:${String(t.minute).padStart(2, '0')}:${String(t.second).padStart(2, '0')}`;
}

/** Month short */
export function thaiMonth(value) {
    const d = parseDate(value);
    if (!d) return '';
    return THAI_MONTHS_SHORT[getThaiParts(d).month];
}

/** Month long */
export function thaiMonthLong(value) {
    const d = parseDate(value);
    if (!d) return '';
    return THAI_MONTHS_LONG[getThaiParts(d).month];
}

/** Day */
export function thaiDay(value) {
    const d = parseDate(value);
    if (!d) return '';
    return getThaiParts(d).day;
}

/** Year (B.E.) */
export function thaiYear(value) {
    const d = parseDate(value);
    if (!d) return '';
    return getThaiParts(d).year + 543;
}