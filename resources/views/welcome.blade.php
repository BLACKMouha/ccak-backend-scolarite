<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'CCAK Scolarite API') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|ibm-plex-mono:400,500" rel="stylesheet" />
        <style>
            :root {
                --ink: #1f2a1f;
                --muted: #4c5a4f;
                --paper: #f7f2e7;
                --sand: #efe7d7;
                --leaf: #2b6a5f;
                --sun: #f2b84b;
                --line: #d7d1c4;
                --shadow: 0 24px 60px rgba(20, 28, 20, 0.12);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: "Space Grotesk", "Segoe UI", sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at 20% 10%, #fef1c9 0%, transparent 45%),
                    radial-gradient(circle at 90% 0%, #dff4f2 0%, transparent 40%),
                    linear-gradient(135deg, var(--paper) 0%, #f4f7f1 40%, #eaf3ef 100%);
                min-height: 100vh;
            }

            .page {
                max-width: 1100px;
                margin: 0 auto;
                padding: 40px 24px 56px;
                display: grid;
                gap: 40px;
            }

            .top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 600;
            }

            .dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--leaf), #3aa18f);
                box-shadow: 0 0 0 6px rgba(43, 106, 95, 0.08);
            }

            .badge {
                font-size: 12px;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                padding: 4px 10px;
                border-radius: 999px;
                background: var(--sand);
                color: var(--muted);
            }

            .nav {
                display: flex;
                gap: 20px;
                font-size: 14px;
            }

            .nav a {
                color: var(--muted);
                text-decoration: none;
                border-bottom: 1px solid transparent;
                padding-bottom: 2px;
            }

            .nav a:hover {
                border-color: var(--leaf);
                color: var(--leaf);
            }

            .hero {
                display: grid;
                grid-template-columns: 1.2fr 0.9fr;
                gap: 32px;
                align-items: stretch;
            }

            .intro {
                animation: rise 700ms ease-out both;
            }

            .eyebrow {
                font-size: 14px;
                letter-spacing: 0.2em;
                text-transform: uppercase;
                color: var(--muted);
                margin: 0 0 12px;
            }

            h1 {
                font-size: clamp(32px, 4vw, 52px);
                margin: 0 0 12px;
            }

            .lead {
                font-size: 18px;
                color: var(--muted);
                max-width: 520px;
                margin: 0 0 24px;
            }

            .cta {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 18px;
                border-radius: 999px;
                font-weight: 600;
                text-decoration: none;
                transition: transform 150ms ease, box-shadow 150ms ease;
            }

            .btn.primary {
                background: var(--leaf);
                color: white;
                box-shadow: var(--shadow);
            }

            .btn.ghost {
                background: transparent;
                border: 1px solid var(--leaf);
                color: var(--leaf);
            }

            .btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 30px rgba(20, 28, 20, 0.18);
            }

            .api-base {
                margin-top: 12px;
                font-size: 14px;
                color: var(--muted);
            }

            .api-base code {
                font-family: "IBM Plex Mono", ui-monospace, monospace;
                background: #eef3ec;
                border-radius: 6px;
                padding: 2px 6px;
                color: var(--ink);
            }

            .meta {
                margin: 28px 0 0;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 16px;
            }

            .meta div {
                background: white;
                border: 1px solid var(--line);
                border-radius: 16px;
                padding: 14px 16px;
                box-shadow: 0 10px 30px rgba(20, 28, 20, 0.06);
            }

            .meta dt {
                font-size: 12px;
                color: var(--muted);
                text-transform: uppercase;
                letter-spacing: 0.08em;
                margin-bottom: 6px;
            }

            .meta dd {
                margin: 0;
                font-weight: 600;
            }

            .panel {
                background: white;
                border-radius: 24px;
                padding: 24px;
                border: 1px solid var(--line);
                box-shadow: var(--shadow);
                display: grid;
                gap: 20px;
                animation: rise 800ms ease-out 100ms both;
            }

            .panel h2 {
                margin: 0;
                font-size: 20px;
            }

            .panel p {
                margin: 0;
                color: var(--muted);
            }

            .code {
                font-family: "IBM Plex Mono", ui-monospace, monospace;
                font-size: 13px;
                background: #0f1b18;
                color: #f8f3e6;
                padding: 12px 14px;
                border-radius: 12px;
                overflow-wrap: anywhere;
            }

            .list {
                list-style: none;
                margin: 0;
                padding: 0;
                display: grid;
                gap: 10px;
            }

            .list li {
                display: flex;
                justify-content: space-between;
                font-size: 14px;
                padding-bottom: 8px;
                border-bottom: 1px dashed var(--line);
            }

            .list span {
                color: var(--muted);
            }

            .modules {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                font-size: 13px;
            }

            .module {
                padding: 10px 12px;
                border-radius: 12px;
                background: var(--sand);
                color: var(--muted);
                border: 1px solid #e6ddcd;
            }

            .foot {
                font-size: 13px;
                color: var(--muted);
            }

            @keyframes rise {
                from {
                    opacity: 0;
                    transform: translateY(16px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 860px) {
                .hero {
                    grid-template-columns: 1fr;
                }

                .nav {
                    flex-wrap: wrap;
                    justify-content: flex-end;
                }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header class="top">
                <div class="brand">
                    <span class="dot"></span>
                    <span>{{ config('app.name', 'CCAK Scolarite') }}</span>
                    <span class="badge">API</span>
                </div>
                <nav class="nav">
                    <a href="{{ url('/docs/api') }}">Docs</a>
                    <a href="{{ url('/up') }}">Health</a>
                    <a href="{{ config('keycloak.issuer') }}">Keycloak</a>
                </nav>
            </header>

            <main class="hero">
                <section class="intro">
                    <p class="eyebrow">Academic management platform</p>
                    <h1>CCAK Scolarite backend services</h1>
                    <p class="lead">
                        Secure APIs for enrollment, academic structures, grading, finance, and document generation.
                    </p>
                    <div class="cta">
                        <a class="btn primary" href="{{ url('/docs/api') }}">Open API docs</a>
                        <a class="btn ghost" href="https://si-api.ucak.sn" target="_blank" rel="noopener noreferrer">Production API</a>
                        <a class="btn ghost" href="https://back-office.ucak.sn" target="_blank" rel="noopener noreferrer">Back Office</a>
                    </div>
                    <p class="api-base">API base: <code>/api/v1</code></p>
                    <dl class="meta">
                        <div>
                            <dt>Environment</dt>
                            <dd>{{ config('app.env') }}</dd>
                        </div>
                        <div>
                            <dt>Version</dt>
                            <dd>{{ config('scramble.info.version', 'v1') }}</dd>
                        </div>
                        <div>
                            <dt>Realm</dt>
                            <dd>{{ config('keycloak.realm') }}</dd>
                        </div>
                    </dl>
                </section>

                <aside class="panel">
                    <div>
                        <h2>Quick start</h2>
                        <p>Use a Keycloak bearer token for protected routes.</p>
                    </div>
                    <div class="code">Authorization: Bearer &lt;token&gt;</div>
                    <ul class="list">
                        <li><span>Docs</span> /docs/api</li>
                        <li><span>Health</span> /up</li>
                        <li><span>Base</span> /api/v1</li>
                    </ul>
                    <div class="modules">
                        <div class="module">Academic structure</div>
                        <div class="module">Enrollment</div>
                        <div class="module">Grades</div>
                        <div class="module">Finance</div>
                        <div class="module">Documents</div>
                        <div class="module">Reports</div>
                    </div>
                </aside>
            </main>

            <footer class="foot">
                CCAK Scolarite API — built for secure academic operations.
            </footer>
        </div>
    </body>
</html>
