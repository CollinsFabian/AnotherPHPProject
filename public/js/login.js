(() => {
  // resources/js/utils.js
  var select = (e) => document.querySelector(e);
  var id = (e) => document.getElementById(e);
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

  // resources/js/login.js
  var login_form = id("login-form");
  var email_input = id("email");
  var password_input = id("password");
  var login_btn = select("#login-form button[type=submit]");
  login_btn.addEventListener("click", (e) => {
    e.preventDefault();
    const em = String(email_input.value).trim();
    const p = String(password_input.value).trim();
    if (em == "" || p == "") return;
    login();
  });
  async function login() {
    const email = email_input.value;
    const password = password_input.value;
    const req = await asyncFetch("POST", "/api/v1/login", "json", {
      body: { email, password }
    });
    console.log(req);
  }
})();
//# sourceMappingURL=login.js.map
