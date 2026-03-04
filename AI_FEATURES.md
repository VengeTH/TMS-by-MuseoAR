## OrgaNiss AI Features Guide

This document explains how the AI-powered features (Gemini-based Task Breakdown and Weekly Planner) are wired into OrgaNiss and how to work with or extend them.

---

## 1. Overview

Current AI features:

- **AI Task Breakdown**
  - Automatically generates subtasks for a newly created task.
  - Uses Google Gemini (`gemini-1.5-flash`) to propose 4–8 actionable steps.
  - Subtasks are stored as child tasks (`parent_task_id`).

- **AI Weekly Planner**
  - Takes a set of selected tasks and generates a weekly distribution (Monday–Sunday).
  - Lets the user apply the plan to task deadlines in one click.

AI is treated as an enhancement: the core task system continues to work even if the AI service is unavailable or the daily limit is exceeded.

---

## 2. Environment & Configuration

### 2.1 Environment variables

Defined in `.env` (see `.env.example`):

- `GEMINI_API_KEY`  
  Google Gemini API key (from Google AI Studio).

The loader `helpers/env.php` uses `vlucas/phpdotenv` and now reads `.env` from the project root.

### 2.2 Gemini API

- Endpoint:
  - `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=GEMINI_API_KEY`
- Request body:
  - JSON with `contents[0].parts[0].text` containing the prompt.
- Transport:
  - Plain cURL via `callGemini(string $url, array $payload): ?string`.

---

## 3. Database Changes

These columns/tables support AI and completion logic:

- `tasks` table:
  - `parent_task_id INT NULL` – parent task for subtasks.
  - `is_completed TINYINT(1) NOT NULL DEFAULT 0` – completion flag.
  - `completed_at DATETIME NULL` – when the task was completed.

- `ai_usage` table:
  - `user_id INT NOT NULL`
  - `date DATE NOT NULL`
  - `requests INT NOT NULL DEFAULT 0`
  - Primary key: `(user_id, date)`

`ai_usage` enforces a simple per-user rate limit: **20 AI requests per day**.

---

## 4. PHP Helpers

File: `helpers/ai.php`

- `generateTaskBreakdown(string $taskTitle, int $maxItems = 8): array`
  - Sends a breakdown prompt to Gemini and returns an array of subtask titles.
  - Cleans bullet markers and numbering from model output.

- `generateWeeklyPlan(array $tasks): array`
  - Expects tasks with:
    - `title`
    - `priority` (`low|medium|high` or similar)
    - `estimated_time`
    - `deadline`
  - Asks Gemini to return a JSON object:
    - Keys: `Monday`–`Sunday`.
    - Values: arrays of task titles.
  - Extracts JSON from model output, parses, and normalizes to:
    - `["Monday" => [...], ..., "Sunday" => [...]]`.

- `callGemini(string $url, array $payload): ?string`
  - Shared low-level HTTP client for Gemini `generateContent` calls.

- `extractJsonObject(string $text): ?string`
  - Finds the first `{ ... }` block and returns it as a JSON string.

- `canUseAiToday(int $userId, int $dailyLimit = 20): bool`
- `incrementAiUsage(int $userId): void`
  - Enforce and update daily AI usage count using `ai_usage`.

---

## 5. AI Task Breakdown Flow

### 5.1 User flow

1. User opens a task creation modal (dashboard or My Task).
2. Fills in title, details, finish date, and priority.
3. Optionally checks the **“Generate subtasks using AI”** checkbox.
4. On save:
   - Main task is created via `/api/tasks/create.php`.
   - If AI is enabled:
     - Frontend calls `/api/tasks/ai-breakdown.php` with:
       - `task_title`
       - `parent_task_id` (ID of the new main task).
     - Subtasks are created and linked to the parent.

### 5.2 Backend pieces

- `api/tasks/create.php`
  - Validates inputs.
  - Creates a task using `Task::addTask(...)`.
  - Returns `{ success: true, task_id: <id> }` on success.

- `api/tasks/ai-breakdown.php`
  - Validates user, title, and `parent_task_id`.
  - Enforces AI usage limit via `canUseAiToday`.
  - Loads the parent task (to inherit `finish_date` and priority).
  - Calls `generateTaskBreakdown(...)`.
  - Inserts each subtask:
    - `parent_task_id = parent id`.
    - `finish_date` = parent’s finish date.
    - `priority` = parent’s priority.
  - Returns:
    - On success: `{ success: true, subtasks: [...] }`.
    - On failure: `{ success: false, message, subtasks: [] }` — main task is still preserved.

### 5.3 UI integration

Files:

- `components/dashboard.php`
- `components/task.php`

Both:

- Extend the SweetAlert `showNewTaskModal()` to include:
  - A checkbox: **Generate subtasks using AI**.
- After successful main-task creation:
  - When AI is enabled, show a loading indicator and call `/api/tasks/ai-breakdown.php`.
  - On success, inform user that subtasks were generated.
  - On failure, inform user politely but keep the main task.

---

## 6. AI Weekly Planner Flow

### 6.1 User flow

1. User navigates to `/dashboard/weekly-planner.php` via:
   - Sidebar link “AI Weekly Planner”.
   - Dashboard card “AI Weekly Planner → Open”.
2. Selects which tasks to include in the plan from a checkbox list.
3. Clicks **“Generate Weekly Plan with AI”**.
4. Board view is populated with tasks per weekday.
5. User optionally clicks **“Apply Plan to Tasks”** to update deadlines.

### 6.2 Backend pieces

- `api/ai/weekly-plan.php`
  - Accepts JSON payload:
    ```json
    {
      "tasks": [
        {
          "title": "...",
          "priority": "...",
          "estimated_time": "...",
          "deadline": "..."
        }
      ]
    }
    ```
  - Validates and trims data (caps at 50 tasks).
  - Enforces AI usage limit.
  - Calls `generateWeeklyPlan(...)`.
  - Returns:
    ```json
    {
      "success": true,
      "plan": {
        "Monday": ["..."],
        "Tuesday": ["..."],
        "...": []
      }
    }
    ```

- `api/ai/apply-weekly-plan.php`
  - Accepts JSON payload:
    ```json
    {
      "plan": {
        "Monday": [taskId1, taskId2],
        "Tuesday": [taskId3],
        ...
      }
    }
    ```
  - Computes the next date for each named weekday.
  - Updates `finish_date` to that day at `18:00:00` for the listed task IDs.

### 6.3 UI integration

File: `dashboard/weekly-planner.php`

- Preloads current user tasks via `Task::getTasks(...)`.
- Provides:
  - **Task selection list** (parents only) with checkboxes.
  - **Generate Weekly Plan with AI** button:
    - Builds payload (includes `title`, `priority`, default `estimated_time`, and `deadline`).
    - Calls `/api/ai/weekly-plan.php`.
  - **Weekly board**:
    - 7 columns (Monday–Sunday), showing tasks returned by Gemini.
    - Shows “No tasks” for empty days.
  - **Apply Plan to Tasks** button:
    - Maps plan titles back to known task IDs where possible.
    - Calls `/api/ai/apply-weekly-plan.php` with IDs.

---

## 7. Extending AI Features

Guidelines for adding new AI-powered tools:

- Reuse `helpers/ai.php` for Gemini calls:
  - Either add new helper functions or adapt existing prompts.
- Respect the rate limit:
  - Call `canUseAiToday` before any new AI endpoint.
  - Call `incrementAiUsage` only after a successful AI response.
- Keep AI optional:
  - Never prevent normal task operations if the model fails or is unavailable.
  - Use neutral, informative messages to describe AI failures.
- Follow the UI and design rules in `DESIGN.md`:
  - Place new AI utilities near relevant task views.
  - Use consistent cards and buttons.

