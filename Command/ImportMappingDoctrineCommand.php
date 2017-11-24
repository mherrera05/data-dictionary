<?php

namespace DataDictionaryBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use DataDictionaryBundle\Tools\Export\ClassMetadataExporter;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ImportMappingDoctrineCommand extends DoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('data:dictionary:import')
            ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle to import the mapping information to')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command')
            ->addOption('filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A string pattern used to match entities that should be mapped.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force to overwrite existing mapping files.')
            ->setDescription('Imports mapping information from an existing database')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command imports mapping information
from an existing database:

<info>php %command.full_name% "MyCustomBundle" xml</info>

You can also optionally specify which entity manager to import from with the
<info>--em</info> option:

<info>php %command.full_name% "MyCustomBundle" xml --em=default</info>

If you don't want to map every entity that can be found in the database, use the
<info>--filter</info> option. It will try to match the targeted mapped entity with the
provided pattern string.

<info>php %command.full_name% "MyCustomBundle" xml --filter=MyMatchedEntity</info>

Use the <info>--force</info> option, if you want to override existing mapping files:

<info>php %command.full_name% "MyCustomBundle" xml --force</info>
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument('bundle'));
        $destPath = $bundle->getPath();
        $destPath .= '/Resources/config/dictionary';
        $cme = new ClassMetadataExporter();
        $exporter = $cme->getExporter('json');
        $exporter->setOverwriteExistingFiles($input->getOption('force'));

        /*if ('annotation' === $type) {
            $entityGenerator = $this->getEntityGenerator();
            $exporter->setEntityGenerator($entityGenerator);
        }*/

        $em = $this->getEntityManager($input->getOption('em'), $input->getOption('shard'));

        $databaseDriver = new DatabaseDriver($em->getConnection()->getSchemaManager());
        $em->getConfiguration()->setMetadataDriverImpl($databaseDriver);

        $emName = $input->getOption('em');
        $emName = $emName ? $emName : 'default';

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);
        $metadata = $cmf->getAllMetadata();
        $metadata = MetadataFilter::filter($metadata, $input->getOption('filter'));
        if ($metadata) {
            $output->writeln(sprintf('Importing mapping information from "<info>%s</info>" entity manager', $emName));
            foreach ($metadata as $class) {
                $className = $class->name;
                $class->name = $bundle->getNamespace().'\\Entity\\'.$className;
                $path = $destPath.'/'.str_replace('\\', '.', $className).'.orm.json';
                $output->writeln(sprintf('  > writing <comment>%s</comment>', $path));
                $code = $exporter->exportClassMetadata($class);
                if (!is_dir($dir = dirname($path))) {
                    mkdir($dir, 0775, true);
                }
                file_put_contents($path, $code);
                chmod($path, 0664);
            }

            return 0;
        } else {
            $output->writeln('Database does not have any mapping information.', 'ERROR');
            $output->writeln('', 'ERROR');

            return 1;
        }
    }
}
