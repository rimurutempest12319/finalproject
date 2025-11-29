<?php
session_start();
require 'db.php';

// ---------- LOGIN ----------
$login_error = "";

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($username === "admin" && $password === "password123") {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $login_error = "Invalid username or password.";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Poppins', system-ui, sans-serif;
                background: radial-gradient(circle at top, #0f172a, #020617);
                color: #e5e7eb;
            }
            .login-card {
                background: rgba(15, 23, 42, 0.95);
                border-radius: 18px;
                padding: 24px 22px 22px;
                width: 100%;
                max-width: 360px;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.9);
                border: 1px solid rgba(148, 163, 184, 0.4);
            }
            h2 {
                margin: 0 0 4px;
                font-size: 22px;
            }
            .sub {
                font-size: 12px;
                color: #9ca3af;
                margin-bottom: 18px;
            }
            label {
                display: block;
                font-size: 12px;
                color: #9ca3af;
                margin-bottom: 4px;
            }
            input {
                width: 100%;
                padding: 9px 10px;
                border-radius: 10px;
                border: 1px solid #334155;
                background: #020617;
                color: #e5e7eb;
                margin-bottom: 10px;
                outline: none;
            }
            input:focus {
                border-color: #38bdf8;
                box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.4);
            }
            button {
                width: 100%;
                padding: 9px 10px;
                border-radius: 999px;
                border: none;
                background: linear-gradient(to right, #38bdf8, #6366f1);
                color: #020617;
                font-weight: 500;
                cursor: pointer;
                margin-top: 4px;
            }
            button:hover {
                filter: brightness(1.05);
            }
            .error {
                background: rgba(220, 38, 38, 0.18);
                border-radius: 10px;
                padding: 7px 9px;
                margin-bottom: 10px;
                font-size: 12px;
                color: #fecaca;
                border: 1px solid rgba(220, 38, 38, 0.5);
            }
        </style>
    </head>
    <body>
    <form method="post" class="login-card">
        <h2>Admin Panel</h2>
        <div class="sub">Sign in to manage projects and messages.</div>

        <?php if ($login_error): ?>
            <div class="error"><?php echo $login_error; ?></div>
        <?php endif; ?>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Login</button>
    </form>
    </body>
    </html>
    <?php
    exit;
}

// ---------- HANDLE ADD PROJECT ----------
$project_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_project'])) {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $link        = trim($_POST['link'] ?? '');
    $image_path  = trim($_POST['image'] ?? '');

    if ($title === "" || $description === "") {
        $project_msg = "Title and description are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, image, link) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $image_path, $link);
        if ($stmt->execute()) {
            $project_msg = "Project added successfully.";
        } else {
            $project_msg = "Error adding project. Please try again.";
        }
        $stmt->close();
    }
}

$projects = $conn->query("SELECT * FROM projects ORDER BY id DESC");
$messages = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: radial-gradient(circle at top, #020617, #020617 40%, #000000);
            --card-bg: rgba(15, 23, 42, 0.96);
            --border-subtle: rgba(148, 163, 184, 0.45);
            --accent: #38bdf8;
            --accent-soft: rgba(56, 189, 248, 0.12);
            --text: #e5e7eb;
            --text-muted: #9ca3af;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Poppins', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(30, 64, 175, 0.7);
            background: rgba(15, 23, 42, 0.95);
            position: sticky;
            top: 0;
            z-index: 15;
        }
        .topbar-title {
            font-size: 16px;
            font-weight: 500;
        }
        .topbar-sub {
            font-size: 11px;
            color: var(--text-muted);
        }
        .topbar-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .topbar a {
            font-size: 12px;
            color: var(--accent);
            text-decoration: none;
        }
        .btn-small {
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.6);
            background: transparent;
            color: var(--text-muted);
            font-size: 11px;
            cursor: pointer;
        }
        .btn-small:hover {
            background: rgba(15, 23, 42, 0.9);
            color: var(--text);
        }

        .page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr);
            gap: 18px;
        }
        @media (max-width: 900px) {
            .page {
                grid-template-columns: 1fr;
            }
        }

        /* Sidebar tabs */
        .sidebar {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 16px 14px;
            border: 1px solid var(--border-subtle);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.9);
            height: fit-content;
        }
        .sidebar-title {
            font-size: 13px;
            margin-bottom: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .11em;
        }
        .tab-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .tab-btn {
            border-radius: 999px;
            padding: 7px 11px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            text-align: left;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tab-btn span.badge {
            background: rgba(15, 23, 42, 0.9);
            border-radius: 999px;
            padding: 0 8px;
            font-size: 10px;
            border: 1px solid rgba(148, 163, 184, 0.6);
        }
        .tab-btn.active {
            background: var(--accent-soft);
            color: var(--accent);
            border: 1px solid rgba(56, 189, 248, 0.7);
        }

        /* Content panels */
        .panel {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 18px 18px 20px;
            border: 1px solid var(--border-subtle);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.9);
            display: none;
        }
        .panel.active { display: block; }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 14px;
        }
        .panel-title {
            font-size: 17px;
            font-weight: 500;
        }
        .panel-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }
        .status-pill {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(22, 163, 74, 0.15);
            color: #bbf7d0;
            border: 1px solid rgba(22, 163, 74, 0.7);
        }

        .form-row {
            margin-bottom: 10px;
        }
        label {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 3px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px 10px;
            border-radius: 11px;
            border: 1px solid #334155;
            background: #020617;
            color: var(--text);
            font-size: 13px;
            outline: none;
        }
        input[type="text"]:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.4);
        }
        textarea { resize: vertical; min-height: 80px; }

        .btn-primary {
            padding: 8px 16px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(to right, #38bdf8, #6366f1);
            color: #020617;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }
        .btn-primary:hover { filter: brightness(1.05); }

        .msg-banner {
            padding: 8px 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .msg-banner.info {
            background: rgba(59, 130, 246, 0.16);
            border: 1px solid rgba(59, 130, 246, 0.65);
            color: #bfdbfe;
        }
        .msg-banner.error {
            background: rgba(220, 38, 38, 0.16);
            border: 1px solid rgba(220, 38, 38, 0.7);
            color: #fecaca;
        }

        /* === TABLE WRAP FIX HERE === */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;              /* make columns fixed width */
        }
        th, td {
            border-bottom: 1px solid rgba(55, 65, 81, 0.9);
            padding: 7px 6px;
            vertical-align: top;
            white-space: normal;              /* allow wrapping */
            overflow-wrap: anywhere;          /* wrap long URLs */
            word-break: break-word;
        }
        th {
            text-align: left;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        tr:nth-child(even) td {
            background: rgba(15, 23, 42, 0.8);
        }
        .muted {
            color: var(--text-muted);
        }
        .nowrap { 
            white-space: nowrap;              /* keep short values on one line */
            overflow-wrap: normal;
            word-break: normal;
        }
        /* optional extra control for URL cells */
        .url-cell {
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        footer {
            text-align: center;
            font-size: 11px;
            color: var(--text-muted);
            margin: 14px 0 22px;
        }
    </style>
</head>
<body>

<header class="topbar">
    <div>
        <div class="topbar-title">Admin Panel</div>
        <div class="topbar-sub">Manage portfolio data stored in MySQL.</div>
    </div>
    <div class="topbar-actions">
        <a href="index.php" target="_blank">View site</a>
        <form method="post" style="margin:0;">
            <button class="btn-small" type="submit" name="logout">Log out</button>
        </form>
    </div>
</header>

<main class="page">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-title">Dashboard</div>
        <div class="tab-list">
            <button class="tab-btn active" data-panel="overview">Overview</button>
            <button class="tab-btn" data-panel="add-project">Add project</button>
            <button class="tab-btn" data-panel="projects">
                Projects
                <span class="badge"><?php echo $projects ? $projects->num_rows : 0; ?></span>
            </button>
            <button class="tab-btn" data-panel="messages">
                Messages
                <span class="badge"><?php echo $messages ? $messages->num_rows : 0; ?></span>
            </button>
        </div>
    </aside>

    <!-- Panels -->
    <section>
        <!-- Overview -->
        <section id="panel-overview" class="panel active">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Overview</div>
                    <div class="panel-subtitle">
                        Quick glance at current content in your database.
                    </div>
                </div>
                <span class="status-pill">Connected to MySQL</span>
            </div>

            <div class="form-row">
                <div class="panel-subtitle">
                    • <strong><?php echo $projects ? $projects->num_rows : 0; ?></strong> projects published<br>
                    • <strong><?php echo $messages ? $messages->num_rows : 0; ?></strong> contact messages stored
                </div>
            </div>
            <div class="panel-subtitle" style="margin-top:10px;">
                Use the tabs on the left to add new projects, review all projects or read incoming messages.
            </div>
        </section>

        <!-- Add Project -->
        <section id="panel-add-project" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Add new project</div>
                    <div class="panel-subtitle">Fields will appear immediately on the public portfolio page.</div>
                </div>
            </div>

            <?php if ($project_msg): ?>
                <div class="msg-banner info"><?php echo htmlspecialchars($project_msg); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-row">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-row">
                    <label>Description</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>

                <div class="form-row">
                    <label>Image URL <span class="muted">(optional)</span></label>
                    <input type="text" name="image" placeholder="e.g. /images/project1.jpg">
                </div>

                <div class="form-row">
                    <label>Project link <span class="muted">(optional)</span></label>
                    <input type="text" name="link" placeholder="https://example.com">
                </div>

                <button type="submit" name="add_project" class="btn-primary">Save project</button>
            </form>
        </section>

        <!-- Projects -->
        <section id="panel-projects" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Projects</div>
                    <div class="panel-subtitle">List of all entries stored in the <code>projects</code> table.</div>
                </div>
            </div>

            <?php if ($projects && $projects->num_rows > 0): ?>
                <table>
                    <thead>
                    <tr>
                        <th class="nowrap">ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Link</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $projects->fetch_assoc()): ?>
                        <tr>
                            <td class="nowrap muted"><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="muted"><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                            <!-- URL cells will wrap nicely -->
                            <td class="muted url-cell"><?php echo htmlspecialchars($row['image']); ?></td>
                            <td class="muted url-cell"><?php echo htmlspecialchars($row['link']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="msg-banner info">
                    No projects yet. Use the “Add project” tab to create your first one.
                </div>
            <?php endif; ?>
        </section>

        <!-- Messages -->
        <section id="panel-messages" class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Contact messages</div>
                    <div class="panel-subtitle">
                        Messages submitted from the public contact form on your portfolio page.
                    </div>
                </div>
            </div>

            <?php if ($messages && $messages->num_rows > 0): ?>
                <table>
                    <thead>
                    <tr>
                        <th class="nowrap">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th class="nowrap">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $messages->fetch_assoc()): ?>
                        <tr>
                            <td class="nowrap muted"><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="muted url-cell"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="muted"><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                            <td class="nowrap muted"><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="msg-banner info">
                    No messages yet. When someone submits the contact form, their message will show up here.
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

<footer>
    Admin interface styled with custom CSS · PHP + MySQL
</footer>

<script>
    // Simple tab system
    const tabButtons = document.querySelectorAll('.tab-btn');
    const panels = {
        'overview': document.getElementById('panel-overview'),
        'add-project': document.getElementById('panel-add-project'),
        'projects': document.getElementById('panel-projects'),
        'messages': document.getElementById('panel-messages'),
    };

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-panel');

            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            Object.keys(panels).forEach(key => {
                panels[key].classList.toggle('active', key === target);
            });
        });
    });
</script>

</body>
</html>
