<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono&display=swap" rel="stylesheet">
<!-- HERO -->
<section class="hero">
    <div class="container hero-inner">
        <h2>
            Build PHP apps<br>
            <span class="gradient">faster. cleaner. smarter.</span>
        </h2>
        <p>
            A modern lightweight framework with a built-in asset pipeline,
            reactive navigation system, and zero unnecessary abstraction.
        </p>

        <p><span>Ziro</span> friction, Maximum velocity</p>

        <div class="cta">
            <a href="#start" class="btn primary">Get Started</a>
            <a href="https://github.com/CollinsFabian/Ziro" target="_blank" class="btn">View GitHub</a>
        </div>
    </div>

    <div class="code-block">
        <div class="code-header">routes.php</div>
        <pre>
            <code class="language-php">$router->get('/', [UserController::class, 'index']);</code>
        </pre>
    </div>
</section>

<!-- FEATURES -->
<section id="features" class="features container">
    <h3>Why Ziro?</h3>

    <div class="grid">
        <div class="card">
            <h4>⚡ Speed First</h4>
            <p>Minimal overhead. Maximum performance.</p>
        </div>

        <div class="card">
            <h4>🔥 Built-in Builder</h4>
            <p>Dev server, HMR, and production bundling.</p>
        </div>

        <div class="card">
            <h4>🧠 Smart Navigation</h4>
            <p>PageState system for seamless UX.</p>
        </div>

        <div class="card">
            <h4>🧱 Flexible Structure</h4>
            <p>No rigid conventions forcing your architecture.</p>
        </div>

        <div class="card">
            <h4>📦 Hybrid DB</h4>
            <p>Use MySQLi or PDO without friction.</p>
        </div>

        <div class="card">
            <h4>🛠 Dev Experience</h4>
            <p>Hot reload, clean builds, smooth workflow.</p>
        </div>
    </div>
</section>

<!-- CODE -->
<section id="code" class="code container">
    <h3>Simple & Powerful</h3>

    <div class="code-block">
        <div class="code-header">routes.php</div>
        <pre>
            <code class="language-php">$router->get('/courses/{id}', [CourseController::class, 'show']);</code>
        </pre>
    </div>

    <div>
        <div class="code-header">z</div>
        <pre>
            <code class="language-bash">php z build --dev</code>
        </pre>
    </div>

</section>

<!-- START -->
<section id="start" class="start container">
    <h3>Get Started</h3>

    <div class="steps">
        <div class="step">
            <span>01</span>
            Clone repo
        </div>
        <div class="step">
            <span>02</span>
            Run build
        </div>
        <div class="step">
            <span>03</span>
            Build something real
        </div>
    </div>
</section>