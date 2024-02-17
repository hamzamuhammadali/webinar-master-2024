<?php
defined( 'ABSPATH' ) || exit; 
/**
 * Email integration services to extract necessary fields.
 */
class AR_Integration {

            public $form_action = '';
            public $form_fields = array();
            public $mapped_fields = array();
            protected $_registered_services = array();

            public function __construct() {
                        $this->set_mapped_fields();
            }

            protected function set_mapped_fields() {
                        $this->mapped_fields = array(
                                    'ar_name' => '',
                                    'ar_lname' => '',
                                    'ar_email' => '',
                                    'ar_phone' => '',
                                    'ar_hidden' => array()
                        );
            }

            public function set_registered_services( $ar_services ) {
                        $this->_registered_services = $ar_services;
            }

            protected function detect_service() {
                        foreach ( $this->_registered_services as $_service_name => $_service ) {
                                    if ( call_user_func(array( new $_service, 'detected' ), $this->form_action) ) {
                                                return array( 'name' => $_service_name, 'class_name' => $_service );
                                    }
                        }
                        return array();
            }

            function parse_form( $data ) {
                        $form = $this->clean_data($data);
                        $this->parse_fields($form);
                        $service = $this->detect_service();
                        if ( empty($service) ) {
                                    trigger_error('No AR Services defined.', E_USER_WARNING);
                                    return;
                        } else {
                                    call_user_func_array(array( new $service['class_name'], 'map_fields' ), array( $this->form_fields, &$this->mapped_fields ));
                        }
                        return array( 'form_action' => $this->form_action, 'form_fields' => $this->mapped_fields, 'service' => $service['name'] );
            }

            protected function parse_fields( $form ) {
                        //find input fields
                        if ( preg_match('~<form\s*.*?action="([^"]+)"[^>]*>~Uis', $form, $action) && !empty($action[1]) ) {
                                    $this->form_action = html_entity_decode($action[1]);
                        }
                        if ( preg_match_all('~<(input|textarea)([^>]+)\s*/?\s*>~Uis', $form, $fields) ) {
                                    foreach ( $fields[2] as $field ) {
                                                $_fields = $this->parse_field($field);
                                                if ( !empty($_fields) ) {
                                                            $this->form_fields[$_fields['name']] = $_fields;
                                                }
                                    }
                        }
            }

            protected function parse_field( $field ) {
                        //find field attributes
                        preg_match_all('~(type|name|value)="([^"]+)"~Uis', $field, $atts);
                        if ( !empty($atts) ) {
                                    $paired = array_combine($atts[1], $atts[2]);
                                    return $paired;
                        }
                        return array();
            }

            protected function clean_data( $data ) {
                        $data = preg_replace('~(.*)<form~Uis', '<form', $data);
                        $data = preg_replace('~</form>(.*)$~Uis', '</form>', $data);
                        $data = strip_tags($data, '<form><textarea><input>');
                        return $data;
            }

}

abstract class AR_Service_Integration {

            abstract public function detected( $form_action );

            abstract protected function get_mappings();

            public function map_fields( $fields, &$mapped_fields ) {
                        $processed_fields = array();
                        foreach ( $this->get_mappings() as $field => $mapped_field ) {
                                    if ( !empty($fields[$mapped_field]) ) {
                                                $mapped_fields[$field] = $fields[$mapped_field];
                                                $processed_fields[] = $mapped_field;
                                    }
                        }
                        foreach ( $fields as $field_name => $field_atts ) {
                                    if ( !in_array($field_name, $processed_fields) )
                                                $mapped_fields['ar_hidden'][] = $field_atts;
                        }
            }

}

class AR_Aweber_Integration extends AR_Service_Integration {

            public function detected( $form_action ) {
                        return ( bool ) preg_match('~aweber\.com~Uis', $form_action);
            }

            protected function get_mappings() {
                        return array(
                                    'ar_email' => 'email',
                                    'ar_name' => 'name (awf_first)',
                                    'ar_lname' => 'name (awf_last)'
                        );
            }

}

class AR_GetResponse_Integration extends AR_Service_Integration {

            public function detected( $form_action ) {
                        return ( bool ) preg_match('~getresponse\.com~Uis', $form_action);
            }

            protected function get_mappings() {
                        return array(
                                    'ar_email' => 'email',
                                    'ar_name' => 'name',
                        );
            }

}

class AR_InfusionSoft_Integration extends AR_Service_Integration {

            public function detected( $form_action ) {
                        return ( bool ) preg_match('~infusionsoft\.com~Uis', $form_action);
            }

            protected function get_mappings() {
                        return array(
                                    'ar_email' => 'inf_field_Email',
                                    'ar_name' => 'inf_field_FirstName',
                                    'ar_lname' => 'inf_field_LastName'
                        );
            }

}

class AR_Icontact_Integration extends AR_Service_Integration {

            public function detected( $form_action ) {
                        return ( bool ) preg_match('~icontact\.com~Uis', $form_action);
            }

            protected function get_mappings() {
                        return array(
                                    'ar_email' => 'fields_email',
                                    'ar_name' => 'fields_fname',
                                    'ar_lname' => 'fields_lname'
                        );
            }

}

class AR_MailChimp_Integration extends AR_Service_Integration {

            public function detected( $form_action ) {
                        return ( bool ) preg_match('~/subscribe/post\?u=[a-z0-9]+\&id=[a-z0-9]~Uis', $form_action);
            }

            protected function get_mappings() {
                        return array(
                                    'ar_email' => 'EMAIL',
                                    'ar_name' => 'FNAME',
                                    'ar_lname' => 'LNAME'
                        );
            }

}

class AR_Default_Integration extends AR_Service_Integration {

            public function detected( $form_action ) {
                        return true;
            }

            protected function get_mappings() {
                        return array();
            }

            public function map_fields( $fields, &$mapped_fields ) {
                        $processed_fields = array();
                        foreach ( $fields as $field_name => $field_atts ) {
                                    if ( $field_atts['type'] == 'hidden' )
                                                continue;
                                    if ( stripos($field_name, 'email') !== false && empty($mapped_fields['ar_email']) ) {
                                                $mapped_fields['ar_email'] = $field_atts;
                                                $processed_fields[] = $field_name;
                                    }
                                    if ( stripos($field_name, 'name') !== false ) {
                                                if ( empty($mapped_fields['ar_name']) ) {
                                                            $mapped_fields['ar_name'] = $field_atts;
                                                            $processed_fields[] = $field_name;
                                                } else {
                                                            if ( preg_match('~(l|last).*name.*~Uis', $field_name) ) {
                                                                        $mapped_fields['ar_lname'] = $field_atts;
                                                                        $processed_fields[] = $field_name;
                                                            } else {
                                                                        if ( preg_match('~(f|first).*name.*~Uis', $field_name) ) {
                                                                                    $mapped_fields['ar_name'] = $field_atts;
                                                                                    $processed_fields[] = $field_name;
                                                                        }
                                                            }
                                                }
                                    }
                        }
                        foreach ( $fields as $field_name => $field_atts ) {
                                    if ( !in_array($field_name, $processed_fields) )
                                                $mapped_fields['ar_hidden'][] = $field_atts;
                        }
            }

}

$registered_ar_services = array(
            'Aweber' => 'AR_Aweber_Integration',
            'GetResponse' => 'AR_GetResponse_Integration',
            'InfusionSoft' => 'AR_InfusionSoft_Integration',
            'Icontact' => 'AR_Icontact_Integration',
            'MailChimp' => 'AR_MailChimp_Integration',
            'Default' => 'AR_Default_Integration'
);

global $ar_integration;

$ar_integration = new AR_Integration();
$ar_integration->set_registered_services($registered_ar_services);

// Extract AR fields
add_action('wp_ajax_nopriv_ar_extract_fields', 'ar_form_extract_fields');
add_action('wp_ajax_ar_extract_fields', 'ar_form_extract_fields');

function ar_form_extract_fields() {
    
        
            check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
            $post_input     = filter_input_array(INPUT_POST);
            global $ar_integration;
            $fields = $ar_integration->parse_form(stripslashes($post_input['form_data']));
            foreach ( $fields['form_fields']['ar_hidden'] as &$_hidden_field ) {
                        $_hidden_field = '<input type="hidden" name="' . $_hidden_field['name'] . '" value="' . $_hidden_field['value'] . '" />';
            }
            $fields['form_fields']['ar_hidden'] = implode('', $fields['form_fields']['ar_hidden']);
            echo json_encode($fields);
            die();
}
