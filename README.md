# Koko Analytics

> This project is still in early development and a stable version has not yet been released.

Koko Analytics is a PHP application that you can self-host to provide you with simple, open-source, lightweight (< 850 bytes) and privacy-friendly website analytics.

It aims to be a simple alternative to Google Analytics that respects the privacy of your visitors. Nothing visitor specific is tracked, only aggregated counts.

<figure>
  <img src="https://raw.githubusercontent.com/koko-analytics/standalone/main/public/screenshot.png" alt="Screenshot of the Koko Analytics dashboard" loading="lazy" width="830">
  <figcaption>Screenshot of the Koko Analytics dashboard.</figcaption>
</figure>


## Features

- Compliance: GDPR and CCPA Compliant by design.
- Local: No external services.
- Anonymous: No personal information or anything visitor specific is tracked.
- Cookieless: Option to not use any cookies.
- Fast: Handles thousands of daily visitors or sudden bursts of traffic without breaking a sweat.
- Lightweight: The tracking script is < 1 kB.
- Storage efficient: A year worth of data takes up less than 5 MB of database storage.
- Cached: Fully compatible with pages served from server or browser caches.
- Open-source: GNU AGPLv3 licensed.


## Installation

To install Koko Analytics you will need a server with at least the following requirements:


### Requirements

- PHP 8.2 or higher.
- SQLite or MySQL database.


### Deployment

First, read through [deploying a Symfony application](https://symfony.com/doc/current/deployment.html) for a general overview on what to expect in deploying Koko Analytics.

1. Upload the source code to the server on which you want to run Koko Analytics.
1. Use Composer to install dependencies: `composer install --no-dev --optimize-autoloader`
1. Create a local configuration file: `cp .env .env.local`
1. In `.env.local`, update `APP_SECRET` and the various `DATABASE_` entries.
1. Run `php bin/console app:database:migrate` to initialize the database.
1. Configure your webserver to point all requests to `public/index.php`
1. Run `php bin/console app:user:create <email> <password>` to register a new user.


### Tracking snippet

To start collecting visitor statistics for any website, deploy this application to a suitable location and then add the following tracking snippet to your pages.

```html
<script>
(function(o, c) {
  window[o] = c;
  var s = document.createElement('script');
  s.defer = true;
  s.src = [c.url, '/', o, '.js'].join('');
  document.body.appendChild(s);
})('ka', {
  url: 'http://localhost:8000',   /* the URL to your Koko Analytics application instance */
  cookie: 'koko_analytics',       /* name of the cookie to use or empty for no cookie */
})
</script>
```

## License

Koko Analytics is open-source software using the GNU AGPLv3 license.
