<header class="header">
    <a href="/" class="logo-wrapper">
        <img src="/img/logo.png" class="logo" alt="OrgaNiss">
        <span class="titleBesideLogo">ORGANISS</span>
        <span class="brand-tag">The Heedful System</span>
    </a>
    <nav class="menu">
        <a href="/" class="menu-link">Sign in</a>
        <a href="/user/register" class="menu-link menu-link--primary">Register</a>
    </nav>
</header>
<style>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap");

.header {
<<<<<<< HEAD
    width: 100%;
    background: #0f0f0f;
=======
    background: #0f172a;
>>>>>>> 24ced267ae5b431263b838fd8c840853d2d9738f
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    height: 4rem;
    border-bottom: 1px solid #2b2b2b;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: #ffffff;
}

.logo {
    width: 2.5rem;
    height: 2.5rem;
    object-fit: contain;
}

.titleBesideLogo {
    font-family: "Space Grotesk", "Inter", system-ui, sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 0.05em;
}

.brand-tag {
    font-family: "Inter", system-ui, sans-serif;
    font-size: 0.7rem;
    color: #0f0f0f;
    background: #ffc107;
    padding: 0.2rem 0.45rem;
    border-radius: 999px;
    font-weight: 600;
    letter-spacing: 0.03em;
}

.menu {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.menu-link {
    padding: 0.5rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    font-family: "Inter", system-ui, sans-serif;
    text-decoration: none;
    color: #ffffff;
    background: #1a1a1a;
    border: 1px solid #424242;
    border-radius: 8px;
    transition: background 150ms ease, border-color 150ms ease;
}

.menu-link:hover {
    background: #2b2b2b;
    border-color: #ffc107;
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
    .brand-tag {
        display: none;
    }
}
</style>
