# XAMPP Virtual Host Setup for Amal Testing

This directory contains the testing interface for the Amal platform. If you're using XAMPP and want to set up a local virtual host for easier testing, follow these instructions.

## Virtual Host Configuration

Add this configuration to your XAMPP Apache `httpd-vhosts.conf` file (usually located at `C:\xampp\apache\conf\extra\httpd-vhosts.conf`):

```apache
<VirtualHost *:80>
    ServerName tests.amal.local
    DocumentRoot "C:/xampp/htdocs/amal/tests"
    <Directory "C:/xampp/htdocs/amal/tests">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog "logs/tests.amal.local-error.log"
    CustomLog "logs/tests.amal.local-access.log" common
</VirtualHost>
```

## Windows Hosts File

Add this line to your Windows hosts file (usually at `C:\Windows\System32\drivers\etc\hosts`):

```
127.0.0.1 tests.amal.local
```

## How the Routing Works

This directory includes:

1. **`.htaccess`** - Apache configuration for proper URL rewriting and file handling
2. **`web` symlink** - A symbolic link to the main web directory for easy access to plugin demos
3. **Updated paths** - All demo links in `index.html` have been updated to work with the virtual host setup

## Accessing the Testing Interface

1. Start XAMPP Apache server
2. Open your browser and go to: `http://tests.amal.local`
3. All demo links should now work correctly

## Troubleshooting

- **404 errors**: Make sure the DocumentRoot path matches your actual project location
- **Symlink issues**: Ensure your XAMPP Apache has FollowSymLinks enabled (included in the .htaccess)
- **Permission issues**: Check that the Apache user has read permissions for the entire project directory

## Alternative Access Methods

If virtual hosts don't work, you can also:

1. Copy this entire `tests` directory to your `C:\xampp\htdocs\` folder
2. Copy the `web` directory to `C:\xampp\htdocs\tests\web`
3. Access via `http://localhost/tests/`