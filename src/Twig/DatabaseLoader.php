<?php
namespace App\Twig;

use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class DatabaseLoader implements LoaderInterface
{
    protected $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Template::class);
    }

    public function getSourceContext($name)
    {
        if (false === $template = $this->getTemplate($name)) {
            throw new LoaderError(sprintf('Template "%s" does not exist.', $name));
        }

        return new Source($template->getSource(), $name);
    }

    public function exists($name)
    {
        return (bool)$this->getTemplate($name);
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
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
    protected function getTemplate($name)
    {
        $template = $this->repo->findOneBy(['filename' => str_replace('dynamic/', '', $name)]);

        // Return null if the template is blank in the database: this will cause it to fall back to the filesystem
        // loader.
        if ($template && $template->getSource() === '') {
            return null;
        }

        return $template;
    }
}
