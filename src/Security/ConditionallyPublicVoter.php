<?php
namespace App\Security;

use App\Service\ConfigService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * This voter applies to paths that have CONDITIONALLY_PUBLIC listed as a required role in security.yml.
 * It grants/denies access based on whether the path is currently listed as public.
 */
class ConditionallyPublicVoter implements VoterInterface
{
    private ConfigService $configService;
    private LoggerInterface $logger;

    public function __construct(ConfigService $configService, LoggerInterface $logger)
    {
        $this->configService = $configService;
        $this->logger = $logger;
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
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if (!in_array('CONDITIONALLY_PUBLIC', $attributes)) {
            $this->logger->debug('Route doesn\'t have CONDITIONALLY_PUBLIC', $attributes);
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if ($subject instanceof Request) {
            $routeName = $subject->attributes->get('_route');
        } elseif (is_string($subject)) {
            $routeName = $subject;
        } else {
            $routeName = null;
        }
        $this->logger->debug('Route name: ' . $routeName);

        if (!$routeName) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if ($this->configService->getConfig()->isPagePublic($routeName)) {
            $this->logger->debug('Page public: ' . $routeName);
            return VoterInterface::ACCESS_GRANTED;
        }

        $this->logger->debug('Page not public: ' . $routeName, $attributes);
        return VoterInterface::ACCESS_DENIED;
    }
}
