import { router } from "./router.js";
import { mountLandingPage } from "./pages/landing.js";
import { mountLoginPage } from "./pages/login.js";
import { mountDashboardPage } from "./pages/dashboard.js";
import { mountNotFoundPage } from "./pages/not-found.js";

router.get('/', async () => mountLandingPage());
router.get('/home/{slug1}', async () => mountLandingPage());
router.get('/login', async () => mountLoginPage());
router.get('/dashboard', async () => mountDashboardPage());
router.notFound(async () => mountNotFoundPage());

document.addEventListener('click', (event) => {
    const link = event.target.closest('a');
    if (!link) return;

    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || link.target === '_blank') return;
    if (/^(mailto:|tel:|https?:)/.test(href)) return;

    const url = new URL(href, window.location.origin);
    if (url.origin !== window.location.origin) return;

    event.preventDefault();
    router.navigate(url.pathname);
});

router.init();

if (window.location.hostname === 'localhost') {
    try {
        const ws = new WebSocket('ws://localhost:3002');

        ws.onopen = () => console.log("HMR mode: ON")
        ws.onerror = (e) => console.log("Websocket error:", e)
        ws.onmessage = (event) => {
            const msg = JSON.parse(event.data);

            if (msg.type === 'css') {
                document.querySelectorAll('link[rel=stylesheet]').forEach((link) => {
                    const pathname = new URL(link.href).pathname;
                    if (pathname === `/${msg.file}` || pathname.endsWith(`/${msg.file.split('/').pop()}`)) {
                        const baseHref = link.href.split('?')[0];
                        link.href = `${baseHref}?${Date.now()}`;
                    }
                });
            }

            if (msg.type === 'js') {
                console.log('[HMR] JS changed, reloading page...');
                window.location.reload();
            }
        };
        ws.onclose = (e) => console.log("HMR closed", e.code, e.reason)
    } catch (e) {
        console.error('Failed to initialize HMR (Hot Module Replacement). This may be due to the dev server not running on ws://localhost:3002 or a network issue. Please ensure your development server supports HMR and is running. Detailed error:', e);
    }
}
