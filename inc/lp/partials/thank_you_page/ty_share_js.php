<?php defined( 'ABSPATH' ) || exit; ?>
<script type="text/javascript">

    window.fbAsyncInit = function () {
        FB.init({
            appId: '178580152294594', status: true, cookie: true,
            xfbml: true
        });

        // FACEBOOK LIKE/SHARE
        FB.Event.subscribe('edge.create',
            function (response) {
                $(".sharePRE").hide();
                $(".shareREVEAL").show();
                jQuery(".sharePRE").hide();
                jQuery(".shareREVEAL").show();
            }
        );

    };
    (function () {
        var e = document.createElement('script');
        e.async = true;
        e.src = document.location.protocol +
        '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
    }());


</script>



<!-- TWITTER -->
<script type="text/javascript">

    //Twitter Widgets JS
    window.twttr = (function (d, s, id) {
        var t, js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);
        return window.twttr || (t = {
                _e: [], ready: function (f) {
                    t._e.push(f)
                }
            });
    }(document, "script", "twitter-wjs"));

    //Once twttr is ready, bind a callback function to the tweet event
    twttr.ready(function (twttr) {
        twttr.events.bind('tweet', function (event) {
            jQuery(".sharePRE").hide();
            jQuery(".shareREVEAL").show();
        });
    });

</script>


