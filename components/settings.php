<div class="settings-page">
    <div class="settings-card">
        <div class="settings-header">
            <div>
                <h2>Settings</h2>
                <p class="settings-lead">Manage your app preferences and workspace behavior.</p>
            </div>
            <span class="settings-badge">Dark-first</span>
        </div>

        <div class="settings-overview">
            <div class="settings-overview-card">
                <span class="settings-overview-label">Theme</span>
                <strong id="settingsThemeSummary">Heedful Dark</strong>
                <p>Yellow accents, compact panels, and high contrast surfaces.</p>
            </div>
            <div class="settings-overview-card">
                <span class="settings-overview-label">Planning cadence</span>
                <strong>Weekly AI</strong>
                <p>Demo content is available even when your task list is empty.</p>
            </div>
            <div class="settings-overview-card">
                <span class="settings-overview-label">Email updates</span>
                <strong id="settingsEmailSummary">Daily digest</strong>
                <p>Use this setting to control reminders and digest frequency.</p>
            </div>
        </div>

        <div class="settings-group">
            <div class="settings-row">
                <div>
                    <h3>Interface Theme</h3>
                    <p>Keep the dashboard aligned with The Heedful dark-first style.</p>
                </div>
                <select class="settings-control settings-select" data-setting="interfaceTheme">
                    <option value="heedful-dark" selected>Heedful Dark</option>
                    <option value="deep-surface">Deep Surface</option>
                    <option value="high-contrast">High Contrast</option>
                </select>
            </div>

            <div class="settings-row">
                <div>
                    <h3>Sidebar Density</h3>
                    <p>Choose a compact or comfortable navigation layout.</p>
                </div>
                <select class="settings-control settings-select" data-setting="sidebarDensity">
                    <option value="compact">Compact</option>
                    <option value="comfortable" selected>Comfortable</option>
                </select>
            </div>

            <div class="settings-row">
                <div>
                    <h3>Task Due Alerts</h3>
                    <p>Show warnings before due dates inside dashboard views.</p>
                </div>
                <label class="settings-switch">
                    <input type="checkbox" checked data-setting="taskDueAlerts">
                    <span>Enabled</span>
                </label>
            </div>

            <div class="settings-row">
                <div>
                    <h3>Email Updates</h3>
                    <p>Control reminders and activity summaries sent to your inbox.</p>
                </div>
                <select class="settings-control settings-select" data-setting="emailUpdates">
                    <option value="off">Off</option>
                    <option value="daily" selected>Daily digest</option>
                    <option value="weekly">Weekly summary</option>
                </select>
            </div>

            <div class="settings-row">
                <div>
                    <h3>AI Planning</h3>
                    <p>Configure how weekly plans are generated from your tasks.</p>
                </div>
                <a href="/dashboard?tab=weeklyPlanner" class="settings-link">Open Planner</a>
            </div>

            <div class="settings-row">
                <div>
                    <h3>Focus Hours</h3>
                    <p>Block time for deep work before notifications are softened.</p>
                </div>
                <select class="settings-control settings-select" data-setting="focusHours">
                    <option value="8-12" selected>8:00 AM - 12:00 PM</option>
                    <option value="9-1">9:00 AM - 1:00 PM</option>
                    <option value="10-2">10:00 AM - 2:00 PM</option>
                    <option value="custom">Custom</option>
                </select>
            </div>

            <div class="settings-row settings-row--hidden" id="focusHoursCustomRow">
                <div>
                    <h3>Custom Focus Hours</h3>
                    <p>Choose your own start and end times for focus mode.</p>
                </div>
                <div class="settings-custom-time-grid">
                    <input type="time" class="settings-control" id="focusHoursStart" value="08:00" aria-label="Focus hours start time">
                    <input type="time" class="settings-control" id="focusHoursEnd" value="12:00" aria-label="Focus hours end time">
                </div>
            </div>

            <div class="settings-row">
                <div>
                    <h3>Weekend Mode</h3>
                    <p>Scale down suggestions on Saturday and Sunday.</p>
                </div>
                <label class="settings-switch">
                    <input type="checkbox" checked data-setting="weekendMode">
                    <span>Light workload</span>
                </label>
            </div>

            <!-- Language is intentionally disabled for now until full localization is implemented. -->
            <!-- <div class="settings-row">
                <div>
                    <h3>Language</h3>
                    <p>Choose the primary language used across the workspace.</p>
                </div>
                <select class="settings-control settings-select" data-setting="language">
                    <option value="en" selected>English</option>
                    <option value="fil">Filipino</option>
                </select>
            </div> -->

            <div class="settings-row">
                <div>
                    <h3>Session Timeout</h3>
                    <p>Auto-lock your workspace after inactivity.</p>
                </div>
                <select class="settings-control settings-select" data-setting="sessionTimeout">
                    <option value="15">15 min</option>
                    <option value="30" selected>30 min</option>
                    <option value="60">60 min</option>
                </select>
            </div>

            <div class="settings-row">
                <div>
                    <h3>Data Export</h3>
                    <p>Download your tasks and profile data when you need a backup.</p>
                </div>
                <a href="/dashboard?tab=profile" class="settings-link">Profile Tools</a>
            </div>
        </div>

        <div class="settings-footnote" id="settingsSaveState">Preferences stay local in this demo build so you can test the interface without risking your account data.</div>
    </div>
</div>

<style>
<?php include "../css/settings.css"; ?>
</style>

<script>
(() => {
    const storageKey = "th.dashboard.preferences.v1";
    const defaults = {
        interfaceTheme: "heedful-dark",
        sidebarDensity: "comfortable",
        taskDueAlerts: true,
        emailUpdates: "daily",
        focusHours: "8-12",
        weekendMode: true,
        sessionTimeout: "30"
    };

    const controls = Array.from(document.querySelectorAll("[data-setting]"));
    const customRow = document.getElementById("focusHoursCustomRow");
    const customStart = document.getElementById("focusHoursStart");
    const customEnd = document.getElementById("focusHoursEnd");
    const saveState = document.getElementById("settingsSaveState");
    const themeSummary = document.getElementById("settingsThemeSummary");
    const emailSummary = document.getElementById("settingsEmailSummary");

    let preferences = { ...defaults };

    try {
        const raw = localStorage.getItem(storageKey);
        if (raw) {
            preferences = { ...defaults, ...JSON.parse(raw) };
        }
    } catch (_error) {
        preferences = { ...defaults };
    }

    function humanTheme(value) {
        if (value === "deep-surface") return "Deep Surface";
        if (value === "high-contrast") return "High Contrast";
        return "Heedful Dark";
    }

    function humanEmail(value) {
        if (value === "off") return "Off";
        if (value === "weekly") return "Weekly summary";
        return "Daily digest";
    }

    function applyPreferences() {
        document.documentElement.dataset.interfaceTheme = preferences.interfaceTheme;
        document.documentElement.dataset.sidebarDensity = preferences.sidebarDensity;
        document.documentElement.dataset.emailUpdates = preferences.emailUpdates;
        document.documentElement.dataset.weekendMode = preferences.weekendMode ? "on" : "off";
        document.documentElement.dataset.sessionTimeout = String(preferences.sessionTimeout || "30");
        document.documentElement.dataset.taskDueAlerts = preferences.taskDueAlerts ? "on" : "off";

        if (themeSummary) {
            themeSummary.textContent = humanTheme(preferences.interfaceTheme);
        }
        if (emailSummary) {
            emailSummary.textContent = humanEmail(preferences.emailUpdates);
        }

        const isCustom = String(preferences.focusHours).startsWith("custom:");
        if (customRow) {
            customRow.classList.toggle("settings-row--hidden", !isCustom);
        }
    }

    function updateControlValues() {
        controls.forEach((control) => {
            const key = control.getAttribute("data-setting");
            if (!key || !(key in preferences)) {
                return;
            }

            if (control.type === "checkbox") {
                control.checked = Boolean(preferences[key]);
                return;
            }

            if (key === "focusHours" && String(preferences.focusHours).startsWith("custom:")) {
                control.value = "custom";
                const custom = String(preferences.focusHours).replace("custom:", "");
                const [start, end] = custom.split("-");
                if (start && customStart) customStart.value = start;
                if (end && customEnd) customEnd.value = end;
                return;
            }

            control.value = String(preferences[key]);
        });
    }

    function savePreferences(message) {
        localStorage.setItem(storageKey, JSON.stringify(preferences));
        applyPreferences();
        window.dispatchEvent(new CustomEvent("th-preferences-updated", { detail: preferences }));
        if (saveState) {
            saveState.textContent = message;
        }
    }

    controls.forEach((control) => {
        control.addEventListener("change", () => {
            const key = control.getAttribute("data-setting");
            if (!key) {
                return;
            }

            if (control.type === "checkbox") {
                preferences[key] = control.checked;
            } else if (key === "focusHours") {
                if (control.value === "custom") {
                    preferences.focusHours = `custom:${customStart.value}-${customEnd.value}`;
                } else {
                    preferences.focusHours = control.value;
                }
            } else {
                preferences[key] = control.value;
            }

            savePreferences("Preferences saved locally. Changes are now active.");
        });
    });

    [customStart, customEnd].forEach((timeInput) => {
        if (!timeInput) {
            return;
        }
        timeInput.addEventListener("change", () => {
            preferences.focusHours = `custom:${customStart.value}-${customEnd.value}`;
            savePreferences(`Focus hours set to ${customStart.value} - ${customEnd.value}.`);
        });
    });

    updateControlValues();
    applyPreferences();
})();
</script>