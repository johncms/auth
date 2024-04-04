<?php

declare(strict_types=1);

namespace Johncms\Auth\Forms;

use Johncms\Forms\AbstractForm;
use Johncms\Forms\Inputs\InputHidden;
use Johncms\Forms\Inputs\InputPassword;

class ChangePasswordForm extends AbstractForm
{
    protected function prepareFormFields(): array
    {
        $fields = [];
        $fields['password'] = (new InputPassword())
            ->setLabel(__('New Password'))
            ->setPlaceholder(p__('placeholder', 'New Password'))
            ->setNameAndId('password')
            ->setHelpText(
                n__(
                    'Min. %s character.',
                    'Min. %s characters.',
                    6,
                    6
                )
            )
            ->setValidationRules(
                [
                    'NotEmpty',
                    'StringLength' => ['min' => 6],
                ]
            );

        $fields['code'] = (new InputHidden())
            ->setNameAndId('code')
            ->setValue($this->getValue('code'))
            ->setValidationRules(['NotEmpty']);

        return $fields;
    }
}
