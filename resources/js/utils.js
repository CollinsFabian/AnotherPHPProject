const select = (e) => document.querySelector(e);
const selectAll = (e) => document.querySelectorAll(e);
const id = (e) => document.getElementById(e);
const createEl = (e) => document.createElement(e);

/**
 * @param {"GET"|"POST"|"PATCH"|"DELETE"} verb
 * @param {string} url - endpoint URL
 * @param {"json"|"text"} responseType
 * @param {object} options - fetch config
 */
const asyncFetch = async (verb, url, responseType = "json", options = {}) => {
    verb = verb.toUpperCase();
    const allowed = ["GET", "POST", "PATCH", "DELETE"];

    if (!allowed.includes(verb)) throw new Error(`Invalid HTTP verb: ${verb}`);

    const isStateChanging = verb !== "GET";
    const headers = { "Accept": "application/json", ...(options.headers || {}) };
    headers["X-Client-Type"] = "CLIEN-T-JS";

    // auto-attach CSRF for state-changing requests
    if (isStateChanging && window.CSRF) headers["X-CSRF-Token"] = window.CSRF;

    // auto-stringify JSON bodies
    const all_check = options.body && typeof options.body === "object" && !(options.body instanceof FormData) && !(options.body instanceof Blob);
    if (all_check) {
        headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(options.body);
    }

    let res;

    try {
        res = await fetch(url, {
            method: verb,
            credentials: "include",
            ...options,
            headers,
        });
    } catch (err) {
        if (err.name === "AbortError") throw err; // important: let caller detect aborts
        throw new Error(`Network error: ${err}`);
    }

    responseType = responseType.toLowerCase();
    return responseType === "json" ? res.json() : res.text();
};

export {asyncFetch, id, select, selectAll, createEl};