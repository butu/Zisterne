<?php
// measurements of the well
$distanceToGroundInCm = 197;
$tankDepthInCm = 140;
$diameterInCm = 215;
$litersPerMinute = 15;

// load data from thingspeak
$json = file_get_contents('https://api.thingspeak.com/channels/813242/fields/2.json?api_key=ZLQJWYW43AHCESR3&results=1');
$data = json_decode($json, true);
$date = DateTime::createFromFormat(
    DATE_ATOM,
    $data['feeds'][0]['created_at']
)->setTimezone(new \DateTimeZone('Europe/Vienna'));

// calculate current water depth
$waterDepth = $distanceToGroundInCm - (float)$data['feeds'][0]['field2'];
$percent = round($waterDepth / $tankDepthInCm * 100);
if ($percent > 100) {
    $percent = 100;
}
if ($percent < 0) {
    $percent = 1;
}

// calculate volume
$volumeInLiters = $diameterInCm / 2 * $diameterInCm / 2 * 3.141592 * $waterDepth / 1000;

// calculate time until the well is empty
$wateringFlowersHours = $volumeInLiters / $litersPerMinute / 60;
$wateringFlowersHoursCa = floor($wateringFlowersHours);
$wateringFlowersMinutesCa = round(($wateringFlowersHours - $wateringFlowersHoursCa) * 60);
?>
<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="refresh" content="60">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Wasserstand der Zisterne">

    <title>Zisterne</title>

    <link rel='manifest' href='manifest.json'>
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed|Rubik:500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<div class="date"><?php echo $date->format('d.m.Y, H:i'); ?></div>
<div class="label">Wasserstand Zisterne</div>
<div class="liters"><?= round($volumeInLiters / 1000, 1) . ' m³' ?></div>
<div class="watering"><?= $wateringFlowersHoursCa . ' Std ' . $wateringFlowersMinutesCa . ' Min gießen' ?></div>

<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
     style="display: none;">
    <symbol id="wave">
        <path
                d="M420,20c21.5-0.4,38.8-2.5,51.1-4.5c13.4-2.2,26.5-5.2,27.3-5.4C514,6.5,518,4.7,528.5,2.7c7.1-1.3,17.9-2.8,31.5-2.7c0,0,0,0,0,0v20H420z"></path>
        <path
                d="M420,20c-21.5-0.4-38.8-2.5-51.1-4.5c-13.4-2.2-26.5-5.2-27.3-5.4C326,6.5,322,4.7,311.5,2.7C304.3,1.4,293.6-0.1,280,0c0,0,0,0,0,0v20H420z"></path>
        <path
                d="M140,20c21.5-0.4,38.8-2.5,51.1-4.5c13.4-2.2,26.5-5.2,27.3-5.4C234,6.5,238,4.7,248.5,2.7c7.1-1.3,17.9-2.8,31.5-2.7c0,0,0,0,0,0v20H140z"></path>
        <path
                d="M140,20c-21.5-0.4-38.8-2.5-51.1-4.5c-13.4-2.2-26.5-5.2-27.3-5.4C46,6.5,42,4.7,31.5,2.7C24.3,1.4,13.6-0.1,0,0c0,0,0,0,0,0l0,20H140z"></path>
    </symbol>
</svg>
<div class="box">
    <div class="percent">
        <div class="percentNum" id="count">0</div>
        <div class="percentB">%</div>
    </div>
    <div id="water" class="water">
        <svg viewBox="0 0 560 20" class="water_wave water_wave_back">
            <use xlink:href="#wave"></use>
        </svg>
        <svg viewBox="0 0 560 20" class="water_wave water_wave_front">
            <use xlink:href="#wave"></use>
        </svg>
    </div>
</div>

<script>maxPercent = <?php echo $percent;?>;</script>
<script src="js/main.js"></script>
</body>
</html>
