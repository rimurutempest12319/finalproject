<?php
require 'db.php';

// Fetch up to 3 latest projects for homepage preview
$featuredProjects = $conn->query("SELECT * FROM projects ORDER BY id DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home • Joel Diaz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #1f2933, #111827, #0f172a);
            --card-bg: rgba(17, 24, 39, 0.85);
            --border-subtle: rgba(148, 163, 184, 0.3);
            --accent: #38bdf8;
            --accent-soft: rgba(56, 189, 248, 0.15);
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --radius-xl: 18px;
            --shadow-soft: 0 18px 45px rgba(15, 23, 42, 0.55);
            --transition-fast: 0.18s ease-out;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-gradient);
            color: var(--text-main);
            min-height: 100vh;
        }
        a { color: var(--accent); text-decoration: none; }

        .nav {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(18px);
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.1));
            border-bottom: 1px solid var(--border-subtle);
        }
        .nav-inner {
            max-width: 1080px;
            margin: 0 auto;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo {
            font-weight: 600;
            letter-spacing: .08em;
            font-size: 14px;
            text-transform: uppercase;
        }
        .nav-links {
            display: flex;
            gap: 18px;
            font-size: 14px;
        }
        .nav-link {
            color: var(--text-muted);
            position: relative;
        }
        .nav-link::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 0;
            height: 2px;
            background: var(--accent);
            border-radius: 999px;
            transition: width var(--transition-fast);
        }
        .nav-link:hover { color: var(--text-main); }
        .nav-link:hover::after { width: 100%; }
        .nav-link.active {
            color: var(--text-main);
        }
        .nav-link.active::after {
            width: 100%;
        }

        .page {
            max-width: 1080px;
            margin: 0 auto;
            padding: 32px 20px 40px;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1.4fr);
            gap: 32px;
            margin-top: 10px;
            margin-bottom: 28px;
        }
        @media (max-width: 840px) { .hero { grid-template-columns: 1fr; } }

        .hero-main {
            padding: 24px 24px 26px;
            border-radius: var(--radius-xl);
            background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.12), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(96, 165, 250, 0.18), transparent 60%),
                        var(--card-bg);
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-subtle);
        }
        .hero-tag {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .hero-title {
            font-size: clamp(26px, 4vw, 32px);
            font-weight: 600;
            margin-bottom: 6px;
        }
        .hero-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }
        .hero-list {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 18px;
            line-height: 1.6;
        }
        .hero-list b { color: var(--text-main); }

        .hero-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 11px;
            margin-bottom: 18px;
        }
        .chip {
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            border: 1px solid rgba(56, 189, 248, 0.35);
        }
        .chip-muted {
            background: rgba(148, 163, 184, 0.1);
            color: var(--text-muted);
            border-color: rgba(148, 163, 184, 0.4);
        }

        .hero-cta-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn {
            border-radius: 999px;
            border: none;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform var(--transition-fast), box-shadow var(--transition-fast), background var(--transition-fast);
        }
        .btn-primary {
            background: radial-gradient(circle at top left, #38bdf8, #0ea5e9);
            color: #0b1120;
            box-shadow: 0 14px 35px rgba(56, 189, 248, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 18px 42px rgba(56, 189, 248, 0.45);
        }
        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
        }
        .btn-ghost:hover {
            background: rgba(15, 23, 42, 0.7);
            color: var(--text-main);
            transform: translateY(-1px);
        }

        .hero-side {
            padding: 18px 18px 20px;
            border-radius: var(--radius-xl);
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid var(--border-subtle);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
        }
        .avatar {
            width: 160px;
            height: 160px;
            border-radius: 999px;
            object-fit: cover;
            border: 3px solid rgba(56, 189, 248, 0.8);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.9);
        }
        .hero-metric-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .hero-metric-caption {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Featured projects on home */
        .section {
            margin-top: 12px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 14px;
        }
        .section-title { font-size: 18px; font-weight: 500; }
        .section-subtitle { font-size: 12px; color: var(--text-muted); }
        .section-link {
            font-size: 12px;
        }

        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 16px;
        }
        .project-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            padding: 12px 12px 14px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.6);
            position: relative;
            transition: transform 0.18s ease-out, box-shadow 0.18s ease-out, border-color 0.18s ease-out;
            display: block;               /* so the whole card is clickable */
            color: inherit;
        }
        .project-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.14), transparent 60%);
            opacity: 0;
            transition: opacity 0.18s ease-out;
        }
        .project-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.8);
            border-color: rgba(56, 189, 248, 0.6);
        }
        .project-card:hover::before { opacity: 1; }
        .project-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .project-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .project-footer {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
        }

        footer {
            margin-top: 30px;
            font-size: 11px;
            color: var(--text-muted);
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-inner">
        <div class="nav-logo">JOEL DIAZ</div>
        <div class="nav-links">
            <a href="index.php"
               class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                Home
            </a>
            <a href="projects.php"
               class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'projects.php' ? 'active' : ''; ?>">
                Projects
            </a>
            <a href="about.php"
               class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : ''; ?>">
                About
            </a>
            <a href="admin.php" class="nav-link">Admin</a>
        </div>
    </div>
</nav>

<main class="page" id="top">
    <!-- HERO -->
    <section class="hero">
        <div class="hero-main">
            <div class="hero-tag">Hello, I’m Joel 👋</div>
            <div class="hero-title">Aspiring developer from Santa Maria, Bulacan.</div>
            <div class="hero-subtitle">
                I’m <b>Joel Diaz</b>, a BSCS student who enjoys building interactive systems and web projects with PHP and MySQL.
            </div>
            <div class="hero-list">
                <b>Location:</b> Tumana, Santa Maria, Bulacan<br>
                <b>Email:</b> <a href="mailto:joeldiazjr6@gmail.com">joeldiazjr6@gmail.com</a>
            </div>

            <div class="hero-chip-row">
                <span class="chip">Web Development</span>
                <span class="chip chip-muted">PHP · MySQL</span>
                <span class="chip chip-muted">Unity · Game Dev</span>
            </div>

            <div class="hero-cta-row">
                <a href="projects.php" class="btn btn-primary">View my projects</a>
                <a href="about.php" class="btn btn-ghost">More about me</a>
            </div>
        </div>

        <aside class="hero-side">
            <img src="joel.jpg" alt="Photo of Joel Diaz" class="avatar">
            <div class="hero-metric-label">From</div>
            <div class="hero-metric-caption">
                Tumana, Santa Maria, Bulacan<br>
                Always learning and experimenting with new ideas.
            </div>
        </aside>
    </section>

    <!-- FEATURED PROJECTS -->
    <section class="section">
        <div class="section-header">
            <div>
                <div class="section-title">Featured projects</div>
                <div class="section-subtitle">
                    A quick preview of what I’ve been working on.
                </div>
            </div>
            <div class="section-link">
                <a href="projects.php">View all projects →</a>
            </div>
        </div>

        <div class="project-grid">
            <?php if ($featuredProjects && $featuredProjects->num_rows > 0): ?>
                <?php while ($row = $featuredProjects->fetch_assoc()): ?>
                    <a href="project.php?id=<?php echo $row['id']; ?>" class="project-card">
                        <div class="project-title">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </div>
                        <p class="project-desc">
                            <?php
                            $desc = strip_tags($row['description']);
                            if (strlen($desc) > 110) {
                                $desc = substr($desc, 0, 110) . '...';
                            }
                            echo htmlspecialchars($desc);
                            ?>
                        </p>
                        <div class="project-footer">
                            <span>View details</span>
                            <?php if (!empty($row['link'])): ?>
                                <span>Has live link ↗</span>
                            <?php else: ?>
                                <span>Local demo</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="section-subtitle">
                    No projects added yet. Once you create some in the admin panel, they will appear here.
                </p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        Home • Portfolio of Joel Diaz
    </footer>
</main>

</body>
</html>
