<div class="header header--light">
    <a href="/" class="logo-wrapper">
        <img src="/img/logo.png" class="logo" alt="OrgaNiss">
        <span class="brand-copy">
            <span class="titleBesideLogo">ORGANISS</span>
            <span class="brand-tag">The Heedful System</span>
        </span>
    </a>
    <nav class="menu" aria-label="Primary">
        <a href="/" class="menu-link menu-link--light">Sign in</a>
        <a href="/user/register.php" class="menu-link menu-link--primary">Register</a>
    </nav>
</div>
<style>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap");

.header {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.9rem 1.5rem;
}

.header--light {
    background: linear-gradient(180deg, #ffffff 0%, #f7f7f7 100%);
    border-bottom: 1px solid #e2e2e2;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
}

.logo-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: #111111;
}

.logo {
    width: 2.6rem;
    height: 2.6rem;
    object-fit: contain;
}

.brand-copy {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.titleBesideLogo {
    font-family: "Space Grotesk", "Inter", system-ui, sans-serif;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: 0.08em;
}

.brand-tag {
    width: fit-content;
    font-family: "Inter", system-ui, sans-serif;
    font-size: 0.7rem;
    color: #0f0f0f;
    background: #ffc107;
    padding: 0.2rem 0.5rem;
    border-radius: 999px;
    font-weight: 700;
    letter-spacing: 0.03em;
}

.menu {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.menu-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 1.15rem;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: "Inter", system-ui, sans-serif;
    text-decoration: none;
    color: #111111;
    background: #ffffff;
    border: 1px solid #d7d7d7;
    border-radius: 999px;
    transition: background 150ms ease, border-color 150ms ease, transform 150ms ease;
}

.menu-link:hover {
    background: #f0f0f0;
    border-color: #c8c8c8;
    transform: translateY(-1px);
}

.menu-link--primary {
    background: #ffc107;
    color: #0f0f0f;
    border-color: #ffc107;
}

.menu-link--primary:hover {
    background: #ffb300;
    border-color: #ffb300;
}

@media (max-width: 720px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
    }

    .brand-tag {
        display: none;
    }
}
</style>