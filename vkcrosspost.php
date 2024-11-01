<?php

/*
Plugin Name: VKontakte Cross-Post
Plugin URI:
Description: Automatic post on the Vkontakte Walls
Version: 0.3.2
Author: Oleg Zhavoronkin
Author URI: http://webstudy.com.ua
*/

add_option("skylark_vkcp_auth_token", '', "Vkontakte application auth token", 'no'); // default value
add_option("skylark_vkcp_application_id", '', "Vkontakte application id", 'no'); // default value
add_option("skylark_vkcp_group_id", '', "Vkontakte group id", 'no'); // default value
add_option("skylark_vkcp_autopost_on_publish", '', "Automatically prompt the user with the post to wall dialog", 'no'); // default value
add_option("skylark_vkcp_use_excerpt_text", '', "Use excerpt as a default post text", 'no'); // default value
add_option("skylark_vkcp_use_excerpt_length", 250, "Excerpt Length", 'no'); // default value

// WP 3.0+
// add_action( 'add_meta_boxes', 'myplugin_add_custom_box' );

// backwards compatible
add_action('admin_init', 'vk_cross_post_add_custom_box', 1);

function vk_cross_post_add_custom_box()
{
    add_meta_box('vcp', 'VKontakte Cross-Post', 'vk_cross_post_add_custom_wall_publish_option', 'post');
    add_meta_box('vcp', 'VKontakte Cross-Post', 'vk_cross_post_add_custom_wall_publish_option', 'page');
}


function vk_cross_post_add_custom_wall_publish_option($post)
{
    $vkontakteApplicationId = get_option('skylark_vkcp_application_id');
    $vkontakteApplicationAuthToken = get_option('skylark_vkcp_auth_token');
    $group_id = get_option('skylark_vkcp_group_id');

    if ($vkontakteApplicationId > 0 && !empty($vkontakteApplicationAuthToken) && $group_id > 0) {

        $excerptUse = get_option('skylark_vkcp_use_excerpt_text');
        $autopostUse = get_option('skylark_vkcp_autopost_on_publish');

        ?>
    <table>
        <tr>
            <th scope="row" style="text-align:right; vertical-align:top;">
                Опции публикации на стену группы ВКонтакте
            </th>
            <td>
                <input type="radio" name="vkcp_publish" checked="1" value="1" id="var1"/><label for="var1">Запостить на стену ВК только в случае если это первая публикация поста</label><br/><br/>
                <input type="radio" name="vkcp_publish" value="2" <?php if (!empty($autopostUse)) { ?>checked="1"<?php }?> id="var2"/><label for="var2">Обязательно запостить на стену ВК (даже если это просто обновление поста)</label><br/><br/>
                <input type="radio" name="vkcp_publish" value="3" id="var3"/><label for="var3">Не постить на стену ВК</label>
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <th scope="row" style="text-align:right; vertical-align:top;">
                Текст сообщения которое будет отправлено на стену ВК
            </th>
            <td>
                <input type="radio" name="vkcp_publish_text_variant" <?php if (empty($excerptUse)) { ?>checked="1"<?php }?> value="1" checked="1" id="var4"/><label for="var4">Только заголовок</label><br/><br/>
                <input type="radio" id="vkcp_publish_excerpt_text" name="vkcp_publish_text_variant" <?php if (!empty($excerptUse)) { ?>checked="1"<?php }?>  value="2"/><label for="vkcp_publish_excerpt_text">Краткое содержание</label><br/><br/>
                <input type="radio" id="vkcp_publish_custom_text" name="vkcp_publish_text_variant" value="3"/><label for="vkcp_publish_custom_text">Собственный текст (введите ниже)</label><br/><br/>
                <textarea id="vgwp_publish_text" name="vkcp_publish_text" stype="width:300px;height:200px"></textarea>
            </td>
        </tr>
    </table>
    <?php
    } else {
        ?>
    <h2 style="color: #F33">Для активации плагина необходимо создать приложение ВК, прописать его ID в настройках а также сгенерировать Auth Token на странице настроек плагина.</h2>
    <?php
    }
}

function skylark_vkontakte_cross_post_options_page()
{
    if ($_POST) {
        if ($_POST['skylark_vkcp_application_id']) {
            update_option('skylark_vkcp_application_id', $_POST['skylark_vkcp_application_id']);
        }
        if ($_POST['skylark_vkcp_auth_token']) {
            update_option('skylark_vkcp_auth_token', $_POST['skylark_vkcp_auth_token']);
        }
        if (isset($_POST['skylark_vkcp_use_excerpt_length'])) {
            update_option('skylark_vkcp_use_excerpt_length', $_POST['skylark_vkcp_use_excerpt_length']);
        } else {
            update_option('skylark_vkcp_use_excerpt_length', 250);
        }
        if ($_POST['skylark_vkcp_group_id']) {
            update_option('skylark_vkcp_group_id', $_POST['skylark_vkcp_group_id']);
        }
        if ($_POST['skylark_vkcp_autopost_on_publish']) {
            update_option('skylark_vkcp_autopost_on_publish', $_POST['skylark_vkcp_autopost_on_publish']);
        } else {
            update_option('skylark_vkcp_autopost_on_publish', '');
        }

        if ($_POST['skylark_vkcp_use_excerpt_text']) {
            update_option('skylark_vkcp_use_excerpt_text', $_POST['skylark_vkcp_use_excerpt_text']);
        } else {
            update_option('skylark_vkcp_use_excerpt_text', '');
        }
    }


    $vkontakteApplicationId = get_option('skylark_vkcp_application_id');
    $vkontakteApplicationAuthToken = get_option('skylark_vkcp_auth_token');
    $vkontakteGroupId = get_option('skylark_vkcp_group_id');
    $autopost = get_option('skylark_vkcp_autopost_on_publish');
    $excerptUse = get_option('skylark_vkcp_use_excerpt_text');
    $excerptLength = get_option('skylark_vkcp_use_excerpt_length');

    $autopostText = '';
    if ($autopost) {
        $autopostText = 'checked="checked"';
    }

    $excerptUseText = '';
    if ($excerptUse) {
        $excerptUseText = 'checked="checked"';
    }

	$link = 'https://oauth.vk.com/authorize?client_id=' . $vkontakteApplicationId . '&scope=wall,photos,offline&redirect_uri=https://oauth.vk.com/blank.html&display=page&response_type=token';

    if ($vkontakteApplicationId > 0) {
        $settings = '
			<div class="wrap">
				<h2>Настройки Vkontakte Cross-Post</h2>
				<form method="post" action="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">
					<p>ID приложения на VK</p>
					<input type="text" id="skylark_vkcp_application_id" name="skylark_vkcp_application_id" value="' . $vkontakteApplicationId . '">

					<p>Токен Авторизации пользователя. Параметр обязателен для активации плагина. <a href="' . $link . '" target="_blank">Авторизовать пользователя ВК</a> для получения токена</p>
					<p>Кликнув на ссылку Авторизовать пользователя ВК в новом окне откроется окно предоставления пермишенов вашему приложению.</p>

					<p><strong>В случае если вам не удаётся сгенерировать авторизационный токен - перелогиньтесь в ВК используя номер вашего телефона указанного при регистрации ВК в качестве логина.</strong></p>

					<img src="/wp-content/plugins/vkontakte-cross-post/img/step4.png" /><br />
					<p>Подтвердив права, вам будет сгенерирован так называемый Auth Token. Скопируйте его в поле ниже, для активации плагина.</p>
					<img src="/wp-content/plugins/vkontakte-cross-post/img/step5.png" /><br />

					<p>Токен Авторизации пользователя.</p>
					<input type="text" id="skylark_vkcp_auth_token" name="skylark_vkcp_auth_token" value="' . $vkontakteApplicationAuthToken . '">

					<p>ID Группы или Официальной страницы VK</p>
					<input type="text" id="skylark_vkcp_group_id" name="skylark_vkcp_group_id" value="' . $vkontakteGroupId . '">

					<p>Автоматически всегда постить на стену вконтакте</p>
					<input type="checkbox" id="skylark_vkcp_autopost_on_publish" name="skylark_vkcp_autopost_on_publish" value="1" ' . $autopostText . '>
					<label for="skylark_vkcp_autopost_on_publish">Постить автоматически</label>

					<p>Использовать краткое содержание в качестве текста поста</p>
					<input type="checkbox" id="skylark_vkcp_use_excerpt_text" name="skylark_vkcp_use_excerpt_text" value="1" ' . $excerptUseText . '>
					<label for="skylark_vkcp_use_excerpt_text">Использовать краткое содержание</label>

					<p>Если поле краткого содержания поста не заполнено, то будет использован отрезок текста самого поста от начала поста, соответствующей длины.</p>
					<p>Если указать значение равное либо меньше 0, то будет использован весь текст поста.</p>
					<input type="text" id="skylark_vkcp_use_excerpt_length" name="skylark_vkcp_use_excerpt_length" value="' . $excerptLength . '"><br />
					<label for="skylark_vkcp_use_excerpt_length">Длина краткого содержания поста</label>

					<p class="submit" style="width:420px;"><input type="submit" value="Submit &raquo;" /></p>
				</form>
			</div>
		';
    } else {
        $settings = '
			<div class="wrap">
			<h2>Настройки Vkontakte Cross-Post</h2>
			<form method="post" action="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">

			<div id="appid">
				<p>Для начала Вам необходимо создать приложение на ВК на странице <a href="http://vk.com/developers.php" target="_blank">VK "Разработчикам"</a>. Следуйте инструкциям на скриншотах предоставленных ниже.</p>
				<p>Шаг 1. На странице разработчиков перейти к режиму создания приложения кликнув на кнопке "Создать Приложение"</p>
				<img src="/wp-content/plugins/vkontakte-cross-post/img/step1.png" /><br />
				<p>Шаг 2. Ввести название и выбрать тип Standalone-приложение</p>
				<img src="/wp-content/plugins/vkontakte-cross-post/img/step2.png" /><br />
				<p>Далее надо будет ввести код высланый вам на мобильный телефон, указанный при регистрации на ВК.</p>
				<p>Последним шагом является настройка самого приложения. Необходимо ввести <br />
				URL вашего сайта: <strong>http://' . $_SERVER['HTTP_HOST'] . '</strong><br />
				Доменное имя вашего сайте: <strong>' . $_SERVER['HTTP_HOST'] . '</strong><br /><br />
				Также в настройки плагина надо перенести ID приложения (выделено зелёным цветом.)
				</p>
				<img src="/wp-content/plugins/vkontakte-cross-post/img/step3.png" /><br />
				<p>ID приложения на VK</p>
				<input type="text" id="skylark_vkcp_application_id" name="skylark_vkcp_application_id" value="' . $vkontakteApplicationId . '">
			</div>

			<p class="submit" style="width:420px;"><input type="submit" value="Submit &raquo;" /></p>
			</form>
			</div>
		';
    }

    print $settings;
}

function skylark_vkcp_cross_post_admin_page()
{
    add_submenu_page('options-general.php', 'VK Cross-Post', 'VK Cross-Post', 9, 'skylark-vk-cross-post.php', 'skylark_vkontakte_cross_post_options_page');
}

function skylark_vkcp_post_on_vkontakte_wall_forced($post_ID)
{
    if (isset($_POST['vkcp_publish']) && $_POST['vkcp_publish'] == 2) {
        return skylark_vkcp_post_on_vkontakte_wall($post_ID);
    }

    return $post_ID;
}

function skylark_vkcp_post_on_vkontakte_wall_scheduled($post_ID)
{
	$post = get_post($post_ID);

	if($post->post_modified == $post->post_date) return $post_ID;

    skylark_vkcp_post_on_vkontakte_wall($post_ID, true);

    return $post_ID;
}

function requestVK($method, $params)
{
    $vkontakteApplicationAuthToken = get_option('skylark_vkcp_auth_token');

    $params['access_token'] = $vkontakteApplicationAuthToken;

    $query = http_build_query($params);

    $link = 'https://api.vk.com/method/' . $method . '?' . $query;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $otvet = curl_exec($ch);
    curl_close($ch);
    return $otvet;
}

function sendImageToVK()
{
    $attachment = get_attached_file(get_post_thumbnail_id());
    if (empty($attachment)) {
        return false;
    }

    $group_id = get_option('skylark_vkcp_group_id');

    $thumbUploadUrl = requestVK('photos.getWallUploadServer', array(
        'gid' => $group_id
    ));

    if (!empty($thumbUploadUrl)) {
        $thumbUploadUrlObj = json_decode($thumbUploadUrl);
        $VKuploadUrl = $thumbUploadUrlObj->response->upload_url;
    }

    if (!empty($VKuploadUrl)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $VKuploadUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => '@' . $attachment));

        $otvet = curl_exec($ch);
        curl_close($ch);
        $uploadResultObj = json_decode($otvet);
        if (!empty($uploadResultObj->server) && !empty($uploadResultObj->photo) && !empty($uploadResultObj->hash)) {
            $saveImageResult = requestVK('photos.saveWallPhoto', array(
                'server' => $uploadResultObj->server,
                'photo' => $uploadResultObj->photo,
                'hash' => $uploadResultObj->hash,
                'gid' => $group_id
            ));

            $resultObject = json_decode($saveImageResult);
            if (isset($resultObject) && isset($resultObject->response[0]->id)) {
                return $resultObject->response[0];
            } else {
                return false;
            }
        }
    }
}

function skylark_vkcp_post_on_vkontakte_wall($post_ID, $scheduledRun = false)
{
    if (!$scheduledRun && !isset($_POST['vkcp_publish']) || $_POST['vkcp_publish'] == 3) {
        return $post_ID;
    }

	global $post;

    if ($post->toWallPublished) {
        return $post_ID;
    }

	$post = get_post($post_ID);

    $vkontakteApplicationId = get_option('skylark_vkcp_application_id');
    $vkontakteApplicationAuthToken = get_option('skylark_vkcp_auth_token');
    $group_id = get_option('skylark_vkcp_group_id');

    if ($vkontakteApplicationId > 0 && !empty($vkontakteApplicationAuthToken) && $group_id > 0) {

        $link = get_permalink($post->ID);

        $excerptLength = get_option('skylark_vkcp_use_excerpt_length');

		$content = isset($_POST['content']) ? $_POST['content'] : $post->post_content;

		$text = '';

        if ($_POST['vkcp_publish_text_variant'] == 1) {
            $text = $post->post_title;
        } else if ($_POST['vkcp_publish_text_variant'] == 3 && !empty($_POST['vkcp_publish_text'])) {
            $text = $_POST['vkcp_publish_text'];
        } else if ($_POST['vkcp_publish_text_variant'] == 2 || $scheduledRun) {
			$excerpt = isset($_POST['excerpt']) ? $_POST['excerpt'] : $post->post_excerpt;
            if (empty($excerpt)) {
                $excerpt = strip_tags($content);
                if ($excerptLength > 0) {
                    $excerpt = mb_substr($excerpt, 0, $excerptLength, 'UTF-8') . '...';
                }
            } else if (strlen($excerpt > $excerptLength)) {
                $excerpt = mb_substr($excerpt, 0, $excerptLength, 'UTF-8') . '...';
            }
            $text = $post->post_title . "\n\n" . $excerpt;
        }

        $text = stripslashes(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));

        if ($imageId = sendImageToVK()) {
            $attachments = $imageId->id;
            $text .= "\n" . $link;
        } else {
            $attachments = $link;
        }


        $result = requestVK('wall.post', array(
            'owner_id' => '-' . $group_id,
            'message' => $text,
            'from_group' => 1,
            'attachments' => $attachments
        ));

        $post->toWallPublished = true;

    }

    return $post_ID;
}

add_action('pending_to_publish', 'skylark_vkcp_post_on_vkontakte_wall');
add_action('draft_to_publish', 'skylark_vkcp_post_on_vkontakte_wall');
add_action('new_to_publish', 'skylark_vkcp_post_on_vkontakte_wall');

add_action('publish_post', 'skylark_vkcp_post_on_vkontakte_wall_forced');
add_action('publish_future_post', 'skylark_vkcp_post_on_vkontakte_wall_scheduled');

add_action('admin_menu', 'skylark_vkcp_cross_post_admin_page');