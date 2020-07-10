const Cookies = require('js-cookie/src/js.cookie');

function startAnalytics(varConsent){
    Cookies.set('ga-disable-UA-149860837-1', false, { expires: 395 });
    // insertion de cette valeur dans l'objet window
    window['ga-disable-UA-149860837-1'] = false;

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-149860837-1', 'auto');
    ga('send', 'pageview');

    Cookies.set(varConsent, true, { expires: 395 });
}

function stopAnalytics(varConsent){
    const GA_COOKIE_NAMES = ['__utma', '__utmb', '__utmc', '__utmz', '_ga', '_gat', '_git', '_gid'];
    // création du cookie spécifique empêchant Google Analytics de démarrer
    Cookies.set('ga-disable-UA-149860837-1', true, { expires: 395 });
    // insertion de cette valeur dans l'objet window
    window['ga-disable-UA-149860837-1'] = true;

    // suppression de tous les cookies précédemment créés par Google Analytics
    if(GA_COOKIE_NAMES !== undefined){
        GA_COOKIE_NAMES.forEach(function (value, index) {
            document.cookie = value + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            Cookies.remove(value)
        })
    }
    Cookies.set(varConsent, false, { expires: 395 });
}

module.exports = {
    startAnalytics,
    stopAnalytics
}