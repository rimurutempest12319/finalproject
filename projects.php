<?php
require 'db.php';
$projects = $conn->query("SELECT * FROM projects ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projects • Joel Diaz</title>
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
        .nav-link.active::after { width: 100%; }

        .page {
            max-width: 1080px;
            margin: 0 auto;
            padding: 32px 20px 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 16px;
        }
        .section-title { font-size: 22px; font-weight: 600; }
        .section-subtitle { font-size: 13px; color: var(--text-muted); }

        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
        }

        /* CLICKABLE CARD */
        .project-card {
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            border: 1px solid rgba(148, 163, 184, 0.35);
            padding: 14px 14px 16px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.6);
            position: relative;
            transition: transform 0.18s ease-out, box-shadow 0.18s ease-out, border-color 0.18s ease-out;
            display: block;      /* so whole card is clickable */
            color: inherit;      /* keep text color */
        }
        .project-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.13), transparent 60%);
            opacity: 0;
            transition: opacity 0.18s ease-out;
        }
        .project-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.8);
            border-color: rgba(56, 189, 248, 0.6);
        }
        .project-card:hover::before { opacity: 1; }

        .project-image {
            width: 100%;
            border-radius: 12px;
            object-fit: cover;
            max-height: 150px;
            margin-bottom: 10px;
        }
        .project-title {
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .project-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .project-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: var(--text-muted);
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

<main class="page">
    <section>
        <div class="section-header">
            <div>
                <div class="section-title">Projects</div>
                <div class="section-subtitle">All projects pulled dynamically from the database.</div>
            </div>
        </div>

        <div class="project-grid">
            <?php if ($projects && $projects->num_rows > 0): ?>
                <?php while ($row = $projects->fetch_assoc()): ?>
                    <a href="project.php?id=<?php echo $row['id']; ?>" class="project-card">
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Project image" class="project-image">
                        <?php endif; ?>

                        <h3 class="project-title"><?php echo htmlspecialchars($row['title']); ?></h3>

                        <p class="project-desc">
                            <?php
                            $desc = strip_tags($row['description']);
                            if (strlen($desc) > 140) {
                                $desc = substr($desc, 0, 140) . '...';
                            }
                            echo htmlspecialchars($desc);
                            ?>
                        </p>

                        <div class="project-footer">
                            <span>View details →</span>
                            <?php if (!empty($row['link'])): ?>
                                <span>Has live link ↗</span>
                            <?php else: ?>
                                <span>Local demo</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="section-subtitle">No projects yet. Add them from the admin panel.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        Projects • Portfolio of Joel Diaz
    </footer>
</main>

</body>
</html>
