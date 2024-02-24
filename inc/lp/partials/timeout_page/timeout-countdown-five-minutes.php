<?php

defined('ABSPATH') || exit;

$time_limit = 300;

$is_preview = WebinarignitionManager::url_is_preview_page();

if( isset( $_GET['lid'] ) ) {

	$lead_id    = sanitize_text_field( $_GET['lid'] );
	$watch_time = (int) get_option('wi_lead_watch_time_'. $lead_id, true );

    if($statusCheck->name == 'ultimate_powerup_tier1a') {
        $watch_limit = HOUR_IN_SECONDS * 2;
    } else {
        $watch_limit = MINUTE_IN_SECONDS * 45;
    }

	if( $watch_limit - $watch_time > 300 ) {
		$time_limit = 300;
	} else {
		$time_limit = $watch_limit - $watch_limit;
	}
}

?>
<style type="text/css">

#wi_count_down_5_mint{
	position: absolute;
	right: 0;
	top: 0;
	z-index: 9999999;
}

.base-timer {
  position: relative;
  width: 100px;
  height: 100px;
  background: rgba(0,0,0,0.5);
  border-radius: 100px;
}

.base-timer__svg {
  transform: scaleX(-1);
}

.base-timer__circle {
  fill: none;
  stroke: none;
}

.base-timer__path-elapsed {
  stroke-width: 7px;
  stroke: grey;
}

.base-timer__path-remaining {
  stroke-width: 7px;
  stroke-linecap: round;
  transform: rotate(90deg);
  transform-origin: center;
  transition: 1s linear all;
  fill-rule: nonzero;
  stroke: currentColor;
}

.base-timer__path-remaining.green {
  color: rgb(65, 184, 131);
}

.base-timer__path-remaining.orange {
  color: orange;
}

.base-timer__path-remaining.red {
  color: red;
}

.base-timer__label {
  position: absolute;
  width: 100px;
  height: 100px;
  top: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
}
</style>
<div id="wi_count_down_5_mint"></div>
<script>
const FULL_DASH_ARRAY = 283;
const WARNING_THRESHOLD = 10;
const ALERT_THRESHOLD = 5;

const COLOR_CODES = {
  info: {
    color: "green"
  },
  warning: {
    color: "orange",
    threshold: WARNING_THRESHOLD
  },
  alert: {
    color: "red",
    threshold: ALERT_THRESHOLD
  }
};

var WI_TIME_LIMIT              =<?php echo intval($time_limit); ?>;
var wi_timeover_timePassed    = 0;
var wi_timeover_timeLeft      =<?php echo intval($time_limit); ?>;
let wi_timeover_timerInterval = null;
let remainingPathColor = COLOR_CODES.info.color;
var wi_paused_interval = false;
var wi_countdown_started = false;
var wi_timeover_popop_display = false;
<?php wp_enqueue_script('jquery'); ?>

jQuery(document.body).on('wi_start_timeout_countdown', function(){

	document.getElementById("wi_count_down_5_mint").innerHTML = `
	<div class="base-timer">
	  <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
	    <g class="base-timer__circle">
	      <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
	      <path
	        id="base-timer-path-remaining"
	        stroke-dasharray="283"
	        class="base-timer__path-remaining ${remainingPathColor}"
	        d="
	          M 50, 50
	          m -45, 0
	          a 45,45 0 1,0 90,0
	          a 45,45 0 1,0 -90,0
	        "
	      ></path>
	    </g>
	  </svg>
	  <span id="base-timer-label" class="base-timer__label">${wi_timeover_formatTime(
	    wi_timeover_timeLeft
	  )}</span>
	</div>
	`;

	wi_timeover_startTimer();
});

<?php if( $is_preview && current_user_can('edit_posts') && isset( $webinar_data->webinar_live_video ) ) {
    if( is_plugin_active('webinar-ignition-helper/webinar-ignition-helper.php') ) {
      ?>
      WI_TIME_LIMIT = 300;
      wi_timeover_timeLeft = 300;
      <?php
    } else {

      if($statusCheck->name == 'ultimate_powerup_tier1a') {
          $watch_limit = HOUR_IN_SECONDS * 2;
      } else {
          $watch_limit = MINUTE_IN_SECONDS * 45;
      }
      ?>
        WI_TIME_LIMIT = <?php echo $watch_limit; ?>;
        wi_timeover_timeLeft = <?php echo $watch_limit; ?>;
      <?php
    }
    ?>
    jQuery(document.body).trigger('wi_start_timeout_countdown');
    wi_countdown_started = true;
    <?php
}
?>

function wi_timeover_onTimesUp() {
  clearInterval(wi_timeover_timerInterval);
}

function wi_timeover_startTimer() {
  wi_timeover_timerInterval = setInterval(() => {

  	if( !wi_paused_interval ) {

  		wi_timeover_timePassed = wi_timeover_timePassed += 1;
      wi_timeover_timeLeft = WI_TIME_LIMIT - wi_timeover_timePassed;

      if (wi_timeover_timeLeft <= 0 ) {
        wi_timeover_onTimesUp();
        return;
      }

	    document.getElementById("base-timer-label").innerHTML = wi_timeover_formatTime(
	      wi_timeover_timeLeft
	    );
	    wi_timeover_setCircleDasharray();
	    setRemainingPathColor(wi_timeover_timeLeft);


  	}
	    
  }, 1000);
}

function wi_timeover_formatTime(time) {
  const minutes = Math.floor(time / 60);
  let seconds = Math.floor(time % 60);

  if (seconds < 10) {
    seconds = `0${seconds}`;
  }

  return `${minutes}:${seconds}`;
}

function setRemainingPathColor(wi_timeover_timeLeft) {
  const { alert, warning, info } = COLOR_CODES;
  if (wi_timeover_timeLeft <= alert.threshold) {
    document
      .getElementById("base-timer-path-remaining")
      .classList.remove(warning.color);
    document
      .getElementById("base-timer-path-remaining")
      .classList.add(alert.color);
  } else if (wi_timeover_timeLeft <= warning.threshold) {
    document
      .getElementById("base-timer-path-remaining")
      .classList.remove(info.color);
    document
      .getElementById("base-timer-path-remaining")
      .classList.add(warning.color);
  }
}

function wi_timeover_calculateTimeFraction() {
  const rawTimeFraction = wi_timeover_timeLeft / WI_TIME_LIMIT;
  return rawTimeFraction - (1 / WI_TIME_LIMIT) * (1 - rawTimeFraction);
}

function wi_timeover_setCircleDasharray() {
  const circleDasharray = `${(
    wi_timeover_calculateTimeFraction() * FULL_DASH_ARRAY
  ).toFixed(0)} 283`;
  document
    .getElementById("base-timer-path-remaining")
    .setAttribute("stroke-dasharray", circleDasharray);
}
</script>