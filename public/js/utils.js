(() => {
  // resources/js/utils.js
  var select = (e) => document.querySelector(e);
  var selectAll = (e) => document.querySelectorAll(e);
  var id = (e) => document.getElementById(e);
  var createEl = (e) => document.createElement(e);
  var asyncFetch = async (verb, url, responseType = "json", options = {}) => {
    verb = verb.toUpperCase();
    const allowed = ["GET", "POST", "PATCH", "DELETE"];
    if (!allowed.includes(verb)) throw new Error(`Invalid HTTP verb: ${verb}`);
    const isStateChanging = verb !== "GET";
    const headers = { "Accept": "application/json", ...options.headers || {} };
    headers["X-Client-Type"] = "CLIEN-T-JS";
    if (isStateChanging && window.CSRF) headers["X-CSRF-Token"] = window.CSRF;
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
        headers
      });
    } catch (err) {
      if (err.name === "AbortError") throw err;
      throw new Error(`Network error: ${err}`);
    }
    responseType = responseType.toLowerCase();
    return responseType === "json" ? res.json() : res.text();
  };
})();
//# sourceMappingURL=utils.js.map
