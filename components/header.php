<header class="header">
    <a href="/" class="logo-wrapper">
        <img src="/img/logo.png" class="logo" alt="OrgaNiss">
        <span class="titleBesideLogo">ORGANISS</span>
    </a>
    <nav class="menu">
        <a href="/" class="menu-link">Sign in</a>
        <a href="/user/register" class="menu-link menu-link--primary">Register</a>
    </nav>
</header>
<style>
.header {
    background: #0f172a;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    height: 4rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: white;
}

.logo {
    width: 2.5rem;
    height: 2.5rem;
    object-fit: contain;
}

.titleBesideLogo {
    font-family: "Righteous", system-ui, sans-serif;
    font-size: 1.25rem;
    font-weight: 500;
    letter-spacing: 0.05em;
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
    font-family: "Outfit", system-ui, sans-serif;
    text-decoration: none;
    color: #0f172a;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: background 150ms ease, border-color 150ms ease;
}

.menu-link:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.menu-link--primary {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.menu-link--primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}
</style>
