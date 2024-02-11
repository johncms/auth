<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

namespace Johncms\Auth\Controllers;

use Johncms\Auth\Forms\LoginForm;
use Johncms\Controller\BaseController;
use Johncms\Exceptions\ValidationException;
use Johncms\Http\Response\RedirectResponse;
use Johncms\Http\Session;
use Johncms\Users\AuthProviders\CookiesAuthProvider;
use Johncms\Users\AuthProviders\SessionAuthProvider;
use Johncms\Users\UserManager;
use Throwable;

class LoginController extends BaseController
{
    protected string $moduleName = 'johncms/auth';

    public function __construct()
    {
        parent::__construct();
        $this->metaTagManager->setAll(__('Login'));
        $this->navChain->add(__('Login'), route('login.index'));
    }

    /**
     * @throws Throwable
     */
    public function index(Session $session, LoginForm $loginForm): string
    {
        $data = [
            'formFields'       => $loginForm->getFormFields(),
            'validationErrors' => $loginForm->getValidationErrors(),
            'storeUrl'         => route('login.authorize'),
            'registrationUrl'  => route('registration.index'),
            'authError'        => $session->getFlash('authError'),
        ];

        return $this->render->render('johncms/auth::login_form', ['data' => $data]);
    }

    public function authorize(
        UserManager $userManager,
        Session $session,
        SessionAuthProvider $sessionAuthProvider,
        CookiesAuthProvider $cookiesAuthProvider,
        LoginForm $loginForm
    ): RedirectResponse {
        try {
            // Validate the form
            $loginForm->validate();
            $values = $loginForm->getRequestValues();

            try {
                // Try to check credentials and authorize the user
                $user = $userManager->checkCredentials($values['login'], $values['password']);
                if ($values['remember']) {
                    $cookiesAuthProvider->store($user);
                }
                $sessionAuthProvider->store($user);
                return (new RedirectResponse(route('homepage.index')));
            } catch (Throwable $exception) {
                $session->flash('authError', $exception->getMessage());
                return (new RedirectResponse(route('login.index')))->withPost();
            }
        } catch (ValidationException $validationException) {
            // Redirect to the login form if the form is invalid
            return (new RedirectResponse(route('login.index')))
                ->withPost()
                ->withValidationErrors($validationException->getErrors());
        }
    }
}
