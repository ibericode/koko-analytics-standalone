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
    var cookie = window.ka.cookie ? window.ka.cookie : '';
    var isNewVisitor = document.referrer.startsWith(self) ? 0 : 1;
    var isUniquePageview = document.referrer == location.href ? 0 : 1;

    if (cookie) {
        var cookieValue = document.cookie.match(new RegExp("(^|;) ?" + cookie + "\=([^;]+)"));
        var pageHistory = cookieValue ? JSON.parse(cookieValue.pop()) : [];
        isNewVisitor = pageHistory.length ? 0 : 1;
        isUniquePageview = pageHistory.indexOf(path) == -1 ? 1 : 0;

        if (isUniquePageview) {
            pageHistory.push(path);
            document.cookie = cookie + '=' + JSON.stringify(pageHistory) + ';SameSite=lax;path=/;max-age=21600';
        }
    }

    navigator.sendBeacon(window.ka.url + '/collect?' + (new URLSearchParams({
        p: path,
        v: isNewVisitor,
        pv: isUniquePageview,
        r: referrer,
    })));
});
