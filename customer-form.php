<?php
/*

 * Plugin Name: woo customer form

 * Plugin URI: http://desarrollador.ga

 * Description: plugin to customers

 * Version: 1.0.0

 * Author: Juan Amaguaña

 * Author URI: http://desarrollador.ga

 * License: GPL2
 * 
*/
date_default_timezone_set('America/Guayaquil');
include ( plugin_dir_path( __FILE__ ) . 'html.php');
define( 'WCCUSTOMERSFORM__PLUGIN_DIR', plugins_url("/", __FILE__) );

class Wp3DEngine {

    private $myThemeParams = array(
        'plugiPath' => WCCUSTOMERSFORM__PLUGIN_DIR
    );

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'addStyles'));
        // add_action('wp_enqueue_scripts', array($this, 'addScripts'));
        add_shortcode('renderModel', array($this, 'renderModel'));
        add_action('wp_enqueue_scripts', array($this, 'addBotScripts'));

        // register the ajax action for authenticated users
        add_action('wp_ajax_mark_message_as_read', array($this, 'mark_message_as_read'));
        // register the ajax action for unauthenticated users
        add_action('wp_ajax_nopriv_mark_message_as_read', array($this, 'mark_message_as_read'));


        add_filter('wp_mail_content_type', array($this, 'mailContent'));
    }

    /**
     * Añade la hoja CSS de este plugin a la cola de estilos de WordPress.
     */
    public function addStyles() {
        wp_enqueue_style('renderModel-styles', plugins_url("/css/styles.css", __FILE__), array(), false, false);
        // wp_enqueue_style('renderModel-styles-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css', array(), false, false);
    }


    /**
     * Añade la hoja JS de este plugin a la cola de estilos de WordPress.
     */
    public function addScripts() {
        
    }

    public function addBotScripts() {
        wp_enqueue_script('renderModel-scripts', plugins_url("/js/script.js", __FILE__), array(), false, true);
        wp_enqueue_script('renderModel-scripts-vuejs', "https://unpkg.com/vue@3", array('jquery'), false, false);
        wp_add_inline_script( 'renderModel-scripts', 'var myThemeParams = ' . wp_json_encode( $this->myThemeParams ), 'before' );
    }


    public function renderModel() {
        $html = new Html();
        $render = $html->renderModel();
        return $render;
    }



    /**
     * JAVASCRIPT
     */

    // handle the ajax request
    public function mark_message_as_read() {
        // $message_id = $_REQUEST['email'];
        $email =  $_REQUEST['email'];
        $username = $_REQUEST['email']; //$_REQUEST['username'];
        $password = $_REQUEST['password'];
        $firstname = $_REQUEST['firstname'];
        $lastname = $_REQUEST['lastname'];

        $user_id = wc_create_new_customer( $email, $username, $password );
        $result = "";
        if(is_wp_error($user_id)){
            $error = $user_id->get_error_message();
            //handle error here
            wp_send_json_error([ 'error' => $error ]);
        }else{
            update_user_meta( $user_id, "first_name", $firstname );
            update_user_meta( $user_id, "last_name", $lastname );
            // in the end, returns success json data
            $this->sendEmail($email);
            wp_send_json_success([ 'message' => '¡Listo!. Esperamos te sean muy útiles los primeros capítulos del libro.'  ]);
        }
        // $this->sendEmail();
    }

    public function mailContent() {
        return 'text/html';
    }

    public function sendEmail($email){
        $to = $email;
        $subject = 'Mi otro BRaiN - ¡Muchas gracias por descargar la muestra gratuita!';
        $body = $this->emailPdf();
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail( $to, $subject, $body, $headers );
    }

    public function emailPdf(){
        $html = '<div style="width:100%"><div style="text-align:center;background:#d64e14;background:linear-gradient(172deg,rgba(214,78,20,1) 58%,rgba(255,255,255,1) 58%);padding:30px"><br><img src="https://hapn.biz/wp-content/uploads/2022/10/mail-logo-1.png"><br><p style="text-align:center;color:#fff;font-size:14px;font-weight:600"><br>¡Muchas Gracias por tu Interés!</p><img src="https://s3.us-east-2.amazonaws.com/admin.kfc.staging/brands/hapn/book-hapn-min.png" width="40%"></div><div style="text-align:center;background:#fff"><div style="text-align:justify;width:70%;margin:auto"><p>Tienes ahora en tu poder los primeros capítulos del libro “Mi otro BRaiN”, que es una guía para impulsar tu carrera incorporando a tu día a día un asistente de pensamiento basado en GPT3, una de las herramientas de inteligencia artificial más avanzadas que existen a la fecha.<p><p>Son tiempos muy interesantes, pues como podrás ver en el libro, ¡no necesitas ningún conocimiento técnico para poder lograrlo!<p><p>Así que espero le saques todo el provecho a esta herramienta, y veas como tu forma de aprender, pensar y trabajar se impulsan tremendamente.<p><p>El libro completo en versión digital se publicará a finales de enero de 2023 y ahora está en pleno proceso de producción. Así que si quieres aprovechar de haber conocido de él antes de su publicación, puedes hacer aquí una compra anticipada y obtener un descuento del 30% del precio que tendrá en el lanzamiento. A cambio podrás descargarte dos capítulos adicionales que te permitirán conocer cómo empezar a usar GPT3 eficazmente hoy mismo! En la fecha de publicación recibirás el libro completo en esta dirección de correo electrónico en la que ahora lees este mensaje.<p><p>¡Muchas gracias nuevamente por tu interés y que lo disfrutes!<br>Jorge<p></div><br><br><img src="https://hapn.biz/wp-content/uploads/2022/10/mail-firma.png"><br><br><br><div style="color:#fff;background:#ff6a00;padding:10px;border-radius:10px;text-decoration:none;width:250px;margin:auto">'.do_shortcode('[wp_otfd id="1" title="Descargar Capítulos Iniciales" class="btn-download-pdf"]').'</div><br><br></div></div>';
        return $html;
    }

}

new Wp3DEngine();