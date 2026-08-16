<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Controller;

use Neo\Core\Controller\AbstractController;
use Neo\Core\Database\Form\Form;
use Neo\Core\Database\Form\FormFactory;
use Neo\Core\Http\Request\Request;
use Neo\Core\Http\Response\Types\Response;
use Neo\Core\Routing\Attribute\MainRoute;
use Neo\Core\Routing\Attribute\Route;
use Neo\Core\Utils\Config\ConfigManager;
use Vendor\NeoPHP\AdminPackage\Service\AdminAuthManager;

#[MainRoute(path: '/admin/auth', name: 'admin.auth')]
final class AuthController extends AbstractController
{
    public function __construct(
        protected FormFactory $formFactory,
    ) {
    }

    #[Route(path: '/login', name: 'login', methods: ['GET'])]
    public function loginForm(): Response
    {
        $form = $this->buildLoginForm($this->formFactory);

        return $this->render('@NeoAdmin/pages/login.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(path: '/login', name: 'login.submit', methods: ['POST'])]
    public function login(Request $request, AdminAuthManager $auth, ConfigManager $config): Response
    {
        $form = $this->buildLoginForm($this->formFactory);
        $form->handleRequest($request->allBody());

        if (!$form->isValid()) {
            return $this->render('@NeoAdmin/pages/login.html.twig', [
                'form' => $form
            ]);
        }

        $data = $form->getData();
        $success = $auth->attempt(
            (string)$data['email'],
            (string)$data['password']
        );

        if (!$success) {
            $form->getField('password')?->addError('Invalid credentials.');

            return $this->render('@NeoAdmin/pages/login.html.twig', [
                'form' => $form
            ]);
        }

        $redirectRoute = $config->from('admin-system')
            ->get('auth.redirect_after_login', 'admin.panel.index');

        return $this->redirectToRoute($redirectRoute);
    }

    #[Route(path: '/logout', name: 'logout', methods: ['GET'])]
    public function logout(AdminAuthManager $auth): Response
    {
        $auth->logout();

        return $this->redirectToRoute('admin.auth.login');
    }

    private function buildLoginForm(FormFactory $formFactory): Form
    {
        return $formFactory->create('admin_login')
            ->add('email', 'email', [
                'label' => 'Email',
                'required' => true,
                'requiredMessage' => 'Email is required'
            ])
            ->add('password', 'password', [
                'label' => 'Password',
                'required' => true,
                'requiredMessage' => 'Password is required'
            ])
            ->getForm();
    }
}