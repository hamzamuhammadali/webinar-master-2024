jQuery.fn.extend({
    hideCTA: function() {
        var docWidth = window.innerWidth;
        this.css({'display':'none'});

        if( !this.hasClass('wi-tab-pane') ) {
            this.css({'left':docWidth+'px'});
            console.log("triggered");
        }
        // this.css({'display':'none'});
        this.removeClass('active');

        return this;
    },
    showCTA: function() {
        this.css({'display':'block'});
        if( !this.hasClass('wi-tab-pane') ) {
            this.css({'left':'0px'});
            console.log("webi nar - cta js ");
        }

        this.addClass('active');
        return this;
    },
});
(function ($) {
    'use strict';

    const WI_CTA = {
        init: function () {
            $(document.body).on('wi_player_play', function (e, player) {
                WI_CTA.reset();
            });

            $(document.body).on('wi_video_ended', function (e) {
                WI_CTA.reset(true);
            });

            $(document.body).on('wi_player_pause', function (e, player) {
                // console.log('wi_player_pause');
                if( player.seeking() === false && player.ended() === false ) {
                    WI_CTA.reset();
                }
            });

            $(document).on('wi_video_timeupdate', function (e, cta, currentTime, isPaused, videoDuration) {

                if(isPaused) return; //Do not trigger CTA show/hide when video is paused

                let currentTimeInSeconds    = currentTime * 1000;   //Convert currentTime seconds into milliseconds
                let videoDurationInSeconds  = videoDuration * 1000; //Convert videoDuration seconds into milliseconds

                if( currentTimeInSeconds > cta.start && currentTimeInSeconds < cta.end ) {
                    $( document.body ).trigger( 'wi_cta_show', [ cta.index ] );
                } else {
                    //Do not hide CTA if end time is greater than the video time
                    if( cta.end > videoDurationInSeconds ) {
                        return;
                    }

                    $(document.body).trigger('wi_cta_hide', [cta.index]);
                }
            });

            $(document).on('wi_cta_show', function (e, ctaIndex) {
                let cta_element_id = 'wi-cta-' + ctaIndex;
                let cta_tab = $('#' + cta_element_id + '-tab');
                let cta_tab_contents_element = cta_tab;
                // console.log('#' + cta_element_id + '-tab');

                if(cta_tab.length > 0) {
                    if(cta_tab.hasClass('timedUnderAreaOverlay') || cta_tab.hasClass('timedUnderArea')) {

                        cta_tab_contents_element.find('.pre-hurrytimer-campaign').each(function() {
                            $( this ).addClass('hurrytimer-campaign');
                        });

                        cta_tab.showCTA();

                    } else {
                        let cta_tab_contents_element = $('div#webinarTabsContent #' +  cta_element_id);
                        let cta_tab_parent = cta_tab.parent('li');
                        if (cta_tab_parent.is('visible') === false) {
                            if (cta_tab.hasClass('clicked') === false) {
                                cta_tab.addClass('clicked').trigger('click');

                                cta_tab_contents_element.find('.pre-hurrytimer-campaign').each(function() {
                                    $( this ).addClass('hurrytimer-campaign');
                                });

                                cta_tab_parent.show();
                                cta_tab_contents_element.showCTA(500, function() {
                                    $(window).trigger('wi_webinar_refresh'); //Refresh webinar contents
                                });

                                // console.log(cta_tab.text() + ' Clicked!');
                                WI_CTA.toggleSidebar();
                            }
                        }
                    }
                }
            });

            $(document).on('wi_cta_hide', function (e, ctaIndex) {
                let cta_element_id = 'wi-cta-' + ctaIndex;
                let cta_tab = $('#' + cta_element_id + '-tab');

                if( cta_tab.length > 0 ) {
                    if(cta_tab.hasClass('timedUnderAreaOverlay') || cta_tab.hasClass('timedUnderArea')) {
                        cta_tab.hideCTA();
                    } else {
                        let cta_tab_contents_element = $('div#webinarTabsContent #' +  cta_element_id);
                        let cta_tab_parent = cta_tab.parent('li');

                        if (cta_tab_parent.is(':visible')) {
                            if ( cta_tab_parent.hasClass('wi-cta-tab') && cta_tab.hasClass('clicked') === true) {
                                cta_tab.removeClass('clicked').removeClass('active');
                                cta_tab_parent.hide();
                                cta_tab_contents_element.hideCTA(500, function() {
                                    if( $('#webinarTabs li:visible').length === 0 ) {
                                        $('#webinarTabsContent').css({'height':'auto'});
                                        $(window).trigger('wi_webinar_refresh'); //Refresh webinar contents
                                    }
                                });

                                var first_visible_li_in_ul = cta_tab_parent.closest('ul').find("li.wi-cta-tab:visible").eq(0);
                                if(first_visible_li_in_ul.length === 0) {
                                    first_visible_li_in_ul = cta_tab_parent.closest('ul').find("li:visible").eq(0);
                                }
                                // console.log(first_visible_li_in_ul.find('a').text() + ' Clicked!');
                                first_visible_li_in_ul.find('a').trigger('click').addClass('clicked');
                                WI_CTA.toggleSidebar();
                            } else {
                                if ( cta_tab_parent.hasClass('wi-cta-tab') === false ) {
                                    cta_tab.trigger('click');
                                }
                            }
                        }
                    }
                }
            });
        },
        reset: function(keepCTA) {

            if(typeof keepCTA === undefined) keepCTA = false;

            //Keep CTAs intact
            if( !keepCTA ) {
                WI_CTA.hideAllCTA();
            }

            $('#webinarTabs').find('li:visible').eq(0).find('a').trigger('click');
        },
        hideAllCTA: function() {
            $('#webinarTabs').find('li.wi-cta-tab').each(function (wi_cta_tab_li_index, wi_cta_tab){
                $(wi_cta_tab).hide().find('a.clicked').removeClass('clicked');
                let wi_cta_tab_index = $(wi_cta_tab).children('a').attr('id').replace('-tab', '');
                $('div#webinarTabsContent #' +  wi_cta_tab_index).hide();
            });

            $('div.wi-cta-tab').each(function(i, div) {
                if($(div).hasClass('wi-cta-tab-keep') === false) {
                    $(div).hide();
                }
            });
        },
        toggleSidebar: function() {
            if( $('#webinarTabs li:visible').length > 0 ) {
                $('#webinarVideo').addClass('wi-col-lg-9');
                $('#webinarSidebar').addClass('wi-col-lg-3');
            } else {
                $('#webinarVideo').removeClass('wi-col-lg-9');
                $('#webinarSidebar').removeClass('wi-col-lg-3');
            }
        }
    }

    $(document).ready(function(e) {
        WI_CTA.init();
    });

})(jQuery);