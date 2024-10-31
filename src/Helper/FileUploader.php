<?php

namespace App\Helper;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(private ParameterBagInterface $parameterBag, private SluggerInterface $slugger) {}


    public function upload(UploadedFile $file, string $name): string
    {

        $dir = $this->parameterBag->get('poster_dir');
        $name = $this->slugger->slug($name) . '-' . uniqid() . '.' . $file->guessExtension();
        $file->move($dir, $name);

        return $name;
    }
}