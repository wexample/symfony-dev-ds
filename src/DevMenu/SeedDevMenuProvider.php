<?php

namespace Wexample\SymfonyDevDs\DevMenu;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Wexample\SymfonyDesignSystem\Interface\DevMenuProviderInterface;
use Wexample\SymfonyDev\Controller\SeedController;
use Wexample\SymfonyDev\Service\SeedService;

/**
 * « Reload the demonstration data » of symfony-dev in the development menu,
 * for a signed-in account — shown unavailable to the others: asked first, the
 * page held under a spinner while the database is filled, then the sign-in,
 * since the accounts were replaced too.
 */
class SeedDevMenuProvider implements DevMenuProviderInterface
{
    private const string DOMAIN = 'WexampleSymfonyDevDsBundle.common.dev_menu::seed.';

    public function __construct(
        private readonly SeedService $seedService,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security,
        private readonly CsrfTokenManagerInterface $csrf,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getDevMenuItems(): array
    {
        // Nothing to load, or symfony-dev's routes left out of the
        // application's routing: no entry rather than one leading nowhere.
        if (! $this->seedService->hasSeeders()) {
            return [];
        }

        try {
            $url = $this->urlGenerator->generate(SeedController::ROUTE_SEED);
        } catch (RouteNotFoundException) {
            return [];
        }

        $item = [
            'icon' => 'ph:bold/arrow-counter-clockwise',
            'label' => self::DOMAIN.'label',
            'account' => true,
            // The heaviest action of the menu: at its very end.
            'order' => 100,
        ];

        // Signed out, the entry stays, unavailable: the reload is a signed-in
        // account's, and asks for its token.
        if (! $this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return [$item + ['disabled' => true]];
        }

        return [$item + [
            'href' => $url,
            'method' => 'post',
            'token' => $this->csrf->getToken(SeedController::CSRF_SEED)->getValue(),
            // Written as they are into the form's attributes: said in words here.
            'confirm' => [
                'title' => $this->translator->trans(self::DOMAIN.'confirm.title'),
                'message' => $this->translator->trans(self::DOMAIN.'confirm.message'),
                'accept' => $this->translator->trans(self::DOMAIN.'confirm.accept'),
            ],
            // The answer is a whole new page, slow to come.
            'busy' => true,
        ]];
    }
}
