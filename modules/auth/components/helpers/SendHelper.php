<?php

declare(strict_types=1);

namespace auth\components\helpers;

use auth\models\User;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Yii;

/**
 * Class SendHelper
 */
class SendHelper
{
    /**
     * @param string $email
     * @param string $code
     * @param string $layout
     */
    public static function sendConfirmEmailMessage(string $email, string $code, string $layout = 'confirm_email'): void
    {
        $text = Yii::$app->view->render(
            sprintf('@auth/views/mails/%s.php', $layout),
            [
                'email' => $email,
                'code'  => $code,
            ]
        );

        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($email)
            ->setSubject('Confirm')
            ->setHtmlBody($text)
            ->send()
        ;
    }

    /**
     * @param User   $user
     *
     * @param string $phone
     *
     * @return true|string
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public static function sendPhoneCode(User $user, string $phone)
    {
        $renewTime = $user->smsRequest->getRenewTime();

        if (time() < $renewTime) {
            return sprintf('You can resend after %s', $renewTime - time());
        }

        try {
            $client = new Client($_ENV['TWILIO_SID'], $_ENV['TWILIO_TOKEN']);
            $client->messages->create(
                $phone,
                [
                    'from' => $_ENV['TWILIO_NUMBER'],
                    'body' => Yii::$app->view->render(
                        '@auth/views/sms/confirm_phone.php',
                        [
                            'code' => $user->confirm_phone,
                        ]
                    ),
                ]
            );

            $user->phone = $phone;
            $user->smsRequest->attempt += 1;
            $user->smsRequest->request_time = date('Y-m-d H:i:s');
            $user->smsRequest->save();
            $user->save();

            return true;
        } catch (RestException $exception) {
            return 'SMS services are not available now, try again later';
        }
    }
}
