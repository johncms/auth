<?php

declare(strict_types=1);

namespace Johncms\Auth\Forms;

use Johncms\Forms\AbstractForm;
use Johncms\Forms\Inputs\Captcha;
use Johncms\Forms\Inputs\InputText;

class RestorePasswordForm extends AbstractForm
{
    protected function prepareFormFields(): array
    {
        $fields = [];
        $fields['login'] = (new InputText())
            ->setLabel(__('Login'))
            ->setPlaceholder(p__('placeholder', 'Enter your login'))
            ->setNameAndId('login')
            ->setValue($this->getValue('login'))
            ->setValidationRules(
                [
                    'NotEmpty',
                ]
            );

        $fields['captcha'] = (new Captcha())
            ->setLabel(__('Enter verification code'))
            ->setPlaceholder(p__('placeholder', 'Verification code'))
            ->setNameAndId('captcha')
            ->setValidationRules(['Captcha']);

        return $fields;
    }
}
