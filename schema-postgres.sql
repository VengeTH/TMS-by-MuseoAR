-- PostgreSQL schema for OrgaNiss
-- Keeps data types compatible with current PHP query patterns.

BEGIN;

DROP TABLE IF EXISTS ai_usage;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NULL,
    profile_picture VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    details TEXT NULL,
    finish_date TIMESTAMP NOT NULL,
    priority SMALLINT NOT NULL DEFAULT 1 CHECK (priority BETWEEN 1 AND 3),
    parent_task_id INTEGER NULL REFERENCES tasks(id) ON DELETE CASCADE,
    is_completed SMALLINT NOT NULL DEFAULT 0 CHECK (is_completed IN (0, 1)),
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_tasks_user_id ON tasks(user_id);
CREATE INDEX idx_tasks_parent_task_id ON tasks(parent_task_id);
CREATE INDEX idx_tasks_user_completed ON tasks(user_id, is_completed);

CREATE TABLE ai_usage (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    requests INTEGER NOT NULL DEFAULT 0 CHECK (requests >= 0),
    PRIMARY KEY (user_id, date)
);

COMMIT;
