window.addEventListener('load', function() {
    if (
        document.visibilityState === 'prerender'
        || (/bot|crawl|spider|seo|lighthouse|preview/i).test(navigator.userAgent)
    ) {
        return
    }

    var path = window.ka.path;
    var self = location.origin;
    path = path ? path : location.pathname;
    var canonical = document.querySelector('link[rel="canonical"]');
    if (canonical) {
        path = canonical.href.split(self.split('http').pop()).pop();
    }
    path = path.replace(/.*:\/\/[^\/]+/, '')
    var referrer = document.referrer.startsWith(self) ? '' : document.referrer;

    navigator.sendBeacon(window.ka.url + '/collect?' + (new URLSearchParams({
        s: window.ka.site,
        p: path,
        v: 1,
        pv: 1,
        r: referrer,
    })));
});
