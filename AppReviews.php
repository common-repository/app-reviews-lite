<?php
/*
* Plugin Name: App Reviews LITE
* Plugin URI: http://appreviewsplugin.com
* Description: App store ratings and reviews are essential for the success of any mobile app. Now show your best app store reviews right on your wordpress marketing site all with a simple shortcode!
* Version: 1.4
* Author: ADM Apps
* Author URI: http://appreviewsplugin.com
*/

function appreviews_setup( $atts, $content = null ) {
    $attributes = shortcode_atts( array(
        'appid' => '',
        'minrate' => '3',
        'countrycode' => 'us',
        'scrollspeed' => '7000'
    ), $atts );
       if (empty($attributes['appid']) || is_numeric($attributes['appid']) == false)
       {
            return "<span color='red'>There is an error with the 'appid' attribute of your App Reviews shortcode</span>";
        }
$json = file_get_contents('https://itunes.apple.com/'. $attributes['countrycode'] .'/rss/customerreviews/page=1/id=' . $attributes['appid'] . '/sortBy=mostRecent/json');
$reviews = json_decode($json, true);
if (isset($reviews['feed']['entry']))
{
$names = array();
        $count = -1;
        $addedcount = 0;
foreach($reviews['feed']['entry'] as $item) {
    if ($count > -1)
    {
        $names[$addedcount] = '<img src="' . WP_PLUGIN_URL . '/app-reviews-lite/' . $item['im:rating']['label'] . '.png"></img><br><em>' . $item['author']['name']['label'] . '</em> said:<br><b>' . $item['title']['label'] . '</b><br>"' . $item['content']['label'] . '"';
        $addedcount = $addedcount + 1;
}
$count = $count + 1;
}
$js_array = json_encode($names);
$arr1 = str_split($attributes['appid']);
$idText = '';
foreach ($arr1 as $num)
{
    $idText = $idText . toAlpha($num);
}
echo '<SCRIPT type="text/javascript">';
echo 'var ' . $idText . ' = ' . $js_array .';';
echo 'function display' . $idText . '()';
echo '{a=Math.floor(Math.random()* ' . $idText . '.length);document.getElementById(\'' . $idText . 'quotation\').innerHTML=' . $idText . '[a];setTimeout("display' . $idText . '()",' . $attributes['scrollspeed'] .');}';
echo '</SCRIPT>';
$output = '<div id="' . $idText . 'quotation">';
$output = $output . '<SCRIPT type="text/javascript">display' . $idText . '()</SCRIPT>';
$output = $output .'</div>';
return $output;
}
else
{
    return "<span color='red'>There was a problem downloading app reviews from the Apple app store.</span>";
}
}

function toAlpha($data){
    $alphabet =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    $alpha_flip = array_flip($alphabet);
        if($data <= 25){
          return $alphabet[$data];
        }
        elseif($data > 25){
          $dividend = ($data + 1);
          $alpha = '';
          $modulo;
          while ($dividend > 0){
            $modulo = ($dividend - 1) % 26;
            $alpha = $alphabet[$modulo] . $alpha;
            $dividend = floor((($dividend - $modulo) / 26));
          } 
          return $alpha;
        }

}
add_shortcode('appreviews', 'appreviews_setup');
?>