/*
Links in the page content - hibbittsdesign.org
- "newwindow" links open in a new tab, and "topwindow" links in the whole browser window (useful when embedded)
- in embedded mode (/chromeless:true, /embedded:true, /standalone:true or ?embedded=true and similar), following a link
  to another page keeps the page in embedded mode; /hidepagetitle:true is kept too
The same behaviour as Quark Open Publishing's my.js, without jQuery (Quark 2 doesn't include it).
*/
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('a.newwindow').forEach(function (link) {
        link.setAttribute('target', '_blank');
    });
    document.querySelectorAll('a.topwindow').forEach(function (link) {
        link.setAttribute('target', '_top');
    });
    document.querySelectorAll('a[target="_blank"], a[target="_top"]').forEach(function (link) {
        link.classList.add('external-link');
    });

    var content = document.getElementById('body-wrapper');
    if (!content) {
        return;
    }

    content.addEventListener('click', function (e) {
        var link = e.target.closest('a');
        if (!link || !content.contains(link)) {
            return;
        }

        // links that keep their usual behaviour: other windows, in-page anchors, lightbox images, links without an address
        var href = link.getAttribute('href');
        if (href === null || link.classList.contains('external-link') || href.charAt(0) === '#'
            || (link.className && String(link.className).indexOf('glightbox') === 0) || link.getAttribute('rel') === 'lightbox') {
            return;
        }

        var currentUrl = window.location.href;
        var newUrl = href;
        var changed = false;

        // Grav parameters (/name:true) on this page are added to the link
        ['chromeless', 'embedded', 'standalone', 'hidepagetitle'].forEach(function (name) {
            if (currentUrl.indexOf(name + ':true') >= 0) {
                newUrl = newUrl + '/' + name + ':true';
                changed = true;
            }
        });

        // ?embedded=true, ?chromeless=true or ?standalone=true (as in Helios) are carried to links on this site
        var params = new URLSearchParams(window.location.search);
        ['embedded', 'chromeless', 'standalone'].forEach(function (name) {
            var value = params.get(name);
            if (value && value !== 'false' && value !== '0') {
                var target = new URL(newUrl, window.location.href);
                if (target.origin === window.location.origin) {
                    target.searchParams.set(name, value);
                    newUrl = target.pathname + target.search + target.hash;
                    changed = true;
                }
            }
        });

        if (!changed) {
            return;
        }

        e.preventDefault();
        if (e.ctrlKey || e.metaKey) {
            window.open(newUrl, '_blank');
        } else {
            window.location.href = newUrl;
        }
    });
});
