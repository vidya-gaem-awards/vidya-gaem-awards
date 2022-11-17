<?php
namespace App\Twig;

use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class DatabaseLoader implements LoaderInterface
{
    protected EntityRepository $repo;

    /** @var bool */
    protected bool $enabled;

    public function __construct(bool $enabled, EntityManagerInterface $em)
    {
        $this->enabled = $enabled;
        if (!$enabled) {
            return;
        }

        $this->repo = $em->getRepository(Template::class);
    }

    public function getSourceContext(string $name): Source
    {
        if (false === $template = $this->getTemplate($name)) {
            throw new LoaderError(sprintf('Template "%s" does not exist.', $name));
        }

        return new Source($template->getSource(), $name);
    }

    public function exists(string $name): bool
    {
        return (bool)$this->getTemplate($name);
    }

    public function getCacheKey(string $name): string
    {
        return $name;
    }

    public function isFresh(string $name, int $time): bool
    {
        if (false === $template = $this->getTemplate($name)) {
            return false;
        }

        return $template->getLastUpdated()->getTimestamp() <= $time;
    }

    /**
     * @param $name
     * @return Template|null
     */
    protected function getTemplate($name): ?Template
    {
        if (!$this->enabled || substr($name, 0, 8) !== 'dynamic/') {
            return null;
        }

        $template = $this->repo->findOneBy(['filename' => str_replace('dynamic/', '', $name)]);

        // Return null if the template is blank in the database: this will cause it to fall back to the filesystem
        // loader.
        if ($template && $template->getSource() === '') {
            return null;
        }

        return $template;
    }
}
