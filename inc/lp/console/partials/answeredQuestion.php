<?php
/**
 * @var $webinar_data
 * @var $questionDone
 * @var $is_support
 */

$answers = [];

if (!WebinarignitionPowerups::is_two_way_qa_enabled($webinar_data)) {
    $answer = '';

    if (!empty($questionDone->answer)) $answer = $questionDone->answer;
    if (!empty($questionDone->answer_text)) $answer = $questionDone->answer_text;

    $answer_author = '';

    if (!empty($questionDone->attr3)) {
        $answer_author = $questionDone->attr3;
    }

    $answers[] = [
        'answer' => $answer,
        'author' => $answer_author,
    ];
} else {
    $q_answers = WebinarignitionQA::get_question_answers($questionDone->ID);

    if (empty($q_answers)) {
        $answer = '';

        if (!empty($questionDone->answer)) $answer = $questionDone->answer;
        if (!empty($questionDone->answer_text)) $answer = $questionDone->answer_text;

        $answer_author = '';

        if (!empty($questionDone->attr3)) {
            $answer_author = $questionDone->attr3;
        }

        $answers[] = [
            'answer' => $answer,
            'author' => $answer_author,
        ];
    } else {
        foreach ($q_answers as $answer_array) {
            $answer = '';

            if (!empty($answer_array['answer'])) $answer = $answer_array['answer'];
            if (!empty($answer_array['answer_text'])) $answer = $answer_array['answer_text'];

            $answer_author = '';

            if (!empty($answer_array['attr3'])) {
                $answer_author = $answer_array['attr3'];
            }

            $answers[] = [
                'answer' => $answer,
                'author' => $answer_author,
            ];
        }
    }
}

//$answer = '';
//
//if (!empty($questionDone->answer)) $answer = $questionDone->answer;
//if (!empty($questionDone->answer_text)) $answer = $questionDone->answer_text;
?>

<!-- QUESTION BLOCK -->
<div
    class="questionBlockWrapper questionBlockWrapperDone"
    qa_lead="<?php echo $questionDone->ID; ?>"
    id="QA-BLOCK-<?php echo $questionDone->ID; ?>"
>

    <div class="questionBlockContainer">
        <div class="questionBlockByTitle">
            <h3><?php _e( 'Question', 'webinarignition' ) ?></h3>

            <p>
                <?php _e( 'by', 'webinarignition' ) ?>
                <strong><?php echo $questionDone->name; ?></strong>
                <?php _e( 'at', 'webinarignition' ) ?>
                <strong><?php echo $questionDone->created; ?></strong> <br>

                <small>
                    <strong><?php echo $questionDone->email; ?></strong>
                    <?php echo ! empty( $questionDone->webinarTime ) && false ? ' <br>(' . $questionDone->webinarTime . __( ' minutes into the webinar)', 'webinarignition' ) : ''; ?>
                </small>
            </p>
        </div>

        <div class="questionBlockQuestionText"><?php echo $questionDone->question; ?></div>


        <div class="questionBlockAnswers">
            <div class="questionBlockByTitle">
                <h3>
                    <?php _e( 'Answers', 'webinarignition' ) ?>
                </h3>
            </div>
            <?php
            if (!empty($answers)) {
                foreach ($answers as $answer) {
                    ?>
                    <div class="questionBlockAnswer">
                        <?php
                        if (!empty($answer['author'])) {
                            ?>
                            <div class="questionBlockByTitle">
                                <p>
                                    <?php _e( 'by', 'webinarignition' ) ?>
                                    <strong><?php echo $answer['author']; ?></strong>
                                </p>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="questionBlockQuestionText">
                            <?php echo $answer['answer']; ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <div class="questionActions">
        <?php if ( ! $is_support ): ?>
            <div class="questionBlockIcons qbi-removeDone"
                 qaID="<?php echo $questionDone->ID; ?>">
                <i data-toggle="tooltip" data-placement="top" title="<?php _e( 'Delete question', "webinarignition" ); ?>"
                   class="icon-remove icon-large"></i>
            </div>
        <?php endif; ?>

        <?php
        if ($webinar_data->webinar_date !== 'AUTO' && 'chat' === $webinar_data->webinar_qa && WebinarignitionPowerups::is_two_way_qa_enabled($webinar_data)) {
            ?>

            <div class="questionBlockIcons qbi-reply">
                <a class="answerMoreAttendee" data-toggle="tooltip" data-placement="top"
                   title="<?php _e( 'Respond to attendee question', "webinarignition" ); ?>"
                   data-questionid="<?php echo $questionDone->ID; ?>"
                   data-attendee-name="<?php echo $questionDone->name; ?>"
                   data-attendee-email="<?php echo $questionDone->email; ?>"><i
                            class="icon-comments icon-large"></i></a>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<!-- END OF QUESTION BLOCK -->