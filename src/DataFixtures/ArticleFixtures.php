<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */

    private static array $articleTitles = [
        'Есть ли жизнь после девятой жизни?',
        'Когда в машинах поставят лоток?',
        'В погоне за красной точкой',
        'В чем смысл жизни сосисок',
    ];

    private static array $articleImages = [
        'car1.jpg',
        'car2.jpg',
        'car3.jpeg',
    ];

    private FileUploader $articleFileUploader;

    public function __construct(FileUploader $articleFileUploader)
    {
        $this->articleFileUploader = $articleFileUploader;
    }


    /**
     * @throws Exception
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Article::class, 10, function (Article $article) use ($manager) {
            $article
                ->setTitle($this->faker->randomElement(self::$articleTitles))
                ->setBody('
Lorem ipsum **красная точка** dolor sit amet, consectetur adipiscing elit, sed
do eiusmod tempor incididunt [Сметанка](/) ut labore et dolore magna aliqua.'
                    . $this->faker->paragraphs($this->faker->numberBetween(2, 5), true));

            if ($this->faker->boolean(60)) {
                $article->setPublishedAt(new \DateTimeImmutable(sprintf('-%d days', rand(0, 50))));
            }

            $fileName = $this->faker->randomElement(self::$articleImages);

            $tmpFileName = sys_get_temp_dir() . '/' . $fileName;

            (new Filesystem())->copy(dirname(dirname(__DIR__)) . '/public/images/' . $fileName, $tmpFileName, true);

            $article
                ->setAuthor($this->getRandomReference(User::class))
                ->setLikeCount(rand(0, 10))
                ->setImageFilename($this->articleFileUploader->uploadFile(new File($tmpFileName)))
            ;

            $manager->persist($article);
            $manager->flush();

            $tags = [];
            for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++){
                $tags[] = $this->getRandomReference(Tag::class);
            }

            foreach ($tags as $tag){
                $article->addTag($tag);
                $tag->getName();
            }
        });
    }

    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
            UserFixtures::class,
        ];
    }
}
