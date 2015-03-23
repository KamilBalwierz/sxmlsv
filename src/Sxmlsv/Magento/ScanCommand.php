<?php

namespace Sxmlsv\Magento;

use N98\Magento\Command\AbstractMagentoCommand;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;

class ScanCommand extends AbstractMagentoCommand
{
    /** @var InputInterface $input */
    protected $input;

    /** @var OutputInterface $input */
    protected $output;

    protected $level = [
        LIBXML_ERR_WARNING => 'Warning',
        LIBXML_ERR_ERROR => 'Error',
        LIBXML_ERR_FATAL => 'Fatal Error',
        LIBXML_ERR_NONE => 'None',
    ];

    protected $errors = [];

    protected $totalScanned = 0;

    protected function configure()
    {
        $this
            ->setName('sxmlsv:scan')
            ->addArgument('directoryToScan', InputArgument::OPTIONAL, 'Directory to scan, if not supplied will scan whole magento directory')
            ->setDescription('Performs recursive scan of given directory to see if SimpleXml library can load found .xml documents');
    }

    /**
     * Execute command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $startPath = $this->input->getArgument('directoryToScan');
        if (empty($startPath)) {
            $startPath = $this->_magentoRootFolder;
        }
        if (empty($startPath)) {
            $startPath = '.';
        }

        $this->output->writeln("<info>Scanning files in '$startPath' for mallformed XML files</info>");

        $this->scanDir($startPath);

        $total = count($this->errors);
        $this->output->writeln("<info>Scanned $this->totalScanned files finding $total problems</info>");

        $this->getHelper('table')->setHeaders(['File', 'Level', 'Message', 'Line', 'Column'])->setRows($this->errors)->render($output);
    }

    protected function scanDir($directory)
    {
        $directory = rtrim($directory, DIRECTORY_SEPARATOR);
        $files = scandir($directory);
        if(is_array($files)) {
            foreach ($files as $file) {
                if (preg_match('/^.{1,2}$/', $file)) {
                    continue;
                }
                if (is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
                    $this->scanDir($directory . DIRECTORY_SEPARATOR . $file);
                } else if (preg_match('/\.xml$/i', $file)) {
                    $this->testFile($directory . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
    }

    protected function testFile($file)
    {
        ++$this->totalScanned;
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $response = simplexml_load_file($file);
        if ($response === false) {
            $errors = libxml_get_errors();
            if(!empty($errors)) {
                foreach ($errors as $error) {
                    $this->errors[] = [
                        $file,
                        $this->level[$error->level],
                        $error->message,
                        $error->line,
                        $error->column,
                    ];
                }
            }
        }
    }
}