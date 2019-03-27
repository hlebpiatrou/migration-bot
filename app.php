<?php

require_once 'curl.php';
require_once 'vendor/autoload.php';

const TELEGRAM_CHAT_ID = -1001396797655; // Insert your Telegram chat ID
const TELEGRAM_BOT_API_KEY = '710713194:AAG8mMB4EzuhhXvS2ieEcjFgpL-aj_8jiqg'; // Insert your Telegram bot API key
const NOTIFICATION_INTEREST_INTERVAL = 'P35D'; // Check availability for upcoming 35 days from today

$responseBody = new Curl(
    'https://www.epolicija.lt/rezervacija/source/public_php.php', array(
    CURLOPT_POSTFIELDS => array(
        'public_rez_q' => 'public_gauti_zingsnis_2_forma',
        'public_rez_skyriaus_id' => '16',
    ))
);

try {

    $domDocument = new DOMDocument;
    $domDocument->loadHTML($responseBody);

    $xPath = new DOMXPath($domDocument);
    $inputs = $xPath->query('//input');

    $datesBusy = array();
    $datesFreePeriods = array();

    if (is_object($inputs)) {
        foreach($inputs as $input) {
            if ($input instanceof DOMElement) {
                $inputValue = $input->getAttribute('value');
                if (preg_match(
                    "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
                    $inputValue)
                ) {
                    array_push($datesBusy, strtotime($inputValue));
                }
            }
        }
    } else {
        die('E-Policija script output has been changed');
    }

    for ($i=0; $i<count($datesBusy); $i++) {
        if (isset($datesBusy[$i+1]) && ($datesBusy[$i+1] - $datesBusy[$i] > 86400)) {
            $datesFreePeriod = new DatePeriod(
                new DateTime(
                    date('m/d/Y', strtotime(
                        '+1 day', $datesBusy[$i])
                    )
                ),
                new DateInterval('P1D'),
                new DateTime(date('m/d/Y', $datesBusy[$i+1]))
            );
            array_push($datesFreePeriods, $datesFreePeriod);
        }
    }

    $bot = new \TelegramBot\Api\BotApi(TELEGRAM_BOT_API_KEY);

    $maxInterestedDate = new DateTime();
    $maxInterestedDate->add(
        new DateInterval(
            NOTIFICATION_INTEREST_INTERVAL
        )
    );

    foreach ($datesFreePeriods as $datesFreePeriod) {
        foreach ($datesFreePeriod as $date => $value) {
            if ($value instanceof DateTime && $value <= $maxInterestedDate) {
                $bot->sendMessage(
                    TELEGRAM_CHAT_ID,
                    'Только что стала доступна следующая дата для резервации: ' . $value->format('d/m/Y') . '. Ссылка на сайт: https://www.epolicija.lt/rezervacija/index.php?id=3&lang=RU'
                );
            }
        }
    }

} catch (\RuntimeException $ex) {
    die(
        sprintf('Http error %s with code %d',
            $ex->getMessage(), $ex->getCode()
        )
    );
}