<?php
define('WP_USE_THEMES', false);
require('./wp-blog-header.php');


$postID = $_GET["postid"];
$type = $_GET["type"];

switch ($type){
   		case "singlePost":
     		 $posts = get_post($postID);
     		 $image = get_the_post_thumbnail( $postID);
      break;
}



?>
<xml>
<items>
			<item>
            <id><?= $posts->ID ?></id>
            <title><![CDATA[<?= $posts->post_title ?>]]></title>
            <content><![CDATA[<?= $posts->post_content ?>]]></content>
            <image><![CDATA[<?= $image ?>]]></image>
            <link><![CDATA[<?= $posts->guid ?>]]></link>      
         </item>
   </items>
</xml>