## OrgaNiss Design Guide

This guide documents the visual and interaction design rules for OrgaNiss so that new features look and feel consistent with the existing app.

---

## 1. Layout & Structure

- **Page shell**
  - Left sidebar: navigation (`Dashboard`, `My Task`, `Settings`, `Help`, `AI Weekly Planner`).
  - Right content area: page-specific content (dashboard cards, task list, calendar, weekly planner).
  - Maintain a clear visual separation between sidebar and content using background color and spacing.

- **Containers**
  - Use white cards (`bg-white`) with rounded corners (`rounded-xl`) and subtle shadows (`shadow-md`) on a light gray background (`bg-gray-100`).
  - Default horizontal padding: `px-4` or `px-6`; vertical padding: `py-4`.
  - Use `max-w-6xl mx-auto` (or similar) for full-page tools such as the weekly planner.

- **Grids**
  - Use Tailwind’s responsive grid utilities:
    - Single column on mobile (`grid-cols-1`).
    - 2–4 columns on tablet/desktop (`md:grid-cols-2`, `lg:grid-cols-4` etc.).
  - Avoid fixed pixel widths where possible; prefer responsive classes.

---

## 2. Typography

- **Fonts**
  - Primary display font: `"Righteous", sans-serif` (already in `components/task.php`).
  - For long-form text or dense content, prefer system sans-serif or an existing project font to maintain readability.

- **Hierarchy**
  - Page titles: `text-2xl font-bold`.
  - Section headings / card titles: `text-lg font-semibold`.
  - Body text: `text-sm` or `text-base` with `text-gray-700`.
  - Muted captions / labels: `text-xs text-gray-500`.

- **Line-length and spacing**
  - Keep line length to ~60–80 characters; use `max-w-*` or container widths to avoid overly wide text.
  - Use vertical spacing between text blocks (`mt-2`, `mt-4`) to make sections scannable.

---

## 3. Color & Theming

- **Backgrounds**
  - Page background: `bg-gray-100`.
  - Cards: `bg-white`.
  - Sub-panels (e.g., subtasks, secondary sections): `bg-gray-50`.

- **Text and labels**
  - Primary text: `text-gray-800` or `text-black`.
  - Secondary text / helper copy: `text-gray-600`.
  - Muted / labels: `text-gray-500`.

- **Accent colors**
  - Primary actions: blue family (`bg-blue-600 hover:bg-blue-700`, `text-blue-600` for links).
  - Success / positive metrics: green (`text-green-700`).
  - Warnings / streaks / emphasis: orange (`text-orange-600`).
  - Errors and deadlines: red (`text-red-800`).

- **Shadows & borders**
  - Use subtle shadows (`shadow`, `shadow-md`) and light borders (`border border-gray-200`) to separate elements instead of thick outlines.

---

## 4. Components & Patterns

### 4.1 Cards

- Use for metrics, quick links, and grouped information.
- Structure:
  - Label at top in muted style (`text-xs text-gray-500`).
  - Primary value or content below in larger text (`text-xl font-semibold`).
  - Optional action (e.g., `Open`, `View`) aligned to the right in `text-xs text-blue-600 hover:underline`.

### 4.2 Task Lists

- **Parent tasks**
  - Display as rows with:
    - Optional collapse-toggle icon (`▶` / `▼`) aligned left when subtasks exist.
    - Title, details, due date, and priority rendered in columns or flex children.
  - Overdue or due-today tasks:
    - Due date text: `text-red-800`.
  - Completed tasks:
    - Title styled as `line-through text-gray-500`.

- **Subtasks**
  - Shown indented under parent in a soft background (`bg-gray-50`) with bullet-like indicator (`•` text prefix).
  - Hide by default; show on parent toggle.
  - Subtask completion uses checkboxes; completed subtasks also use `line-through text-gray-500`.

### 4.3 Weekly Planner Board

- Layout
  - 7 columns in desktop (`lg:grid-cols-7`), fewer columns or stacked on smaller breakpoints (`md:grid-cols-4`, `grid-cols-1`).
  - Each column is a card with:
    - Day name as header (`font-semibold mb-2`).
    - List of tasks rendered as pill-like items (`px-2 py-1 bg-white rounded border border-gray-200`).
    - When no tasks: small muted label (`text-xs text-gray-400` with “No tasks”).

- Interactions
  - Keep plan-generation as a single clear primary button: “Generate Weekly Plan with AI”.
  - Apply-plan action is secondary, right-aligned, but visually strong (green button).

---

## 5. Interaction & UX Rules

- **Feedback**
  - Every action that changes data should:
    - Show a loading state for longer operations (e.g., AI calls).
    - Show a success, info, or error message on completion (SweetAlert is the existing pattern).

- **AI-related actions**
  - Always treat AI enhancements as optional:
    - Never block core flows (like creating a task) on AI availability.
    - If AI fails, show a neutral “Task created; AI subtasks not generated” style info instead of an error.

- **Checkboxes**
  - For completion:
    - Ensure click targets include both checkbox and label.
    - Update visual state immediately; persist to backend in the background.
  - For bulk actions (e.g., delete), require explicit confirmation.

- **Modals (SweetAlert)**
  - Use consistent ordering of inputs: title → description → date/time → priority → optional toggles (AI, etc.).
  - Validate required inputs inside `preConfirm` with clear, concise messages.

---

## 6. Responsiveness & Accessibility

- **Breakpoints**
  - Optimise for:
    - Mobile: stacked layout, `grid-cols-1`.
    - Tablet: 2–4 columns for cards/boards.
    - Desktop: full multi-column layout.

- **Keyboard & focus**
  - Use native buttons and links for interactive elements (no click-only `div`).
  - Ensure focusable items (buttons, links, inputs) remain reachable when adding new components.

- **Contrast**
  - Maintain at least WCAG AA contrast for text:
    - Dark text on light backgrounds.
    - Buttons with sufficient color difference between text and background.

---

## 7. When Adding New UI

When you add a new feature or screen:

- Reuse existing patterns:
  - Sidebar navigation.
  - Card layout for metrics and tools.
  - SweetAlert for task creation/edit flows.
- Follow existing color and typography tokens instead of introducing new colors or fonts.
- Consider:
  - Where this fits in the sidebar.
  - Which existing card layout it should mimic (dashboard, weekly planner, or task list).

