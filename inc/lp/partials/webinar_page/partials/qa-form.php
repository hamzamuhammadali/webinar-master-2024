<?php
/**
 * @var $webinar_data
 * @var $name
 * @var $email
 * @var $allow_qa_edit_name_email
 * @var $webinar_id
 */

if ( $webinar_data->webinar_qa == "custom" ) {
    webinarignition_display( $webinar_data->webinar_qa_custom, __( "CUSTOM Q/A SYSTEM WILL DISPLAY HERE... NO CODE ENTERED...", "webinarignition") );
} else {
    if ('chat' === $webinar_data->webinar_qa && !WebinarignitionPowerups::is_two_way_qa_enabled($webinar_data)) {
        $webinar_data->webinar_qa = 'we';
    }

    if ( 'chat' === $webinar_data->webinar_qa ) {
        ?>
        <input type="hidden" id="optName" value="<?php echo $name; ?>">
        <input type="hidden" id="optEmail" value="<?php echo $email; ?>">
        <?php
    } else {
        ?>
        <div class="form-group wiFormGroup wiFormGroup-lg">
            <input
                    value="<?php echo $name; ?>"
                    type="<?php echo $allow_qa_edit_name_email ? 'text' : 'hidden'; ?>"
                    id="optName"
                    class="optNamer2 wiRegForm wiFormControl"
                    placeholder="<?php webinarignition_display( $webinar_data->webinar_qa_name_placeholder, __( "Enter Your Full Name...", 'webinarignition' ) ); ?>"

            >
        </div>

        <div class="form-group wiFormGroup wiFormGroup-lg">
            <input
                    value="<?php echo $email; ?>"
                    type="<?php echo $allow_qa_edit_name_email ? 'text' : 'hidden'; ?>"
                    id="optEmail"
                    class="optEmailr2 wiRegForm wiFormControl"
                    placeholder="<?php webinarignition_display( $webinar_data->webinar_qa_email_placeholder, __( "Enter Your Best Email...", 'webinarignition' ) ); ?>"

            >
        </div>
        <?php
    }
    ?>

    <input type="hidden" id="leadID" value="<?php echo empty($_GET["lid"]) ? '' :  $_GET["lid"]; ?>">

    <?php
    if ( 'chat' === $webinar_data->webinar_qa ) {
        ?>
        <div id="chatQASubmit">
            <div class="form-group wiFormGroup wiFormGroup-lg">
                <textarea id="question"
                    class="form-control wiRegForm wiFormControl"
                    placeholder="<?php webinarignition_display( $webinar_data->webinar_qa_desc_placeholder, __( "Ask Your Question Here...", 'webinarignition' ) ); ?>"
                    style="height: 80px;"
                ></textarea>
            </div>

            <button id="chatQuestion"
                class="button wiButton wiButton-lg wiButton-block addedArrow"
                style="color:#fff;border:1px solid rgba(0,0,0,0.10);background-color:<?php webinarignition_display( $webinar_data->webinar_qa_button_color, "#3E8FC7" ); ?>;"
                data-app_id="<?php echo $webinar_id; ?>"
                data-video_live_time="<?php echo ($webinar_data->webinar_date == 'AUTO') ? $lead->date_picked_and_live : $webinar_data->webinar_date . ' '. $webinar_data->webinar_start_time; ?>"
            >
               <?php webinarignition_display( $webinar_data->webinar_qa_button, __( "Submit Your Question", "webinarignition") ); ?>
            </button>
        </div>
        <?php
    } else {
        ?>
        <div class="form-group wiFormGroup wiFormGroup-lg">
            <textarea class="form-control wiRegForm wiFormControl" id="question"
                      placeholder="<?php webinarignition_display( $webinar_data->webinar_qa_desc_placeholder, __( "Ask Your Question Here...", 'webinarignition' ) ); ?>"
                      style="height: 80px;"
            ></textarea>
        </div>
        <a href="#"
           id="askQuestion"
           class="button wiButton wiButton-lg wiButton-block addedArrow"
           style="color:#fff;border:1px solid rgba(0,0,0,0.10);background-color:<?php webinarignition_display( $webinar_data->webinar_qa_button_color, "#3E8FC7" ); ?>;">
            <?php webinarignition_display( $webinar_data->webinar_qa_button, __( "Submit Your Question", "webinarignition") ); ?>
        </a>
        <?php
    }
    ?>

<?php } ?>