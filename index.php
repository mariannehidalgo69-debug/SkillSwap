
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap | Learn. Share. Grow.</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f8f9ff;
            color: #20243a;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 8%;
            background: white;
            box-shadow: 0 2px 10px #00000010;
        }

        .logo {
            color: #635bdb;
            font-size: 27px;
            font-weight: bold;
        }

        nav a {
            text-decoration: none;
            color: #333;
            margin-left: 22px;
            font-weight: bold;
        }

        nav a:hover {
            color: #635bdb;
        }

        .hero {
            text-align: center;
            padding: 110px 20px;
            background: linear-gradient(135deg, #eeedff, #e5f3ff);
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero h1 span {
            color: #635bdb;
        }

        .hero p {
            max-width: 620px;
            margin: 0 auto 30px;
            line-height: 1.7;
            color: #62677a;
        }

        .btn {
            display: inline-block;
            padding: 14px 25px;
            margin: 6px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .primary {
            background: #635bdb;
            color: white;
        }

        .secondary {
            background: white;
            color: #635bdb;
            border: 1px solid #635bdb;
        }

        .section {
            padding: 65px 8%;
            text-align: center;
        }

        .section h2 {
            font-size: 30px;
            margin-bottom: 15px;
        }

        .skills {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 22px;
            margin-top: 35px;
        }

        .skill-card {
            background: white;
            padding: 30px 20px;
            border-radius: 14px;
            box-shadow: 0 4px 15px #0000000d;
        }

        .skill-card .emoji {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .skill-card h3 {
            margin-bottom: 10px;
        }

        .skill-card p {
            color: #777;
            line-height: 1.5;
        }

        footer {
            background: #20243a;
            color: white;
            text-align: center;
            padding: 25px;
        }

        @media (max-width: 650px) {
            header {
                flex-direction: column;
                gap: 18px;
            }

            nav a {
                margin: 0 7px;
                font-size: 14px;
            }

            .hero h1 {
                font-size: 35px;
            }
        }
    </style>
</head>

<body>

<header>
    <div class="logo">SkillSwap.</div>

    <nav>
        <a href="index.php">Home</a>
        <a href="#skills">Explore Skills</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<section class="hero">
    <h1>Share Your Skills.<br><span>Learn Something New.</span></h1>

    <p>
        Welcome to SkillSwap! Connect with fellow students,
        share your talents, learn new skills, and grow together
        through knowledge exchange.
    </p>

    <a href="#skills" class="btn primary">Explore Skills</a>
    <a href="register.php" class="btn secondary">Join SkillSwap</a>
</section>

<section class="section" id="skills">
    <h2>Discover What You Can Learn</h2>
    <p>Explore skills shared by fellow students and mentors.</p>

    <div class="skills">

        <div class="skill-card">
            <div class="emoji">💻</div>
            <h3>Programming</h3>
            <p>Learn coding, web development, and computer skills.</p>
        </div>

        <div class="skill-card">
            <div class="emoji">🎨</div>
            <h3>Graphic Design</h3>
            <p>Discover creativity through digital design and art.</p>
        </div>

        <div class="skill-card">
            <div class="emoji">📚</div>
            <h3>Tutoring</h3>
            <p>Improve your knowledge through academic learning.</p>
        </div>

        <div class="skill-card">
            <div class="emoji">📷</div>
            <h3>Photography</h3>
            <p>Learn photography techniques and visual storytelling.</p>
        </div>

    </div>
</section>

<footer>
    <p>© 2026 SkillSwap | Learn. Share. Grow.</p>
    <p>Student Skill Exchange Platform</p>
</footer>

</body>
</html>
