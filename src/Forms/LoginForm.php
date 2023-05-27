<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

namespace Johncms\Auth\Forms;

use Johncms\Forms\AbstractForm;
use Johncms\Forms\Inputs\Captcha;
use Johncms\Forms\Inputs\Checkbox;
use Johncms\Forms\Inputs\InputPassword;
use Johncms\Forms\Inputs\InputText;
use Johncms\Users\User;

class LoginForm extends AbstractForm
{
    protected function prepareFormFields(): array
    {
        $fields = [];
        $fields['login'] = (new InputText())
            ->setLabel(__('Username'))
            ->setPlaceholder(p__('placeholder', 'Enter your username'))
            ->setNameAndId('login')
            ->setValue($this->getValue('login'))
            ->setValidationRules(['NotEmpty']);

        $fields['password'] = (new InputPassword())
            ->setLabel(__('Password'))
            ->setPlaceholder(p__('placeholder', 'Password'))
            ->setNameAndId('password')
            ->setValidationRules(['NotEmpty']);

        $fields['remember'] = (new Checkbox())
            ->setLabel(__('Remember me'))
            ->setNameAndId('remember')
            ->setValue('yes')
            ->setChecked(! empty($this->getValue('remember', true)));

        if ($this->needCaptcha()) {
            $fields['captcha'] = (new Captcha())
                ->setLabel(__('Enter verification code'))
                ->setPlaceholder(p__('placeholder', 'Verification code'))
                ->setNameAndId('captcha')
                ->setValidationRules(['Captcha']);
        }

        return $fields;
    }

    private function needCaptcha(): bool
    {
        $login = $this->getValue('login');
        if (! empty($login)) {
            $user = (new User())
                ->where('login', $login)
                ->orWhere('email', $login)
                ->orWhere('phone', $login)
                ->first();
            if ($user?->failed_login > 2) {
                return true;
            }
        }
        return false;
    }
}
