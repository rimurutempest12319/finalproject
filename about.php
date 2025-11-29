<?php
require 'db.php';

$contact_success = "";
$contact_error   = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === "" || $email === "" || $message === "") {
        $contact_error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            $contact_success = "Message sent successfully! I’ll get back to you soon.";
        } else {
            $contact_error = "Error saving message. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About • Joel Diaz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #1f2933, #111827, #0f172a);
            --card-bg: rgba(17, 24, 39, 0.9);
            --border-subtle: rgba(148, 163, 184, 0.4);
            --accent: #38bdf8;
            --accent-soft: rgba(56, 189, 248, 0.2);
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
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.1));
            border-bottom: 1px solid var(--border-subtle);
        }
        .nav-inner {
            max-width: 900px;
            margin: 0 auto;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo { font-weight: 600; letter-spacing: .08em; font-size: 14px; text-transform: uppercase; }
        .nav-links { display: flex; gap: 18px; font-size: 14px; }
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
        .nav-link.active { color: var(--text-main); }
        .nav-link.active::after { width: 100%; }

        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 20px 40px;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.2fr);
            gap: 24px;
        }
        @media (max-width: 840px) { .layout { grid-template-columns: 1fr; } }

        .about-main {
            background: var(--card-bg);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-subtle);
            padding: 20px 18px 22px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.85);
        }
        .title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }
        .paragraph {
            font-size: 13px;
            color: var(--text-main);
            line-height: 1.7;
            margin-bottom: 12px;
        }
        .small-heading {
            margin-top: 16px;
            font-size: 13px;
            font-weight: 500;
        }
        ul {
            padding-left: 16px;
            margin-top: 6px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .about-side {
            background: rgba(17, 24, 39, 0.92);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-subtle);
            padding: 18px 16px 20px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.85);
            text-align: center;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 999px;
            object-fit: cover;
            border: 3px solid rgba(56, 189, 248, 0.8);
            margin-bottom: 10px;
        }
        .label {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: .11em;
        }
        .info {
            font-size: 13px;
            margin-bottom: 6px;
        }

        .contact-card {
            margin-top: 26px;
            background: rgba(15, 23, 42, 0.96);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-subtle);
            padding: 18px 18px 20px;
        }
        .contact-title {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 6px;
        }
        .contact-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .form-row { margin-bottom: 10px; }
        label {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        input, textarea {
            width: 100%;
            background: rgba(15, 23, 42, 0.85);
            border-radius: 11px;
            border: 1px solid rgba(55, 65, 81, 0.9);
            padding: 9px 11px;
            font-size: 13px;
            color: var(--text-main);
            outline: none;
            transition: border-color .18s, box-shadow .18s, background .18s, transform .08s;
        }
        input::placeholder, textarea::placeholder { color: #4b5563; }
        input:focus, textarea:focus {
            border-color: rgba(56, 189, 248, 0.9);
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.4);
            background: rgba(15, 23, 42, 0.96);
            transform: translateY(-1px);
        }
        textarea { resize: vertical; min-height: 90px; }

        .btn {
            border-radius: 999px;
            border: none;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            background: radial-gradient(circle at top left, #38bdf8, #0ea5e9);
            color: #0b1120;
            box-shadow: 0 14px 35px rgba(56, 189, 248, 0.35);
        }
        .btn:hover {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 18px 42px rgba(56, 189, 248, 0.45);
        }

        .message {
            padding: 8px 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .success { background: rgba(22, 163, 74, 0.25); border: 1px solid rgba(22, 163, 74, 0.9); color: #bbf7d0; }
        .error   { background: rgba(220, 38, 38, 0.25); border: 1px solid rgba(220, 38, 38, 0.9); color: #fecaca; }

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
    <section class="layout">
        <article class="about-main">
            <div class="title">About Joel</div>
            <div class="sub">BSCS student · Tumana, Santa Maria, Bulacan</div>

            <p class="paragraph">
                I’m <b>Joel Diaz</b>, an aspiring developer who enjoys building interactive systems, games,
                and web applications. I like turning ideas into working projects that people can actually use.
            </p>

            <p class="paragraph">
                I explore technologies like <b>PHP</b>, <b>MySQL</b>, and <b>Unity</b>.
                I’m especially interested in projects related to education and everyday life problems
                such as ordering systems, portfolio sites, and mobile games.
            </p>

            <p class="paragraph">
                When I’m not coding, I’m usually experimenting with designs, improving UI/UX,
                or learning new tools that make apps smoother and more enjoyable to use.
            </p>

            <div class="small-heading">Skills & Interests</div>
            <ul>
                <li>Web development with PHP &amp; MySQL</li>
                <li>Game development with Unity (2D / Android)</li>
                <li>Portfolio and CRUD-based systems</li>
                <li>Modern, aesthetic interface design</li>
            </ul>
        </article>

        <aside class="about-side">
            <img src="joel.jpg" alt="Photo of Joel Diaz" class="avatar">
            <div class="label">Profile</div>
            <div class="info"><b>Joel Diaz</b></div>
            <div class="info">BSCS Student</div>
            <div class="info" style="color:var(--text-muted);">
                Tumana, Santa Maria, Bulacan
            </div>
            <div class="info" style="margin-top:8px;font-size:12px;">
                Email: <a href="mailto:joeldiazjr6@gmail.com">joeldiazjr6@gmail.com</a>
            </div>
            <!-- SOCIAL LINKS UNDER EMAIL -->

<div class="info" style="margin-top:6px;font-size:12px;">
    Facebook:
    <a href="https://www.facebook.com/joshen0426" target="_blank">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#38bdf8"
             style="vertical-align:middle;margin-right:3px;">
            <path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2c0-2 1.2-3.2 3-3.2.9 0 1.8.1 1.8.1v2h-1
            c-1 0-1.3.6-1.3 1.2v1.9h2.6l-.4 3h-2.2v7A10 10 0 0 0 22 12"></path>
        </svg>
        joshen0426
    </a>
</div>

<div class="info" style="margin-top:4px;font-size:12px;">
    Instagram:
    <a href="https://www.instagram.com/joestar_99z/" target="_blank">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#e879f9" 
             style="vertical-align:middle;margin-right:3px;">
            <path d="M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2
            5-5V7c0-2.8-2.2-5-5-5H7zm10 2c1.7 0 3 1.3 3 3v10c0 1.7-1.3
            3-3 3H7c-1.7 0-3-1.3-3-3V7c0-1.7 1.3-3 3-3h10zm-5 
            3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 
            2a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm4.8-.9a1.1 1.1 
            0 1 0 0-2.2 1.1 1.1 0 0 0 0 2.2z"/>
        </svg>
        @joestar_99z
    </a>
</div>

<div class="info" style="margin-top:4px;font-size:12px;">
    X:
    <a href="https://x.com/jo3717932171247" target="_blank">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f8fafc"
             style="vertical-align:middle;margin-right:3px;">
            <path d="M22.25 0h-5.02L12 7.6 6.77 0H1.75l7.45 
            10.1L0 24h5.04l6.06-8.3 5.82 8.3H24L14.73 
            10.1 22.25 0z"/>
        </svg>
        @jo3717932171247
    </a>
</div>

<div class="info" style="margin-top:4px;font-size:12px;">
    GitHub:
    <a href="https://github.com/rimurutempest12319" target="_blank">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#94a3b8"
             style="vertical-align:middle;margin-right:3px;">
            <path d="M12 0a12 12 0 0 0-3.8 23.4c.6.1.8-.2.8-.5v-2c-3.3.7-4-1.6-4-1.6-.5-1.3-1.2-1.7-1.2-1.7-1-.6.1-.6.1-.6 
            1 .1 1.6 1.1 1.6 1.1.9 1.6 2.5 1.1 3.1.8a2.3 2.3 0 0 1 
            .7-1.5c-2.6-.3-5.4-1.3-5.4-5.9 0-1.3.4-2.3 
            1.1-3.2a4 4 0 0 1 .1-3.1s1-.3 3.2 
            1.1c1-.3 2-.4 3-.4s2 .1 3 .4c2.2-1.4 
            3.2-1.1 3.2-1.1a4 4 0 0 1 .1 
            3.1c.7.9 1.1 2 1.1 3.2 0 4.6-2.8 
            5.6-5.5 5.9a2.5 2.5 0 0 1 .7 
            1.9v2.9c0 .3.2.6.8.5A12 12 0 0 0 12 0"/>
        </svg>
        rimurutempest12319
    </a>
</div>

        </aside>
    </section>

    <!-- CONTACT SECTION AT BOTTOM -->
    <section class="contact-card">
        <div class="contact-title">Contact Joel</div>
        <div class="contact-sub">
            Send a message using this form. Your message is saved in the <code>messages</code> table and can be viewed from the admin panel.
        </div>

        <?php if ($contact_success): ?>
            <div class="message success"><?php echo htmlspecialchars($contact_success); ?></div>
        <?php endif; ?>
        <?php if ($contact_error): ?>
            <div class="message error"><?php echo htmlspecialchars($contact_error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-row">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" placeholder="Your name" required>
            </div>
            <div class="form-row">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="you@example.com" required>
            </div>
            <div class="form-row">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Tell me about your project or question..." required></textarea>
            </div>
            <button type="submit" class="btn" name="contact_submit">Send message</button>
        </form>
    </section>

    <footer>
        About • Portfolio of Joel Diaz
    </footer>
</main>

</body>
</html>
