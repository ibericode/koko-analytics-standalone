window.addEventListener('load', function() {
    if (
        document.visibilityState === 'prerender'
        || (/bot|crawl|spider|seo|lighthouse|facebookexternalhit|preview/i).test(navigator.userAgent)
    ) {
        return
    }

    var path = window.ka.path ? window.ka.path : location.pathname;
    var self = location.origin;
    var canonical = document.querySelector('link[rel="canonical"]');
    if (canonical) {
        path = canonical.href.split(self.split('http').pop()).pop();
    }
    path = path.replace(/.*:\/\/[^\/]+/, '')
    var referrer = document.referrer.startsWith(self) ? '' : document.referrer;
    navigator.sendBeacon(window.ka.url + '/collect',  new URLSearchParams({
        d: window.ka.domain,
        p: path,
        r: referrer,
    }));
});
