<?php
namespace JimLind\Helpers;

class FileHelper
{
    public function csvFileToArray(string $inputFileName): array {
        $file = new \SplFileObject($inputFileName);
        $file->setFlags(\SplFileObject::READ_CSV);
        $iterator = new \LimitIterator($file, 1);

        return iterator_to_array($iterator, false);
    }
}