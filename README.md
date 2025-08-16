# Amal Project - Coding Agent README

## Overview
This repository hosts the **Amal** project, a WordPress-based application built with **Bedrock** and **Sage**. The goal of this agent is to assist with coding tasks, issue resolution, and feature development for the Amal project.

**Amal is a comprehensive service platform for pet owners and service providers.** It allows users to offer and request pet-related services such as pet sitting, dog walking, grooming, training, and other care services. The platform is designed to connect pet owners with trusted service providers, offering a seamless experience for managing bookings, payments, and communication.

Key aspects of the app:
- **User roles:** Pet owners and service providers, each with different permissions and dashboard views.
- **Service management:** Service providers can list services, set pricing, and manage availability. Pet owners can browse, book, and review services.
- **Communication:** Users can message each other to coordinate details.
- **Health & safety:** Optional integrations like smart collars for pet monitoring (future development).
- **Payment workflow:** Booking, payments, and loyalty points system for rewards.
- **Technology stack:** WordPress (Bedrock), Sage theme, PHP, JavaScript (ES6+), npm, Composer, Git, and modern front-end build tools.

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


## GitHub Copilot Agent Configuration

This section guides the Copilot coding agent on how to handle tasks, issues, and coding within the Amal repository.

### Agent Role
- Operates as a development assistant for the Amal project.
- Can generate code, refactor existing files, or create templates.
- Should suggest improvements while respecting project architecture (Bedrock + Sage).
- Should reference the README and folder structure before making changes.

### Issue Handling
- Check open issues in GitHub and prioritize by label and urgency.
- For feature requests:
  - Generate code snippets or full implementations.
  - Follow existing project patterns.
  - Ensure changes are modular and maintainable.
- For bug fixes:
  - Replicate the problem in local context.
  - Suggest the simplest fix that resolves the issue.
  - Update any necessary tests or documentation.

### Branching / Commits
- Work on separate branches named according to the issue number or feature.
  - Example: `issue-12-fix-login-bug` or `feature-frontend-carousel`
- Generate meaningful commit messages reflecting the change:
  - “Fix: Correct availability check in Coffee Lottery matching”
  - “Feat: Add SCSS variables for theme color palette”
- Avoid committing generated `node_modules`, `dist/`, `public/` or `.env` files.

### Code Style Guidelines
- **PHP**: Follow WordPress coding standards.
- **JS/ES6+**: Use modern syntax and Sage conventions.
- **SCSS**: Maintain modular and reusable styles.
- **Blade templates**: Keep templates clean, modular, and DRY.

### Build & Testing
- Before committing changes:
  - Run `npm run build` in `/web/app/themes/Amal_Sage` to ensure assets compile.
  - Ensure no fatal PHP errors occur.
  - Test any front-end functionality locally.

### Communication
- Reference issues or PRs when generating code.
- Comment inline in code if a generated solution needs human verification.
- Suggest improvements or alternative implementations where applicable.

---

> ⚠️ Note: The agent must respect the `.gitignore` rules and never attempt to commit large generated directories like `node_modules` or compiled `dist/public` files.


