<?php

class WebinarignitionQA {
    private static $table_name = 'webinarignition_questions';

    public static function get_table() {
        global $wpdb;

        $table_db_name = $wpdb->prefix . 'webinarignition_questions';

        //check if table exists
        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_db_name ) );

        if ( $wpdb->get_var( $query ) !== $table_db_name ) {
            $table_db_name = $wpdb->prefix . 'webinarignition_questions_new';
        }

        return $table_db_name;
    }

    public static function get_chat_messages($app_id, $email = null, $status = null, $where = '') {
        global $wpdb;
        $table = self::get_table();

        $sql = "SELECT ID, name, email, question, status, created, type, parent_id, answer_text, attr3 FROM {$table} WHERE app_id = {$app_id}";

        if (!empty($email)) $sql .= " AND email = '{$email}'";

        if (!empty($where)) $sql .= " " . $where;

        $sql .= " ORDER BY ID ASC";

        $chat_messages = $wpdb->get_results($sql, ARRAY_A);

        if (empty($chat_messages)) {
            return [];
        }

        foreach ($chat_messages as $i => $chat_message) {
            $type = 'outgoing';

            if (!empty($chat_message['type']) && 'answer' === $chat_message['type'] && !empty($chat_message['answer_text'])) {
                $type = 'incoming';
                $chat_messages[$i]['question'] = $chat_message['answer_text'];

                if (!empty($chat_message['attr3'])) {
                    $author = $chat_message['attr3'];
                } else {
                    $author = __('Webinar Support', 'webinarignition');
                }

                $chat_messages[$i]['author'] = $author;
            }

            $chat_messages[$i]['question'] = wpautop($chat_messages[$i]['question']);
            $chat_messages[$i]['question'] = strip_tags($chat_messages[$i]['question'], '<h1><h2><h3><h4><h5><h6><p><a><span><ul><ol><li><br><strong><b>');

            $chat_messages[$i]['type'] = $type;
        }

        return $chat_messages;
    }

    public static function get_question($id) {
        global $wpdb;
        $table = self::get_table();
        $sql = "SELECT * FROM {$table} WHERE ID = {$id}";

        return $wpdb->get_row($sql, ARRAY_A);
    }

    public static function get_question_answers($id) {
        global $wpdb;
        $table = self::get_table();
        $sql = "SELECT * FROM {$table} WHERE parent_id = {$id}";

        return $wpdb->get_results($sql, ARRAY_A);
    }

    public static function create_question($data) {
        global $wpdb;
        $table = self::get_table();

        if (!empty($data['ID'])) {
            $ID = $data['ID'];
            unset($data['ID']);
            $wpdb->update( $table, $data, array( 'ID' => $ID ));
        } else {
            $wpdb->insert( $table, $data );
            $ID = $wpdb->insert_id;
        }

        return $ID;
    }

    public static function delete_answers($question_id) {
        $answers = self::get_question_answers($question_id);

        if (!empty($answers)) {
            foreach ($answers as $answer) {
                $data = [
                    'ID' => $answer['ID'],
                    'status' => 'deleted',
                ];

                WebinarignitionQA::create_question($data);
            }
        }

        return true;
    }
}