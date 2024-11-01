<?php
/*
Plugin Name: Shiftt Notify (بىلوگىر ئەسكەرتكۈچى)
Plugin URI: https://cn.wordpress.org/plugins/Shiftt-notify/
Description: ۋوردپىرىس ئىنكاسلىرىنى ئۈندىدار سالونىدا ئەسكەرتىش
Version: 1.0
Author: ShiftBloger
Author URI: https://www.shiftt.cn/
*/

add_action( 'admin_menu', 'shiftt_baxkurux_tizimliki' );

function shiftt_baxkurux_tizimliki() {
    add_submenu_page(
        'tools.php',
        'ئەسكەرتكۈچى تەڭشىكى',
        'ئەسكەرتكۈچى',
        'manage_options', 
        'shiftt-setting',
        'shiftt_notify',
        1000 
    ); 
}

add_action( 'admin_init', 'register_shiftt_setting' );

function register_shiftt_setting() {
    register_setting('shifttnotify-group', 'shiftt_notify_appid');
    register_setting('shifttnotify-group', 'shiftt_notify_appsecret');
    register_setting('shifttnotify-group', 'shiftt_notify_wxtemplateid');
    register_setting('shifttnotify-group', 'shiftt_notify_author_openid');
}

function shiftt_notify() { ?>  
    <div class="wrap">
        
        <h1>بىلوگىر ئەسكەرتكۈچى تەڭشىكى</h1>
        <br/>
        <p>ئەڭ ئاۋال سىزدە دەلىللەنگەن ئۈندىدار سالونى بولىشى كىرەك</p>
        <p>سالون ئارقا سۇپىسى تەڭشىكى ئىچىدىن سالون ئاساسىي ئۇچۇرىنى ئىلىپ چىقىڭ</p>
        <p>مەزكۇر مۇلازىمىتىر ئادىرىسىنى سالون ئاق تىزىملىكىگە كىرگۈزۈڭ </p>   
        <br/>
        
        <?php 
        if (!empty($_REQUEST['settings-updated'])) {
            echo '<div id="message" class="updated notice is-dismissible"><p><strong>تەڭشەك ساقلاندى</strong></p></div>';
        } ?>

        <form method="post" name="shiftt_set" action="options.php">   
        <?php settings_fields('shifttnotify-group'); ?>
        <?php do_settings_sections('shifttnotify-group'); ?>
        
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="shiftt_notify_appid"> AppID </label></th>
                    <td>
                        <input type="text" name="shiftt_notify_appid" class="regular-text" 
                            value="<?php echo esc_attr( get_option('shiftt_notify_appid') ); ?>">
                        <p dir="rtl" style="text-align: left;" class="description">
                    		سالون ئارقا سۇپىسىدكى AppID نى كىرگۈزۈڭ
                		</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="shiftt_notify_appsecret"> APPsecret </label></th>
                    <td>
                        <input type="text" name="shiftt_notify_appsecret" class="regular-text" 
                            value="<?php echo esc_attr( get_option('shiftt_notify_appsecret') ); ?>">
                        <p dir="rtl" style="text-align: left;" class="description" >
                    		سالون ئارقا سۇپىسىدكى APPsecret نى كىرگۈزۈڭ
                		</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="shiftt_notify_wxtemplateid"> TemplateID </label></th>
                    <td>
                        <input type="text" name="shiftt_notify_wxtemplateid" class="regular-text"
                            value="<?php echo esc_attr( get_option('shiftt_notify_wxtemplateid') ); ?>">
                        <p dir="rtl" style="text-align: left;" class="description">
                    		سالون ئارقا سۇپىسىدكى TemplateID نى كىرگۈزۈڭ
                		</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="shiftt_notify_author_openid"> OpenID </label></th>
                    <td>
                        <input type="text" name="shiftt_notify_author_openid" class="regular-text" 
                            value="<?php echo esc_attr( get_option('shiftt_notify_author_openid') ); ?>">
                        <p dir="rtl" style="text-align: left;" class="description">
                            سالونىڭىزدىن ئۆزىڭىزنىڭ OpenID سنى تىپىپ تولدۇرۇڭ
                		</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">   
            <input type="submit" name="submit" class="button button-primary" value="ساقلاش">
        </p> 
    </form>   
    
    </div>
    
<?php }  

add_action('wp_insert_comment', 'shiftt_send_msg');

function shiftt_send_msg($comment_id) {
    
    $appid = get_option('shiftt_notify_appid');
    $appsecret = get_option('shiftt_notify_appsecret');
    $wxtemplateid = get_option('shiftt_notify_wxtemplateid');
    $author_openid = get_option('shiftt_notify_author_openid');
    
    $comment = get_comment($comment_id);
    $post_id = $comment->comment_post_ID;
    $post   = get_post($post_id);
    $post_title = $post->post_title;
    
    $username = get_comment_author($comment_id) ;
    $datetime = $comment->comment_date ;
    $content = $comment->comment_content ;
    $link = get_comment_link($comment_id);
    

    $aurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;

    $body = wp_remote_retrieve_body( wp_remote_get( $aurl ) );
    $body = json_decode( $body, true );
    $access_token = $body['access_token'];

    $openid = $author_openid;
    $templateid = $wxtemplateid;
    $first = 'يازما : [' . $post_title . ']  غا يىڭى ئىنكاس يوللاندى ';
    
    $data1 = $username;
    $data2 = $datetime;
    $data3 = $content;
    $remark = "بۇيەرنى بىسىپ كىرىپ جاۋاب قايتۇرۇڭ";

    $wurl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;

    $data = '{
       "touser":"' . $openid . '",
       "template_id":"' . $templateid . '",
       "url":"'. $link .'", 
       "miniprogram":{
         "appid":"",
         "pagepath":""
       },         
       "data":{
               "first": {
                   "value":"' . $first . '"
               },
               "keyword1":{
                   "value":"' . $data1 . '"
               },
               "keyword2": {
                   "value":"' . $data2 . '"
               },
               "keyword3": {
                   "value":"' . $data3 . '"
               },
               "remark":{
                   "value":"' . $remark . '"
               }
            }
        }';
    $send = wp_remote_post($wurl, array(
        'body' => $data,
        'method' => 'POST',
        'headers' => array('Content-Type' => 'application/json'),
    ));   
    return $send;
    exit;
}

?>
