<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Current project
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

// Previous project (smaller id, closest)
$prev = null;
$next = null;

if ($project) {
    // Previous
    $stmtPrev = $conn->prepare("SELECT id, title FROM projects WHERE id < ? ORDER BY id DESC LIMIT 1");
    $stmtPrev->bind_param("i", $id);
    $stmtPrev->execute();
    $resPrev = $stmtPrev->get_result();
    $prev = $resPrev->fetch_assoc();
    $stmtPrev->close();

    // Next
    $stmtNext = $conn->prepare("SELECT id, title FROM projects WHERE id > ? ORDER BY id ASC LIMIT 1");
    $stmtNext->bind_param("i", $id);
    $stmtNext->execute();
    $resNext = $stmtNext->get_result();
    $next = $resNext->fetch_assoc();
    $stmtNext->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        <?php echo $project ? htmlspecialchars($project['title']) . ' • Project' : 'Project not found'; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #1f2933, #111827, #0f172a);
            --card-bg: rgba(17, 24, 39, 0.95);
            --border-subtle: rgba(148, 163, 184, 0.45);
            --accent: #38bdf8;
            --accent-soft: rgba(56, 189, 248, 0.15);
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --radius-xl: 18px;
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
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.1));
            border-bottom: 1px solid var(--border-subtle);
        }
        .nav-inner {
            max-width: 980px;
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
            transition: width .18s ease-out;
        }
        .nav-link:hover { color: var(--text-main); }
        .nav-link:hover::after { width: 100%; }

        .page {
            max-width: 980px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-subtle);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.9);
            padding: 20px 18px 22px;
        }
        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 10px;
        }
        .project-title {
            font-size: 22px;
            font-weight: 600;
        }
        .project-id {
            font-size: 11px;
            color: var(--text-muted);
        }
        .meta {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 14px;
        }
        .image-wrap {
            margin-bottom: 14px;
        }
        .image-wrap img {
            max-width: 100%;
            border-radius: 14px;
            display: block;
        }
        .desc {
            font-size: 13px;
            line-height: 1.7;
            color: var(--text-main);
            white-space: pre-line;
        }
        .link-row {
            margin-top: 16px;
            font-size: 13px;
        }
        .pill-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border-radius: 999px;
            border: 1px solid rgba(56, 189, 248, 0.7);
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 12px;
        }
        .nav-bottom-row {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 12px;
        }
        .nav-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .nav-chip {
            padding: 6px 11px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.5);
            background: rgba(15, 23, 42, 0.9);
            color: var(--text-muted);
            font-size: 12px;
        }
        .nav-chip strong {
            color: var(--text-main);
        }

        .not-found {
            font-size: 14px;
            color: var(--text-muted);
        }

        footer {
            margin-top: 26px;
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
            <a href="index.php" class="nav-link">Home</a>
            <a href="projects.php" class="nav-link">Projects</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="admin.php" class="nav-link">Admin</a>
        </div>
    </div>
</nav>

<main class="page">
    <?php if ($project): ?>
        <article class="card">
            <div class="title-row">
                <div class="project-title"><?php echo htmlspecialchars($project['title']); ?></div>
                <div class="project-id">#<?php echo $project['id']; ?></div>
            </div>

            <div class="meta">
                This project is managed from the admin panel and stored in the <code>projects</code> table.
            </div>

            <?php if (!empty($project['image'])): ?>
                <div class="image-wrap">
                    <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="Project image">
                </div>
            <?php endif; ?>

            <div class="desc">
                <?php echo nl2br(htmlspecialchars($project['description'])); ?>
            </div>

            <?php if (!empty($project['link'])): ?>
                <div class="link-row">
                    <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank" class="pill-link">
                        Open live project →
                    </a>
                </div>
            <?php endif; ?>

            <!-- Bottom navigation: Back / Previous / Next -->
            <div class="nav-bottom-row">
                <div class="nav-group">
                    <a href="projects.php" class="nav-chip">← Back to all projects</a>
                    <a href="index.php" class="nav-chip">← Back to home</a>
                </div>

                <div class="nav-group">
                    <?php if ($prev): ?>
                        <a href="project.php?id=<?php echo $prev['id']; ?>" class="nav-chip">
                            ← Previous: <strong><?php echo htmlspecialchars($prev['title']); ?></strong>
                        </a>
                    <?php endif; ?>

                    <?php if ($next): ?>
                        <a href="project.php?id=<?php echo $next['id']; ?>" class="nav-chip">
                            Next: <strong><?php echo htmlspecialchars($next['title']); ?></strong> →
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php else: ?>
        <article class="card">
            <div class="project-title">Project not found</div>
            <p class="not-found">
                The project you’re looking for doesn’t exist or was removed.<br>
                Return to the <a href="projects.php">projects page</a>.
            </p>
        </article>
    <?php endif; ?>

    <footer>
        Project details • Portfolio of Joel Diaz
    </footer>
</main>

</body>
</html>
