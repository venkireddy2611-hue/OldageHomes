// Simple input validators
export function isValidEmail(email) {
    const re = /^[\w-.]+@[\w-]+\.[A-Za-z]{2,}$/;
    return re.test(String(email).toLowerCase());
}

export function isValidPhone(phone) {
    // Relaxed phone validator: digits, spaces, +, -, parentheses
    const re = /^[0-9+()\-\s]{6,20}$/;
    return re.test(String(phone).trim());
}

export function serializeForm(form) {
    const fd = new FormData(form);
    const obj = {};
    for (const [k, v] of fd.entries()) {
        if (obj[k]) {
            if (!Array.isArray(obj[k])) obj[k] = [obj[k]];
            obj[k].push(v);
        } else {
            obj[k] = v;
        }
    }
    return obj;
}