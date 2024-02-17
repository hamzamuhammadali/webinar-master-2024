<?php defined( 'ABSPATH' ) || exit; 
/**
 * @var $webinar_data
 */
?>

<div class="topArea">
    <div class="bannerTop">
        <?php
        if ($webinar_data->webinar_banner_image == "") {
            // echo "NONE";
        } else {
            echo "<img src='$webinar_data->webinar_banner_image' />";
        }
        ?>
    </div>
</div>
