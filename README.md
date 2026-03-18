# DATALaboBlog

A mini blog/CMS built with **PHP**, **MySQL**, and **Bootstrap** as a server-rendered web application.

It allows visitors to browse published posts and leave comments without logging in, while authenticated users can create and manage their own posts.  
The application also includes a **draft → published** workflow and **role-based access control** for regular users and admins.

---

## Features

### Public
- View published posts with **pagination**
- View a single post with its comments
- Add a public comment without logging in

### Authenticated User
- Login / Logout
- Create new posts
- Save posts as **draft** or **published**
- Edit or delete **only their own** posts
- Publish drafts (**draft → published**, no revert)

### Admin
- Delete comments
- Create new users
- Create new admins
- Access admin tools for content and user management

---

## Tech Stack

- **PHP** (server-rendered)
- **MySQL**
- **Bootstrap**
- **PDO**
- **Docker Compose** (optional local DB setup)
- **DBeaver** (database GUI)

---

## Project Structure

- `public/` — browser-accessible pages (UI)
- `public/servers/` — thin POST entry-point wrappers
- `servers/` — main POST handlers (create / update / delete actions)
- `functions/` — shared helpers (PDO, auth, CSRF, validation)
- `database/` — SQL scripts (`schema.sql`, `seed.sql`)
- `storage/` — logs (not publicly accessible)

---

## Database Design

### Main Tables
- `users` — stores user accounts and roles (`user`, `admin`)
- `posts` — stores blog posts and status (`draft`, `published`)
- `comments` — stores public comments linked to posts

### Relationships
- One **user** can create many **posts**
- One **post** can have many **comments**

---

## Security / Best Practices

This project includes:

- Password hashing for stored credentials
- PDO with prepared statements to help prevent SQL injection
- CSRF protection for POST requests
- Server-side input validation
- Authorization checks so users can edit/delete only their own posts
- Role-based access control for admin-only actions

---

## Local Setup

### 1) Create the database and tables
Import:

- `database/schema.sql`

This creates:
- Database: `database_labo`
- Required tables for users, posts, and comments

---

### 2) Insert demo data (recommended)
Import:

- `database/seed.sql`

This adds:
- A default admin account
- Default user accounts
- Demo posts
- Demo comments

#### Demo Credentials
- **Admin**  
  Username: `admin`  
  Password: `Admin123!`

- **User**  
  Username: `user`  
  Password: `User123!`

- **User 2**  
  Username: `user2`  
  Password: `password`

---

### 3) Run the PHP development server

From the project root:

```bash
php -S 127.0.0.1:8000 -t public


### 4) Open the application

Posts list: http://127.0.0.1:8000/Posts_List.php

Login page: http://127.0.0.1:8000/User_Login.php


#### Routing Note

    When running the app with:

    php -S 127.0.0.1:8000 -t public

    only the public/ directory is directly accessible from the browser.

    For this reason:

    public/servers/*.php act as thin entry-point wrappers for browser POST requests

    the actual business logic is handled inside /servers

    This keeps the application structure cleaner and avoids exposing internal logic directly.


    