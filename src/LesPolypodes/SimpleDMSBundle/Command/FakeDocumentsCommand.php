<?php
/**
 * This file is part of the SimpleDMS package.
 *
 * (c) 2015 Les Polypodes
 * Made in Nantes, France - http://lespolypodes.com
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * File created by ronan@lespolypodes.com
 */

namespace LesPolypodes\SimpleDMSBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Faker;
use PhpOffice;

/**
 * Class FakeDocumentsCommand.
 */
class FakeDocumentsCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    protected $availableFormats = ['docx', 'txt', 'md', 'jpg'];
    /**
     * @var array
     */
    protected $randomFonts = ["Arial", "Comic Sans MS", "Courier New", "Georgia", "Times New Roman", "Trebuchet MS", "Verdana"];
    /**
     * @var int
     */
    protected $maxQuantity = 50;
    /**
     * @var null
     */
    protected $resources = null;
    /**
     * @var null
     */
    protected $faker = null;

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('dms:fake')
            ->setDescription('Generate dozens of fake documents')
            ->addArgument('extension', InputArgument::OPTIONAL, 'Specify extension to generate: docx, jpg, txt, pdf, md or all')
            ->addArgument('quantity', InputArgument::OPTIONAL, 'Specify quantity to generate: from 1 (default) to '.$this->maxQuantity)
        ;

        //$this->faker = Faker::cre
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extension = $input->getArgument('extension');
        $quantity = $input->getArgument('quantity');
        $extension = (!empty($extension)) ? $extension : 'docx';
        $quantity = (!empty($quantity)) ? $quantity : 1;
        $files = $this->createDocument($extension, $quantity);
        $output->writeln(sprintf("%d document(s) created in %s", $quantity, dirname($files[0])));
    }

    /**
     * @param string $extension
     * @param int    $quantity
     *
     * @return array|null
     */
    protected function createDocument($extension = 'docx', $quantity = 1)
    {
        $fakeDir = $this->getContainer()->get('file_locator')->locate('@LesPolypodesSimpleDMSBundle/Resources/fake');
        $result = null;
        if ($quantity > $this->maxQuantity) {
            throw new \InvalidArgumentException(sprintf("Please do not create more than %d document at once", $this->maxQuantity));
        }
        switch ($extension) {
            case "jpg":
                for ($i = 0; $i < $quantity; $i++) {
                    $result[] = $this->createImage($fakeDir);
                }
                break;
            case "txt":
                for ($i = 0; $i < $quantity; $i++) {
                    $result[] = $this->createTxtFile($fakeDir);
                }
                break;
            case "md": // markdown
                for ($i = 0; $i < $quantity; $i++) {
                    $result[] = $this->createMarkdownFile($fakeDir);
                }
                break;
            case "pdf": // markdown
                for ($i = 0; $i < $quantity; $i++) {
                    $result[] = $this->createPdfFile($fakeDir);
                }
                break;
            case "docx":
                for ($i = 0; $i < $quantity; $i++) {
                    $result[] = $this->createWordFile($fakeDir);
                }
                break;
            case "all":
                for ($i = 0; $i < $quantity; $i++) {
                    $result[] = $this->createWordFile($fakeDir);
                    $result[] = $this->createTxtFile($fakeDir);
                    $result[] = $this->createMarkdownFile($fakeDir);
                    $result[] = $this->createPdfFile($fakeDir);
                    $result[] = $this->createImage($fakeDir);
                }
                break;
            default:
                throw new \InvalidArgumentException(sprintf("%s : %s", "Please give me a valid extension", implode(", ", $this->availableFormats)));
                break;
        }

        return $result;
    }

    /**
     * @param $fakeDir
     *
     * @return string
     */
    protected function createImage($fakeDir)
    {
        $faker = Faker\Factory::create('fr_FR');
        $image = Faker\Provider\Image::image();
        $result = sprintf("%s/image-%s.jpg", $fakeDir, $faker->slug(3));
        rename($image, $result);

        return $result;
    }

    /**
     * @param $fakeDir
     *
     * @return string
     */
    protected function createTxtFile($fakeDir)
    {
        $faker = Faker\Factory::create('fr_FR');

        $elements = array(
           "\t".$faker->sentence(),
        );

        for ($i = 0; $i <= mt_rand(10, 30); $i++) {
            $elements[] = $faker->paragraph(mt_rand(30, 50));
        }

        $elements[] = "This is a fake (RAW) TXT file.";
        $txt = implode("\n\r\n\r", $elements);

        $result = sprintf("%s/%s.%s", $fakeDir, $faker->slug(3), 'txt');
        file_put_contents($result, $txt);

        return $result;
    }

    /**
     * @param string $fakeDir fakeDir path
     * @param string $endType The generated document end type, other than natural markdown (ex: pdf, etc.)
     *
     * @return string
     */
    protected function createMarkdownFile($fakeDir, $endType = "MARKDOWN (md)")
    {
        $faker = Faker\Factory::create('fr_FR');

        $elements = array(
            "#".$faker->sentence(),
            "Edited by: [".$faker->company."](".$faker->url.").",
        );

        for ($i = 0; $i <= mt_rand(10, 30); $i++) {
            $elements[] = "##".$faker->sentence();
            $elements[] = "__".$faker->sentence()."__ ".$faker->paragraph(mt_rand(30, 50));
        }
        $elements[] = sprintf(" This is a fake %s file", $endType);
        $txt = implode("\n\r\n\r", $elements);

        $result = sprintf("%s/%s.%s", $fakeDir, $faker->slug(3), 'md');
        file_put_contents($result, $txt);

        return $result;
    }

    /**
     * @param $fakeDir
     *
     * @return mixed
     */
    protected function createPdfFile($fakeDir)
    {
        $pandocLocation = exec('which pandoc');
        if (false == strpos($pandocLocation, 'pandoc')) {
            throw new \InvalidArgumentException("PDF document cannot be generated: Please install Pandoc: http://johnmacfarlane.net/pandoc/installing.html");
        }

        $markdown = $this->createMarkdownFile($fakeDir, "Portable Document Format (PDF)");
        $result = str_replace('.md', '.pdf', $markdown);
        exec(sprintf("pandoc %s -o %s", $markdown, $result));
        unlink($markdown);

        return $result;
    }

    /**
     * @param $fakeDir
     *
     * @return string
     *
     * @throws PhpOffice\PhpWord\Exception\Exception
     */
    protected function createWordFile($fakeDir)
    {
        $randomFont = $this->randomFonts[array_rand($this->randomFonts)];
        $faker = Faker\Factory::create('fr_FR');

        $phpWord = new PhpOffice\PhpWord\PhpWord();
        $phpWord->addTitleStyle(1, array('name' => $randomFont, 'size' => 26));
        $phpWord->addTitleStyle(2, array('name' => $randomFont, 'size' => 16));
        $section = $phpWord->addSection();
        $section->addTitle($faker->sentence(), 1);
        $section->addTextBreak();
        for ($i = 0; $i <= mt_rand(10, 30); $i++) {
            $section->addTitle($faker->sentence(), 2);
            $section->addTextBreak();
            $section->addText(
                htmlspecialchars($faker->paragraph(mt_rand(30, 50))),
                array(
                    'name'  => $randomFont,
                    'size'  => 11,
                    //'color' => str_replace('#', '', $faker->hexcolor)
                )
            );

            $section->addText("This is a fake Microsoft Word file.");
            $section->addTextBreak();
        }
        $objWriter = PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $result = sprintf("%s/%s.%s", $fakeDir, $faker->slug(3), 'docx');
        $objWriter->save($result);

        return $result;
    }
}
