<?php
namespace AppBundle\Security;

use AppBundle\Service\ConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * This voter applies to paths that have CONDITIONALLY_PUBLIC listed as a required role in security.yml.
 * It grants/denies access based on whether the path is currently listed as public.
 */
class ConditionallyPublicVoter implements VoterInterface
{
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token A TokenInterface instance
     * @param mixed $subject The subject to secure
     * @param array $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        if (!in_array('CONDITIONALLY_PUBLIC', $attributes)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if ($subject instanceof Request) {
            $routeName = $subject->attributes->get('_route');
        } elseif (is_string($subject)) {
            $routeName = $subject;
        } else {
            $routeName = null;
        }

        if (!$routeName) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if ($this->configService->getConfig()->isPagePublic($routeName)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
