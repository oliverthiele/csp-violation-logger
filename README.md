# CSP Violation Logger

* Version: 0.1.0
* Author: Oliver Thiele

License: GNU GENERAL PUBLIC LICENSE Version 2

# Benefits of this package

With this script Content Security Violations (CSP) can be logged on a separate server.
Since different web browsers transfer different data via JSON,
these are processed by the script and stored in a MySQL database.

## Installation

### 1. Get the package:

`composer require oliverthiele/csp-violation-logger --no-dev`

### 2. Setup the database:

The setup file will add a new database **"csp_1"** and the db user **"csp"** with a random password.
The database configuration is automatically saved in the *Private/.env* file.
You must be root to execute setup.sh.

    `./Setup/setup.sh`

### 3.  Activate the reporting:

For example, write your CSP configuration to the file *"/etc/nginx/snippets/ContentSecurityPolicy.nginx"* and
import this in your nginx configuration with `include snippets/ContentSecurityPolicy.nginx;`

**Example for the snippet ContentSecurityPolicy.nginx:***

    ```nginx configuration
    # Note that 'unsafe-inline' is ignored if either a hash or nonce value is present in the source list.
    # Fallback 'unsafe-inline' is only for old browsers.

    set $MANIFEST "manifest-src 'self'";

    set $SCRIPTELEM "script-src-elem 'self'";
    set $SCRIPTELEM "${SCRIPTELEM} https://www.google-analytics.com";
    # todo
    set $SCRIPTELEM "";

    set $SCRIPT "script-src 'self' 'unsafe-inline'";
    set $SCRIPT "${SCRIPT} https://www.google-analytics.com/analytics.js"; # Google Analytics
    set $SCRIPT "${SCRIPT} 'sha256-yL9ZJTwjPrn9w/CyKd4UniatMJZiPdtdFzMMR9AEhbQ='"; # Cookie Consent

    # style-src
    set $STYLE "style-src 'self' 'unsafe-hashes' 'unsafe-inline'";
    # set $STYLE "${STYLE} 'sha256-7Wj4JppQPW/r0fhp+Y3lFnfwMGJjSJYaErRdXi/jGxw='"; # TYPO3 Preview CSS

    set $IMG "img-src 'self' data:";
    set $IMG "${IMG} https://www.google-analytics.com www.google-analytics.com https://stats.g.doubleclick.net;";

    set $FONT "font-src 'self' data:";
    # set $FONT "${FONT} https://example.com";

    set $DEFAULT "default-src 'none'";

    set $CONNECT "connect-src 'self'";
    set $CONNECT "${CONNECT} https://www.google-analytics.com www.google-analytics.com https://stats.g.doubleclick.net";
    set $CONNECT "${CONNECT} https://hello.myfonts.net";

    set $FRAME "frame-src 'self'";
    # set $FRAME "${FRAME} https://example.com";

    add_header Content-Security-Policy-Report-Only " ${DEFAULT}; ${MANIFEST}; ${SCRIPTELEM}; ${SCRIPT}; ${STYLE}; ${IMG}; ${FONT}; ${CONNECT}; ${FRAME}; report-uri https://csp.example.com/log.php" always;
    ```


If the CSP configuration no longer logs errors, then
**Content-Security-Policy-Report-Only** can be changed to **Content-Security-Policy**.

## Security

### Secure the .env file;

Use basic authentication (.htpasswd) to secure the Private directory

### Log-Funktion

You have to allow the log.php access only for the Webserver-IP.

**Example:**

```nginx configuration
location /log.php {
    auth_basic "Restricted";
    auth_basic_user_file /var/www/.htpasswd;

    satisfy any;
    allow 127.0.0.0/8;
    allow 10.0.0.0/8;
    allow 172.16.0.0/12;
    allow 192.168.0.0/16;

    # example.com (Web server with CSP)
    allow 123.123.123.123;
    allow 1234:1234:1234:1234::/64;

    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
}
```

### Secure the output of the log view

You have to allow the index.php access only with basic authentication.

**Example:**

```nginx configuration
location / {
    auth_basic "Restricted";
    auth_basic_user_file /var/www/.htpasswd;

    satisfy any;
    allow 127.0.0.0/8;
    allow 10.0.0.0/8;
    allow 172.16.0.0/12;
    allow 192.168.0.0/16;

    try_files $uri $uri/ =404;
 }
```

## Todo's

* Add a script to remove old database entries
* Add filters for the output
* Use update queries, if a CSP Violation is already reported and update only the timestamp.
