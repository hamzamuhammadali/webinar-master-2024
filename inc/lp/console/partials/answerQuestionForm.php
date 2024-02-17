<?php
/**
 *
 */

$form_id = '';

if (empty($answer_initial)) {
    $form_id = 'More';
}
?>

<div id="answer<?php echo $form_id; ?>FormContainer" style="display:none;">
    <div class="row">
        <div class="col-md-12">
            <form id="answer<?php echo $form_id; ?>Form">
                <div class="form-group">
                    <label for="attendeeName"><h4><?php _e( 'Your Reply To', 'webinarignition' ) ?> <span
                                class="attendeeName"></span></h4></label>
                    <img data-toggle='tooltip' data-placement='top' title='<?php _e( 'Close answer form', "webinarignition" ); ?>'
                         id="closeAnswer<?php echo $form_id; ?>Form" style="float:right; cursor:pointer;"
                         src="<?php echo $assets; ?>images/close.png" width="20" height="20">
                </div>

                <div class="form-group">
                    <label for="attendee<?php echo $form_id; ?>Question"><span
                            class="attendeeMoreName"></span><?php _e( "'s Question", 'webinarignition' ) ?>
                    </label>
                    <textarea name="attendeeQuestion" class="form-control" id="attendee<?php echo $form_id; ?>Question" rows="3"
                              disabled></textarea>
                </div>

                <div class="form-group">
                    <label for="answer<?php echo $form_id; ?>Text"><?php _e( 'Your Answer', 'webinarignition' ) ?></label>
                    <textarea name="answerText" class="form-control" id="answer<?php echo $form_id; ?>Text" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php _e( 'Submit Answer', 'webinarignition' ) ?>
                </button>

                <hr>

                <?php
                $send_answer_via_email = 'off';
                if ('chat' === $webinar_data->webinar_qa && !WebinarignitionPowerups::is_two_way_qa_enabled($webinar_data)) {
                    $webinar_data->webinar_qa = 'we';
                }

                if ($webinar_data->webinar_date === 'AUTO' || !WebinarignitionPowerups::is_two_way_qa_enabled($webinar_data)) {
                    $send_answer_via_email = 'on';
                    $always_email = true;
                }

                if (!empty($always_email) || 'chat' !== $webinar_data->webinar_qa) {
                    $send_answer_via_email = 'on';
                }
                ?>

                <div<?php echo !empty($always_email) ? ' style="display:none;"' : ''; ?>>
                    <div class="airSwitchLeft">
                        <span class="airSwitchTitle"><?php _e( 'Send answer also via email', 'webinarignition' ) ?></span>
                        <span class="airSwitchInfo"><?php _e( 'If set to ON, attendees will get your answers vie email.', 'webinarignition' ) ?></span>
                    </div>

                    <div class="airSwitchRight">
                        <p class="field switch">
                            <input type="hidden" id="EmailQA<?php echo $form_id; ?>Toggle" name="emailQAEnabled" value="<?php echo $send_answer_via_email ?>">
                            <label for="email-qa-enable" class="email-qa<?php echo $form_id; ?>-enable<?php echo $send_answer_via_email === 'on' ? ' selected' : ''; ?>">
                                <span>On</span>
                            </label>
                            <label for="email-qa-disable" class="email-qa<?php echo $form_id; ?>-disable<?php echo $send_answer_via_email === 'off' ? ' selected' : ''; ?>">
                                <span>Off</span>
                            </label>
                        <div style="display: table;content: ' ';clear: both;"></div>
                        </p>
                    </div>

                    <div style="display: table;content: ' ';clear: both;"></div>
                </div>

                <div id="EmailQA<?php echo $form_id; ?>Container"<?php echo $send_answer_via_email === 'off' ? ' style="display:none;"' : ''; ?>>
                    <h3><?php _e( 'Email Answer', 'webinarignition' ) ?></h3>

                    <div class="form-group">
                        <label for="answer<?php echo $form_id; ?>Subject"><?php _e( 'Subject', 'webinarignition' ) ?></label>
                        <input type="text" class="form-control" name="subject" id="answer<?php echo $form_id; ?>Subject"
                               value="<?php _e( 'Your questions to the webinar ' . $webinar_data->webinar_desc, 'webinarignition' ) ?>">
                    </div>

                    <div class="form-group">
                        <label for="answer<?php echo $form_id; ?>Content"><?php _e( 'Answer Body', 'webinarignition' ) ?></label>
                        <p>
                            <small>
                                <?php _e( 'Do not remove <strong>{ANSWER}</strong> placeholder from template, it will be replaced with <strong>"Your Answer"</strong> field content.', 'webinarignition' ) ?>
                            </small>

                        </p>
                        <textarea name="answer" class="form-control" id="answer<?php echo $form_id; ?>Content" rows="3"></textarea>

                    </div>
                </div>

                <input id="attendee<?php echo $form_id; ?>Email" type="hidden" name="attendeeEmail">
                <input id="question<?php echo $form_id; ?>Id" type="hidden" name="questionId">
                <input type="hidden" name="supportName" value="<?php echo $current_user->display_name; ?>">
                <input type="hidden" name="supportId" value="<?php echo $current_user->ID; ?>">
                <?php
                if (empty($answer_initial)) {
                    ?>
                    <input type="hidden" name="isAnswerMore" value="on">
                    <?php
                }
                ?>
            </form>
        </div>
    </div>
</div>