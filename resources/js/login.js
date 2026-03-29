import { id, select, selectAll, asyncFetch } from "./utils.js";

const login_form = id("login-form");
const email_input = id("email");
const password_input = id("password");
const login_btn = select("#login-form button[type=submit]");

login_btn.addEventListener("click", (e) => {
    e.preventDefault();
    const em = String(email_input.value).trim();
    const p = String(password_input.value).trim();
    if (em == "" || p == "") return;
    login();
})

async function login() {
    const email = email_input.value;
    const password = password_input.value;

    const req = await asyncFetch("POST", "/api/v1/login", "json", {
        body: {email, password}
    });

    console.log(req);
}