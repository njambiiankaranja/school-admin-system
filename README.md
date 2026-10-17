# School Admin System

A comprehensive full-stack web application designed to streamline school administration tasks, including student management, course enrollment, assignments, grading, and departmental organization.

---

## Features

- **User Authentication & Role Management:** Secure login and registration supporting different user roles (Administrators, Lecturers, and Students).
- **Course & Department Management:** Add, view, and manage academic courses and school departments.
- **Assignment Dashboard:** Lecturers can create and grade assignments, while students can view, submit, and track their assignment statuses.
- **Attendance & Schedules:** Track student attendance and manage class timetables.
- **Announcements & Calendar:** Keep the school community updated with system-wide announcements and event calendars.

---

## Tech Stack

- **Backend:** PHP
- **Frontend:** HTML, JavaScript, Tailwind CSS
- **Database:** MySQL (`sitedata.sql`, `users.sql`)
- **CI/CD & Deployment:** Jenkins, AWS (Amazon Web Services)

---

## Project Structure

```text
school-admin-system/
├── styles/                  # CSS stylesheets and assets
├── uploads/                 # User-uploaded files and attachments
├── *.php                    # Core application views and backend logic
├── tailwind.config.js       # Tailwind CSS configuration
├── sitedata.sql             # Initial database schema and mock data
└── Jenkinsfile              # CI/CD pipeline configuration