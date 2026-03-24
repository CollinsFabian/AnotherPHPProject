<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= asset("css/landing.css") ?>">
</head>

<body>
    <?php require base_path("app/Views/partials/header.php") ?>

    <div id="main">
        <?= $content ?>
    </div>

    <?php require base_path("app/Views/partials/footer.php") ?>

    <script src="<?= asset("js/landing.js") ?>"></script>

    <?php if (getenv('APP_ENV') === 'dev'): ?>
        <script>
            (function() {
                const ws = new WebSocket('ws://localhost:3002');
                ws.onmessage = (ev) => {
                    const m = JSON.parse(ev.data);
                    if (m.type === 'css') {
                        const links = document.querySelectorAll('link[rel=stylesheet]');
                        links.forEach(l => {
                            const a = l.href.split('/').pop().split('.')[0];
                            const f = m.file.split('/').pop().split('.')[0];
                            if (a === f) {
                                l.href = m.file + '?' + Date.now();
                                console.log('[HMR] CSS updated:', m.file);
                            }
                        });
                    } else if (m.type === 'js') {
                        console.log('[HMR] JS changed, reloading page...');
                        location.reload();
                    };
                };
            })();
        </script>
    <?php endif; ?>
</body>

</html>