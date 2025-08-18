# Amal WordPress Development Platform

**ALWAYS follow these instructions first and fallback to additional search and context gathering ONLY if the information here is incomplete or found to be in error.**

## Working Effectively

### Bootstrap the Repository
- **CRITICAL**: Install dependencies in correct order to avoid failures:
  1. `composer install` -- installs WordPress core and Bedrock dependencies. **TIMEOUT: 10+ minutes. NEVER CANCEL.** May require GitHub token if rate limited.
  2. `cd web/app/themes/Amal_Sage && composer install` -- installs Sage theme dependencies. **TIMEOUT: 5+ minutes. NEVER CANCEL.**
  3. `cd web/app/themes/Amal_Sage && npm install` -- installs Node.js dependencies. Takes ~10 seconds.

### Build and Validate
- **Build assets**: `cd web/app/themes/Amal_Sage && npm run build` -- compiles CSS/JS with Vite. Takes <2 seconds.
- **Development server**: `cd web/app/themes/Amal_Sage && LARAVEL_BYPASS_ENV_CHECK=1 npm run dev` -- starts Vite dev server on localhost:5173. Only works in non-CI environments.
- **PHP linting**: `composer lint` -- runs Laravel Pint. May fail if dev dependencies not installed due to network issues.
- **Plugin validation**: 
  - `cd web/app/plugins/amal-store && php final-validation-test.php` -- validates store plugin implementation
  - `cd web/app/plugins/amal-auth && php test-auth.php` -- validates auth system (outputs HTML test page)

### Environment Requirements
- **PHP**: 8.1+ (tested with 8.3.6)
- **Node.js**: 20.0.0+ (tested with 20.19.4)
- **Composer**: 2.8+ 
- **Database**: MySQL for WordPress (not required for theme development)

## Validation Scenarios
**ALWAYS test these scenarios after making changes:**

### Theme Development Validation
1. **Asset compilation**: Run `npm run build` and verify no errors. Build should complete in <2 seconds.
2. **Development server**: Start `LARAVEL_BYPASS_ENV_CHECK=1 npm run dev` and verify Vite starts without errors.
3. **File watching**: During development, verify assets rebuild automatically when files change.

### Plugin Validation
1. **Authentication plugin**: Run `php test-auth.php` and verify all system requirements pass.
2. **Store plugin**: Run `php final-validation-test.php` and verify all 18+ tests pass.
3. **PHP syntax**: Run `php -l` on any modified PHP files to check syntax.

### WordPress Integration Validation
**Note**: Full WordPress requires database setup and may not work in limited network environments.
1. **Plugin activation**: Verify plugins can be activated in WordPress admin without fatal errors.
2. **Shortcode functionality**: Test `[amal_register_form]` and `[amal_login_form]` shortcodes.
3. **User workflows**: Test complete registration and login flows if database is available.

## Project Structure and Key Locations

### Theme Development (Primary Location)
- **Main theme**: `/web/app/themes/Amal_Sage/`
- **Source files**: `/web/app/themes/Amal_Sage/resources/` (Blade templates, SCSS, JS)
- **Compiled assets**: `/web/app/themes/Amal_Sage/public/build/` (ignored in Git)
- **Build config**: `/web/app/themes/Amal_Sage/vite.config.js` (Vite + Tailwind + Laravel)

### Custom Plugins (Frequently Modified)
- **Authentication**: `/web/app/plugins/amal-auth/` -- user registration/login system
- **Store management**: `/web/app/plugins/amal-store/` -- inventory management with admin interface  
- **Profile management**: `/web/app/plugins/amal-profile-management/` -- user profile features

### Configuration
- **WordPress config**: `/config/application.php` (Bedrock configuration)
- **Environment**: `/.env` (not in repo, create from template if needed)
- **Composer**: `/composer.json` (root project dependencies)
- **PHP linting**: `/pint.json` (Laravel Pint configuration)

## Common Tasks and Commands

### Theme Development Workflow
```bash
# Setup (run once)
cd web/app/themes/Amal_Sage
composer install --timeout=300  # NEVER CANCEL: 5+ minutes
npm install                     # ~10 seconds

# Development (daily use)
npm run build                   # <2 seconds - compile for production
LARAVEL_BYPASS_ENV_CHECK=1 npm run dev  # Start dev server with hot reload

# Translation (when needed)
npm run translate               # Generate/update translation files
```

### Plugin Development Workflow
```bash
# Test authentication plugin
cd web/app/plugins/amal-auth
php test-auth.php              # Standalone validation (outputs HTML)

# Test store plugin  
cd web/app/plugins/amal-store
php final-validation-test.php  # Full validation suite (18+ tests)

# Test with specific user scenarios
# Note: requires WordPress database setup
```

### Code Quality and Linting
```bash
# PHP linting (may fail without network access)
composer lint                   # Laravel Pint code style check
composer lint:fix              # Auto-fix code style issues

# Manual PHP syntax check
php -l path/to/file.php        # Check individual file syntax

# No dedicated JS/CSS linting (handled by Vite build)
```

## Timing Expectations and Critical Warnings

### **NEVER CANCEL** These Commands:
- `composer install` -- **10+ minutes**, may require GitHub token for rate limits
- `composer install` in Sage theme -- **5+ minutes** 
- Any build command with network dependencies

### Expected Timing:
- `npm install` -- **10 seconds**
- `npm run build` -- **<2 seconds** (Vite is very fast)
- `npm run dev` startup -- **<1 second**
- Plugin validation tests -- **<5 seconds each**
- PHP syntax checks -- **<1 second**

## Known Issues and Workarounds

### Network/Authentication Issues
- **GitHub rate limits**: Composer may prompt for GitHub token. This is normal for public repos.
- **DNS resolution failures**: Some packages (WordPress core, themes) may fail to download in restricted environments.
- **Workaround**: Continue with theme development even if WordPress core isn't fully installed.

### Development Environment Issues  
- **Vite in CI**: Development server fails in CI environments. Use `LARAVEL_BYPASS_ENV_CHECK=1` to bypass.
- **Missing autoload**: If `vendor/autoload.php` missing, run `composer install` to completion.
- **WordPress not loading**: Requires complete Composer installation including WordPress core downloads.

### Plugin Testing Limitations
- **Database-dependent features**: Full WordPress features require MySQL database setup.
- **Standalone testing**: Most plugin validation works without WordPress database.
- **Authentication flows**: User registration/login testing requires database and WordPress environment.

## Architecture Notes

### Technology Stack
- **WordPress** 6.8.2 with **Bedrock** structure (organized, secure WordPress)
- **Sage** theme with **Blade** templating and **Vite** build system
- **Tailwind CSS** 4.0+ for styling
- **Laravel Acorn** for advanced PHP features in theme
- **Custom plugins** for pet services platform functionality

### File Organization
- WordPress core lives in `/web/wp/` (installed via Composer)
- Custom code in `/web/app/` (themes, plugins, uploads)
- Configuration in `/config/` (environment-specific settings)
- Dependencies in `/vendor/` (PHP) and `/node_modules/` (Node.js)

### Development Workflow
1. **Theme changes**: Edit files in `resources/`, run `npm run build` to compile
2. **Plugin changes**: Edit PHP files directly, run validation tests  
3. **Database changes**: Use plugin migration files and helper functions
4. **Style changes**: Edit SCSS in theme `resources/`, auto-compiled by Vite

**Always run asset builds and validation tests before committing changes.**

## Common Command Reference

The following are outputs from frequently run commands. Reference them to save time:

### Repository Root Structure
```
ls -la
total 120
.git/
.github/
.gitignore
IMPLEMENTATION_SUMMARY.md
LICENSE.md
README.md
composer.json
composer.lock
config/
pint.json
vendor/
web/
wp-cli.yml
```

### Theme Package Scripts  
```json
{
  "scripts": {
    "dev": "vite",                      // Start dev server (needs LARAVEL_BYPASS_ENV_CHECK=1)
    "build": "vite build",              // Build for production (~1 second)
    "translate": "npm run translate:pot && npm run translate:update",
    "translate:pot": "wp i18n make-pot . ./resources/lang/sage.pot --include=\"theme.json,patterns,app,resources\"",
    "translate:update": "for file in ./resources/lang/*.po; do wp i18n update-po ./resources/lang/sage.pot $file; done"
  }
}
```

### Build Output Example
```
> npm run build
vite v6.3.5 building for production...
✓ 4 modules transformed.
✓ built in 257ms
```

### Plugin Test Results
```
> php final-validation-test.php
✅ Passed: 18 tests
❌ Failed: 0 tests
🎉 All tests passed!
```

## Manual Validation Workflows

### Complete Theme Development Test
1. Make a small change to `/web/app/themes/Amal_Sage/resources/css/app.css`
2. Run `npm run build` -- should complete in <2 seconds with no errors
3. Check that files appear in `public/build/assets/` directory
4. Start dev server: `LARAVEL_BYPASS_ENV_CHECK=1 npm run dev` 
5. Verify server starts on localhost:5173 without errors
6. Stop dev server with Ctrl+C

### Plugin Functionality Test
1. **Auth plugin**: Run `php test-auth.php` and verify HTML output shows all green checkmarks
2. **Store plugin**: Run `php final-validation-test.php` and verify 18+ tests pass
3. Check PHP syntax: `php -l amal-auth.php` should show "No syntax errors detected"
4. Test plugin structure exists: verify `includes/`, `assets/`, `admin/` directories present

### Integration Test (requires database)
1. Set up WordPress with MySQL database
2. Activate plugins in WordPress admin 
3. Test shortcodes: `[amal_register_form]` and `[amal_login_form]` render forms
4. Complete user registration and login flow
5. Verify admin inventory interface accessible at `/admin/inventory/`

Use these validation workflows to ensure changes work correctly before submitting.