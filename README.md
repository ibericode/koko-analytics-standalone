# Koko Analytics

Open-source, self-hosted and privacy-friendly website analytics.

> This project is still in development and a stable version has not yet been released. We expect to be able to release our first stable version in Q2 of 2025.

This repository is a ported version of the [Koko Analytics plugin for WordPress](https://www.kokoanalytics.com/), but built on top of Symfony framework.

<figure>
  <img src="https://raw.githubusercontent.com/koko-analytics/standalone/main/public/screenshot.png" alt="Screenshot of the Koko Analytics dashboard" loading="lazy" width="830">
  <figcaption>Screenshot of the Koko Analytics dashboard.</figcaption>
</figure>


## Requirements

- PHP 8.2 or higher.
- MySQL compatible database.

## Deployment

This application is still undergoing heavy development, so expect things to change and documentation to be sparse. That said, here are some pointers in case you do already want to play around with it on a server of your own.

First, read through [deploying a Symfony application](https://symfony.com/doc/current/deployment.html) for a general overview on what to expect in deploying Koko Analytics.

1. Upload the source code to the server on which you want to run Koko Analytics.
1. Use Composer to install dependencies: `composer install --no-dev --optimize-autoloader`
1. Create a local configuration file: `cp .env .env.local`
1. In `.env.local`, update `APP_SECRET` and the various `DATABASE_` entries.
1. Run `php bin/migrate` to initialize the database.
1. Configure your webserver to point all requests to `public/index.php`


## Tracking snippet

To start collecting visitor statistics for any website, deploy this application to a suitable location and then add the following tracking snippet to your pages.

```html
<script>
((ka, cnf) => {
  window[ka] = cnf;
  let s = document.createElement('script');
  s.defer = true;
  s.src = [cnf.url, '/', ka, '.js'].join('');
document.body.appendChild(s)
})('ka', {
  url: 'http://localhost:8000', /* the URL to your Koko Analytics application instance */
})
</script>
```

## License

GPL 2.0 or later.
