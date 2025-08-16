# Amal Project - Coding Agent README

## Overview
This repository hosts the **Amal** project, a WordPress-based application built with **Bedrock** and **Sage**. The goal of this agent is to assist with coding tasks, issue resolution, and feature development for the Amal project.

The agent should follow best practices in WordPress development, PHP, JavaScript, and front-end workflows, while ensuring compatibility with Sage theme structures and Bedrock configuration.

---

## Project Stack

- **WordPress** (Bedrock structure)
- **Sage Theme** (WordPress starter theme with modern workflow)
  - Blade templating
  - Webpack build system
- **PHP 8+**
- **JavaScript / ES6+**
- **Sass / SCSS**
- **Node.js / npm** (for Sage build tasks)
- **Composer** (for PHP dependency management)
- **Git / GitHub** (version control)
- **XAMPP** (local development environment)
- **MySQL** (via XAMPP)

---

## Folder Structure

```text
amal/
├─ web/
│  ├─ app/
│  │  ├─ themes/Amal_Sage/
│  │  │  ├─ resources/       # Blade templates, SCSS, JS source
│  │  │  ├─ dist/            # Compiled assets (ignored in Git)
│  │  │  ├─ public/          # Compiled front-end files (ignored in Git)
│  │  │  └─ node_modules/    # Node dependencies (ignored in Git)
│  └─ uploads/               # WordPress uploads (ignored in Git)
├─ vendor/                   # Composer dependencies (ignored in Git)
└─ .env                      # Environment variables (ignored in Git)

