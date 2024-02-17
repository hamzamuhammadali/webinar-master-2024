<!-- ON AIR AREA -->
<div id="questionTab" <?php if ( ! $is_support ): ?> style="display:none;" <?php endif; ?> class="consoleTabs">
    <div class="statsDashbord">
        <div class="statsTitle">
            <div class="statsTitleIcon">
                <i class="icon-question-sign icon-2x"></i>
            </div>

            <div class="statsTitleCopy">
                <h2><?php _e( 'Manage Live Questions', 'webinarignition' ) ?></h2>
                <p><?php _e( 'All questions - answered & unanswered...', 'webinarignition' ) ?></p>
            </div>

            <br clear="all"/>
        </div>
    </div>

    <div class="innerOuterContainer">
        <div class="innerContainer">

            <div class="statsQuestionsTab" style="margin-top: -73px;">

                <div class="questionTabIt questionTabSelected" id="qa-active">
                    <i class="icon-question"></i> <?php _e( 'Active Questions', 'webinarignition' ) ?> <span
                            class="labelQA" id="totalQAActive"
                            style='display: none;'><?php echo $totalQuestionsActive; ?></span>
                </div>

                <div class="questionTabIt" id="qa-done">
                    <i class="icon-check-sign"></i> <?php _e( 'Answered Questions', 'webinarignition' ) ?> <span
                            class="labelQA" id="totalQADone"
                            style='display: none;'><?php echo $totalQuestionsDone; ?></span>
                </div>

                <br clear="left"/>

            </div>

            <br clear="all"/>

            <div class="questionMainTabe" id="QAActive">

                <div class="airSwitch" style="padding-top: 0px;">

                    <?php if ( ( $webinar_data->webinar_date != 'AUTO' ) && ( ! $is_support ) ): ?>

                        <div style="padding-bottom:45px;">

                            <div class="airSwitchLeft">
                                <span class="airSwitchTitle"><?php _e( 'Enable/Disable Live Questions', 'webinarignition' ) ?></span>
                                <span class="airSwitchInfo"><?php _e( 'If set to ON, attendees will be able to send questions.', 'webinarignition' ) ?></span>
                            </div>

                            <div class="airSwitchRight">
                                <p class="field switch">
                                    <input type="hidden" id="QAToggle"
                                           value="<?php if ( $webinar_data->webinar_qa == "hide" ) {
                                               echo "hide";
                                           } else {
                                               echo "show";
                                           } ?>">
                                    <label for="radio1"
                                           class="qa-enable <?php if ( $webinar_data->webinar_qa != "hide" ) {
                                               echo "selected";
                                           } ?> "><span><?php _e( 'On', "webinarignition" ); ?></span></label>
                                    <label for="radio2"
                                           class="qa-disable <?php if ( $webinar_data->webinar_qa == "hide" ) {
                                               echo "selected";
                                           } ?>"><span><?php _e( 'Off', "webinarignition" ); ?></span></label>
                                </p>
                            </div>

                        </div>

                        <br clear="all"/>

                    <?php endif; ?>

                    <div class="airSwitchLeft">
                        <span class="airSwitchTitle"><?php _e( 'Active / Unanswered Questions', 'webinarignition' ) ?></span>
                        <span class="airSwitchInfo"><?php _e( 'Below are the questions that have come in that are yet to be answered...', 'webinarignition' ) ?></span>
                    </div>

                    <div class="airSwitchRight">

                        <a href="#" class="small disabled button secondary" style="margin-right: 0px;"><i
                                    class="icon-refresh"></i> <?php _e( 'Questions Will Auto-Update', 'webinarignition' ) ?>
                        </a>
                        <?php if ( ( ! $is_support ) ): ?>
                            <a target="_blank"
                               href="<?php echo $webinar_data->webinar_permalink . '?csv_key=' . $webinar_data->csv_key; ?>"
                               class="small button secondary" style="margin-right: 0px;"><i class="icon-file-text"></i>
                                CSV</a>
                        <?php endif; ?>

                    </div>

                    <br clear="all"/>

                </div>

                <?php include "partials/answerQuestionFormInitial.php"; ?>

                <div id="active_questions" class="questionsBlock">

                    <?php foreach ( $questionsActive as $questionsActive ) { ?>

                        <!-- QUESTION BLOCK -->
                        <div class="questionBlockWrapper questionBlockWrapperActive"
                             qa_lead="<?php echo $questionsActive->ID; ?>"
                             id="QA-BLOCK-<?php echo $questionsActive->ID; ?>">

                            <div class="questionBlockQuestion">
                                <span class="questionTimestamp"><?php echo $questionsActive->created; ?><?php echo ! empty( $questionsActive->webinarTime ) ? '(' . $questionsActive->webinarTime . __( ' minutes into the webinar)', 'webinarignition' ) : ''; ?></span>

                                <p style='padding: 10px; background-color: #eee; width: 100%;border-radius: 7px;'>
                                    <span class="questionBlockText"><?php echo $questionsActive->question; ?></span>
                                    <br>
                                    <span class='questionBlockAuthor' >
                                        <?php echo $questionsActive->name; ?> -
                                        <span
                                                data-toggle='tooltip'
                                                data-placement='top'
                                                title='Search leads table'
                                                class='radius secondary label qa-lead-search'
                                        >
                                            <?php echo $questionsActive->email; ?>
                                        </span>
                                    </span>
                                </p>

                                <?php if ( ( $questionsActive->attr4 == 'hold' && $questionsActive->attr2 == $current_user->ID ) ): ?>
                                    <span class="questionOnHold green bold"> <?php _e( "You're answering this question...", 'webinarignition' ) ?></span>
                                <?php endif; ?>

                                <?php if ( ( $questionsActive->attr4 == 'hold' && $questionsActive->attr2 != $current_user->ID && ! empty( $questionsActive->attr5 ) ) ): ?>
                                    <span class="questionOnHold green bold"> <?php echo $questionsActive->attr5; ?><?php _e( 'is answering this question...', 'webinarignition' ) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="questionActions">

                                <?php if ( ! $is_support ): ?>

                                    <div class="questionBlockIcons qbi-remove"
                                         qaID="<?php echo $questionsActive->ID; ?>">
                                        <i data-toggle="tooltip" data-placement="top" title="<?php _e( 'Delete question', "webinarignition" ); ?>"
                                           class="icon-remove icon-large"></i>
                                    </div>

                                <?php endif; ?>

                                <?php if ( ( $questionsActive->attr4 != 'hold' ) || ( $questionsActive->attr4 == 'hold' && $questionsActive->attr2 == $current_user->ID ) ): ?>

                                    <div class="questionBlockIcons qbi-reply">
                                        <a class="answerAttendee" data-toggle="tooltip" data-placement="top"
                                           title="<?php _e( 'Respond to attendee question', "webinarignition" ); ?>"
                                           data-questionid="<?php echo $questionsActive->ID; ?>"
                                           data-attendee-name="<?php echo $questionsActive->name; ?>"
                                           data-attendee-email="<?php echo $questionsActive->email; ?>"><i
                                                    class="icon-comments icon-large"></i></a>
                                    </div>

                                <?php endif; ?>

                                <br clear="left"/>

                            </div>

                            <br clear="all"/>

                        </div>
                        <!-- END OF QUESTION BLOCK -->

                    <?php } ?>

                </div>

            </div>

            <div class="questionMainTabe" id="QADone" style="display:none;">

                <div class="airSwitch" style="padding-top: 0px;">

                    <div class="airSwitchLeft">
                        <span class="airSwitchTitle"><?php _e( 'Answered Questions', 'webinarignition' ) ?></span>
                        <span class="airSwitchInfo"><?php _e( 'Below are all the answered questions...', 'webinarignition' ) ?></span>
                    </div>

                    <br clear="all"/>

                </div>

                <?php include "partials/answerQuestionFormMore.php"; ?>

                <div id="answered_questions" class="questionsBlock">

                    <?php foreach ( $questionsDone as $questionDone ) {
                        ?>

                        <!-- QUESTION BLOCK -->
                        <?php include "partials/answeredQuestion.php"; ?>
                        <!-- END OF QUESTION BLOCK -->

                    <?php } ?>

                </div>

            </div>

        </div>
    </div>
</div>

<script type="text/html" id="qstn_answer_email_body">
    <?php echo $webinar_data->qstn_answer_email_body; ?>
</script>


<script>
    (function ($) {

        $(document.body).on('click', '.email-qa-enable', function() {
            var parent = jQuery(this).parents('.switch');
            parent.find('.email-qa-disable').removeClass('selected');
            $(this).addClass('selected');
            $("#EmailQAToggle").val("on");
            $("#EmailQAContainer").show();
        });

        $(document.body).on('click', '.email-qa-disable', function() {
            var parent = $(this).parents('.switch');
            parent.find('.email-qa-enable').removeClass('selected');
            $(this).addClass('selected');
            $("#EmailQAToggle").val("off");
            $("#EmailQAContainer").hide();
        });

        $(document.body).on('click', '.email-qaMore-enable', function() {
            var parent = jQuery(this).parents('.switch');
            parent.find('.email-qaMore-disable').removeClass('selected');
            $(this).addClass('selected');
            $("#EmailQAMoreToggle").val("on");
            $("#EmailQAMoreContainer").show();
        });

        $(document.body).on('click', '.email-qaMore-disable', function() {
            var parent = $(this).parents('.switch');
            parent.find('.email-qaMore-enable').removeClass('selected');
            $(this).addClass('selected');
            $("#EmailQAMoreToggle").val("off");
            $("#EmailQAMoreContainer").hide();
        });


    })(jQuery);

    jQuery(document).ready(function ($) {
        var overLay = $('#overlay'),
            answerForm = $('#answerForm'),
            answerFormContainer = $('#answerFormContainer'),
            closeAnswerForm = $('#closeAnswerForm');

        var answerMoreForm = $('#answerMoreForm'),
            answerMoreFormContainer = $('#answerMoreFormContainer'),
            closeAnswerMoreForm = $('#closeAnswerMoreForm');


        // QA Tabs
        $('#qa-done').on('click', function () {
            $(".questionTabIt").removeClass("questionTabSelected");
            $(this).addClass("questionTabSelected");
            $("#QAActive").hide();
            $("#QADone").show();
            return false;
        });

        $('#qa-active').on('click', function () {
            $(".questionTabIt").removeClass("questionTabSelected");
            $(this).addClass("questionTabSelected");
            $("#QADone").hide();
            $("#QAActive").show();
            return false;
        });

        $('#answerContent').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $('#answerText').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        if ($('#answerMoreContent').length) {
            $('#answerMoreContent').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        if ($('#answerMoreText').length) {
            $('#answerMoreText').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }


        $('.qa-lead-search').on('click', function () {
            var $getEmail = $(this).text();
            oTable.fnFilter($getEmail);
            $(".consoleTabs").hide();
            $("#leadTab").show();
            $('.dashTopBTN').removeClass("success");
            $('.dashTopBTN').addClass("secondary");
            $('.dashTopBTN').addClass("lc-btn");
            $("#leadTabBTN").addClass("secondary");
            $("#leadTabBTN").removeClass("lc-btn");
            $("#leadTabBTN").addClass("success");
            return false;
        });

        setInterval(function () {
            $.ajax({
                type: "post",
                url: window.ajaxurl,
                data: {
                    id: window.webinarId,
                    action: "webinarignition_get_questions",
                    security: window.wiRegJS.ajax_nonce,
                    webinar_type: window.webinarType,
                    limit: 1000,
                    offset: 0,
                    is_support: window.is_support ? 1 : ''
                },
                success: function (response) {
                    if (response && response.data && response.data.active_questions) {
                        jQuery("#active_questions").html(response.data.active_questions);
                    }

                    if (response && response.data && response.data.answered_questions) {
                        jQuery("#answered_questions").html(response.data.answered_questions);
                    }

                    $("#dashTotalQ").text(response.data.total_questions);
                    $("#dashTotalActiveQ").text(response.data.total_active_questions);
                }
            });
        }, 10000);

        var markQuestionAsAnswered = function (questionId) {
            $("#QA-BLOCK-" + questionId).detach().appendTo("#answered_questions");
            $("#qbi-answer-" + questionId).remove();

            $totalActive = $("#totalQAActive").text();
            $totalActive = parseInt($totalActive);
            $totalDone = $("#totalQADone").text();
            $totalDone = parseInt($totalDone);
            $dashTotalActiveQ = $("#dashTotalActiveQ").text();
            $dashTotalActiveQ = parseInt($dashTotalActiveQ);

            if ($totalActive != 0) {

                $totalActive = $totalActive - 1;
                $totalDone = $totalDone + 1;
                $("#totalQAActive").text($totalActive);
                $("#totalQADone").text($totalDone);
                $dashTotalActiveQ = $dashTotalActiveQ - 1;
                $("#dashTotalActiveQ").text($dashTotalActiveQ);

            }
        }

        let objectifyForm = function (formArray) {
            let returnObj = {};
            for (let i = 0, len = formArray.length; i < len; i++) {
                returnObj[formArray[i]['name']] = formArray[i]['value'];
            }
            return returnObj;
        };

        let holdOrReleaseConsoleQuestion = function (questionId, hold) {
            let questionData = {};
            questionData.action = 'webinarignition_hold_or_release_console_question';
            questionData.webinarId = window.webinarId;
            questionData.security = window.wiRegJS.ajax_nonce;
            questionData.questionId = questionId;
            questionData.hold = hold;
            questionData.supportName = "<?php echo $current_user->display_name; ?>";
            questionData.supportId = <?php echo $current_user->ID; ?>;

            $.post(window.ajaxurl, questionData);
        };

        closeAnswerForm.on('click', function () {
            $('.questionOnHold').remove();
            answerFormContainer.hide();
            let questionId = $('#questionId').val();
            holdOrReleaseConsoleQuestion(questionId, false);
        });

        closeAnswerMoreForm.on('click', function () {
            answerMoreFormContainer.hide();
        });

        answerForm.on('submit', function (event) {
            event.preventDefault();

            overLay.show();

            let formData = answerForm.serializeArray();
            let answerData = objectifyForm(formData);

            var answer = $('#answerContent').summernote('code');
            var answerText = $('#answerText').summernote('code');

            answer = answer.replace("{ANSWER}", answerText);

            answerData.action = 'webinarignition_answer_attendee_question';
            answerData.webinarId = window.webinarId;
            answerData.security = window.wiRegJS.ajax_nonce;
            answerData.answer = answer;
            answerData.answerText = answerText;
            answerData.attendeeQuestion = $('#attendeeQuestion').val();

            if (!answerData.answerText) return;

            $.post(window.ajaxurl, answerData, function (response) {
                markQuestionAsAnswered(answerData.questionId);
                jQuery('#answerFormContainer').hide('slow', function () {
                    if (response.success && response.success === true) {
                        alert('<?php _e( 'Your answer was succesfully sent', 'webinarignition' ) ?>');
                    }
                    overLay.hide();
                    $('#answerContent').summernote('reset');
                    $('#answerText').summernote('reset');
                });
            });
        });

        if (answerMoreForm.length) {
            answerMoreForm.on('submit', function (event) {
                event.preventDefault();

                overLay.show();

                let formData = answerMoreForm.serializeArray();
                let answerData = objectifyForm(formData);

                var answer = $('#answerMoreContent').summernote('code');
                var answerText = $('#answerMoreText').summernote('code');

                answer = answer.replace("{ANSWER}", answerText);

                answerData.action = 'webinarignition_answer_attendee_question';
                answerData.webinarId = window.webinarId;
                answerData.security = window.wiRegJS.ajax_nonce;
                answerData.answer = answer;
                answerData.answerText = answerText;
                answerData.attendeeQuestion = $('#attendeeMoreQuestion').val();
                answerData.isAnswerMore = 'on';

                if (!answerData.answerText) return;

                $.post(window.ajaxurl, answerData, function (response) {
                    jQuery('#answerMoreFormContainer').hide('slow', function () {
                        if (response.success && response.success === true) {
                            alert('<?php _e( 'Your answer was succesfully sent', 'webinarignition' ) ?>');
                        }
                        overLay.hide();
                        $('#answerMoreContent').summernote('reset');
                        $('#answerMoreText').summernote('reset');
                    });
                });
            });
        }

        if ($('a.answerMoreAttendee')) {
            $('body').on("click", 'a.answerMoreAttendee', function () {
                var attendeeEmail = $(this).data('attendeeEmail'),
                    attendeeName = $(this).data('attendeeName'),
                    questionId = $(this).data('questionid'),
                    question = $(this).parents('.questionBlockWrapperDone').find('.questionBlockContainer').find('.questionBlockQuestionText').html();

                $('#answerMoreText').summernote('reset');
                $('#answerMoreContent').summernote('reset');

                var data = {
                    action: "webinarignition_get_answer_template",
                    webinarId: window.webinarId,
                    security: window.wiRegJS.ajax_nonce
                };

                $('#questionMoreId').val(questionId);
                $('#attendeeMoreEmail').val(attendeeEmail);
                $('#attendeeMoreQuestion').val(question);
                $('.attendeeMoreName').text(attendeeName);

                answerMoreFormContainer.show();

                $.post(window.ajaxurl, data, function (response) {
                    var HTMLstring = response['data']['template'];
                    HTMLstring = HTMLstring.replace("{ATTENDEE}", attendeeName);
                    HTMLstring = HTMLstring.replace("{QUESTION}", question),
                        HTMLstring = HTMLstring.replace("{SUPPORTNAME}", "<?php echo $current_user->display_name; ?>");

                    $('#answerMoreContent').summernote('pasteHTML', HTMLstring);
                    $('#answerMoreText').summernote('focus');

                    $('#answerMoreText').val(questionId);
                    $('#attendeeMoreEmail').val(attendeeEmail);

                    $('#attendeeMoreName').text(attendeeName);
                    answerMoreFormContainer.show();
                });
            });
        }

        $('body').on("click", 'a.answerAttendee', function () {
            var attendeeEmail = $(this).data('attendeeEmail'),
                attendeeName = $(this).data('attendeeName'),
                questionId = $(this).data('questionid'),
                question = $(this).parents('.questionBlockWrapper').find('.questionBlockQuestion').find('.questionBlockText').html();

            $('#answerContent').summernote('reset');
            $('#answerText').summernote('reset');
            $('.questionOnHold').remove();

            var data = {
                action: "webinarignition_get_answer_template",
                webinarId: window.webinarId,
                security: window.wiRegJS.ajax_nonce
            };

            $('#questionId').val(questionId);
            $('#attendeeEmail').val(attendeeEmail);
            $('#attendeeQuestion').val(question);
            $('.attendeeName').text(attendeeName);

            answerFormContainer.show();
            holdOrReleaseConsoleQuestion(questionId, true);

            $.post(window.ajaxurl, data, function (response) {
                var HTMLstring = response['data']['template'];
                HTMLstring = HTMLstring.replace("{ATTENDEE}", attendeeName);
                HTMLstring = HTMLstring.replace("{QUESTION}", question),
                    HTMLstring = HTMLstring.replace("{SUPPORTNAME}", "<?php echo $current_user->display_name; ?>");

                $('#answerContent').summernote('pasteHTML', HTMLstring);
                $('#answerText').summernote('focus');

                $('#questionId').val(questionId);
                $('#attendeeEmail').val(attendeeEmail);

                $('#attendeeName').text(attendeeName);
                answerFormContainer.show();
                holdOrReleaseConsoleQuestion(questionId, true);
            });
        });



        <?php  if( ! $is_support ): ?>

        function toggleQA(status) {

            var data = {
                action: "webinarignition_set_q_a_status",
                webinarId: window.webinarId,
                security: window.wiRegJS.ajax_nonce,
                status: status ? 'show' : 'hide'
            };

            $.post(window.ajaxurl, data);

        }

        // Toggle Q&A
        $(".qa-enable").on('click', function () {

            var parent = $(this).parents('.switch');
            $('.qa-disable', parent).removeClass('selected');
            $(this).addClass('selected');
            $("#QAToggle").val("on");

            toggleQA(true);

        });

        $(".qa-disable").on('click', function () {

            var parent = $(this).parents('.switch');
            $('.qa-enable', parent).removeClass('selected');
            $(this).addClass('selected');
            $("#QAToggle").val("off");

            toggleQA(false);

        });

        // Delete Question
        $('body').on("click", '.qbi-remove, .qbi-removeDone', function () {

            var deleteConfirm = confirm("<?php _e( 'Are you sure you would like to delete this question?', 'webinarignition' ) ?>");

            if (!deleteConfirm) {
                return;
            }

            closeAnswerForm.trigger("click");

            var thisElem = $(this);
            var $ID = thisElem.attr("qaID");

            var data = {
                action: "webinarignition_delete_question",
                id: "" + $ID + "",
                security: window.wiRegJS.ajax_nonce
            };

            $.post(window.ajaxurl, data, function (results) {

                $("#QA-BLOCK-" + $ID).fadeOut("fast");

                if (thisElem.hasClass('qbi-remove')) {

                    $totalActive = $("#totalQAActive").text();
                    $totalActive = parseInt($totalActive);
                    $totalQ = $("#dashTotalQ").text();
                    $totalQ = parseInt($totalQ);
                    $dashTotalActiveQ = $("#dashTotalActiveQ").text();
                    $dashTotalActiveQ = parseInt($dashTotalActiveQ);

                    if ($totalActive != 0) {

                        $totalActive = $totalActive - 1;
                        $("#totalQAActive").text($totalActive);
                        $totalQ = $totalQ - 1;
                        $("#dashTotalQ").text($totalQ);
                        $dashTotalActiveQ = $dashTotalActiveQ - 1;
                        $("#dashTotalActiveQ").text($dashTotalActiveQ);

                    }

                }

                if (thisElem.hasClass('qbi-removeDone')) {

                    $totalQADone = $("#totalQADone").text();
                    $totalQADone = parseInt($totalQADone);
                    $totalQ = $("#dashTotalQ").text();
                    $totalQ = parseInt($totalQ);

                    if ($totalQADone != 0) {

                        $totalQADone = $totalQADone - 1;
                        $("#totalQADone").text($totalQADone);
                        $totalQ = $totalQ - 1;
                        $("#dashTotalQ").text($totalQ);

                    }

                }

            });

            return false;

        });

        <?php endif; ?>

    });

</script>
