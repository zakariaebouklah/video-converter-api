<?php

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Entity\Conversion;
use Doctrine\ORM\EntityManagerInterface;

class Factory
{
    private string $projectDir;
    private EntityManagerInterface $manager;
    private Extractor $extractor;

    public function __construct(
        string $projectDir,
        EntityManagerInterface $manager,
        Extractor $extractor
    )
    {

        $this->projectDir = $projectDir;
        $this->manager = $manager;
        $this->extractor = $extractor;
    }

    public function convertMP3(Conversion $conversion): void
    {
        /**
         * @var string $url
         */
        $url = $conversion->getYtUrl();
        $v_id = $this->extractor->extractVideoId($url);

        $conversion->setStartedAt(new \DateTimeImmutable());

        $conversion->setStatus(Conversion::STATUSES[1]);

        /**
         * TODO: test if file converted already don't do the conversion instead change output name.
         */

        $outputPath = "audios/{$v_id}.mp3";

        $proc = new Process(["{$this->projectDir}\bin\yt-dlp", "-f", "bestaudio", "--extract-audio", "--audio-format", "mp3", "--audio-quality", "0", $url, "-o", $outputPath]);
        $proc->run();

        if(!$proc->isSuccessful()) throw new ProcessFailedException($proc);

        $conversion->setStatus(Conversion::STATUSES[2]);
        $conversion->setFinishedAt(new \DateTimeImmutable());
        $conversion->setFormat("mp3");
        $conversion->setFilePath($outputPath);

        $this->manager->persist($conversion);
        $this->manager->flush();

    }

    public function convertMP4(Conversion $conversion): void
    {
        /**
         * @var string $url
         */
        $url = $conversion->getYtUrl();
        $v_id = $this->extractor->extractVideoId($url);

        $conversion->setStartedAt(new \DateTimeImmutable());

        $conversion->setStatus(Conversion::STATUSES[1]);

        $outputPath = "videos/{$v_id}.mp4";

        $proc = new Process(["{$this->projectDir}\bin\yt-dlp", "-S ext:mp4:m4a", $url, "-o", $outputPath]);
        $proc->run();

        if(!$proc->isSuccessful()) throw new ProcessFailedException($proc);

        $conversion->setStatus(Conversion::STATUSES[2]);
        $conversion->setFinishedAt(new \DateTimeImmutable());
        $conversion->setFormat("mp4");
        $conversion->setFilePath($outputPath);

        $this->manager->persist($conversion);
        $this->manager->flush();

    }
}