<?php

declare(strict_types=1);

namespace Johncms\Auth\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Johncms\i18n\Translator;
use Johncms\Mail\EmailMessage;
use Johncms\Users\Exceptions\UserNotFoundException;
use Johncms\Users\User;

class RestorePasswordService
{
    public function __construct(
        private readonly Translator $translator
    ) {
    }

    public function sendRestorePasswordEmail(string $username): void
    {
        $user = User::query()
            ->where('login', $username)
            ->orWhere('email', $username)
            ->orWhere('phone', $username)
            ->first();

        if (! $user) {
            throw new UserNotFoundException(__('The user "%s" was not found', $username));
        }

        $name = $user->displayName();
        $code = Str::random();

        $user->update(
            [
                'restore_password_code' => $code,
                'restore_password_date' => Carbon::now(),
            ]
        );

        EmailMessage::query()->create(
            [
                'priority' => 1,
                'locale'   => $this->translator->getLocale(),
                'template' => 'system::mail/templates/restore_password',
                'fields'   => [
                    'email_to'        => $user->email,
                    'name_to'         => $name,
                    'subject'         => __('Restore Password'),
                    'user_name'       => $name,
                    'link_to_restore' => route('auth.restorePassword.changePassword', ['userId' => $user->id], ['code' => $code], true),
                ],
            ]
        );
    }
}
