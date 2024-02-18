<?php

declare(strict_types=1);

namespace Johncms\Auth\Controllers;

use Johncms\Auth\Forms\RestorePasswordForm;
use Johncms\Auth\Services\RestorePasswordService;
use Johncms\Controller\BaseController;
use Johncms\Exceptions\ValidationException;
use Johncms\Http\Request;
use Johncms\Http\Response\RedirectResponse;
use Johncms\Http\Session;
use Johncms\Users\Exceptions\UserNotFoundException;
use Throwable;

class RestorePasswordController extends BaseController
{
    protected string $moduleName = 'johncms/auth';

    public function __construct()
    {
        parent::__construct();
        $this->metaTagManager->setAll(__('Restore Password'));
        $this->navChain->add(__('Login'), route('login.index'));
        $this->navChain->add(__('Restore Password'), route('auth.restorePassword'));
    }

    /**
     * @throws Throwable
     */
    public function index(Session $session, RestorePasswordForm $form, Request $request, RestorePasswordService $restorePasswordService)
    {
        if ($request->isPost()) {
            try {
                $form->validate();
                $values = $form->getRequestValues();

                $restorePasswordService->sendRestorePasswordEmail($values['login']);
                $session->flash('successMessage', __('Check your e-mail for further information'));

                return (new RedirectResponse(route('auth.restorePassword')));
            } catch (ValidationException $validationException) {
                return (new RedirectResponse(route('auth.restorePassword')))
                    ->withPost()
                    ->withValidationErrors($validationException->getErrors());
            } catch (UserNotFoundException $exception) {
                $commonErrors = $exception->getMessage();
            }
        }

        return $this->render->render('johncms/auth::restore_password', [
            'data' => [
                'formFields'       => $form->getFormFields(),
                'validationErrors' => $form->getValidationErrors(),
                'storeUrl'         => route('auth.restorePassword'),
                'loginUrl'         => route('login.index'),
                'commonErrors'     => $commonErrors ?? null,
                'successMessage'   => $session->getFlash('successMessage'),
            ],
        ]);
    }
}
