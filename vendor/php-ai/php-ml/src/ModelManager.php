<?php

declare(strict_types=1);

namespace Phpml;

use Phpml\Exception\FileException;
use Phpml\Exception\SerializeException;

class ModelManager
{
    public function saveToFile(Estimator $estimator, string $filepath): void
    {
        if (!is_writable(dirname($filepath))) {
            throw new FileException(sprintf('File "%s" can\'t be saved.', basename($filepath)));
        }

        $serialized = serialize($estimator);
        if (!isset($serialized[0])) {
            throw new SerializeException(sprintf('Class "%s" can not be serialized.', gettype($estimator)));
        }

        $result = file_put_contents($filepath, $serialized, LOCK_EX);
        if ($result === false) {
            throw new FileException(sprintf('File "%s" can\'t be saved.', basename($filepath)));
        }
    }

    public function restoreFromFile(string $filepath): Estimator
    {
        if (!file_exists($filepath) || !is_readable($filepath)) {
            throw new FileException(sprintf('File "%s" can\'t be open.', basename($filepath)));
        }

        $object = unserialize((string) file_get_contents($filepath), [Estimator::class]);
        if ($object === false) {
            throw new SerializeException(sprintf('"%s" can not be unserialized.', basename($filepath)));
        }

        return $object;
    }

    public function get_serialized_data(Estimator $estimator) {
        //$serialized = bzcompress(serialize($estimator), 9);
        $serialized = serialize($estimator);
        if (empty($serialized)) {
            throw SerializeException::cantSerialize(gettype($estimator));
        }

        return $serialized;
    }

    public function restore_serialized_data($estimator) : Estimator {
        //$object = unserialize(bzdecompress($estimator));
        $object = unserialize($estimator);
        if ($object  === false ) {
            throw SerializeException::cantUnserialize(gettype($estimator));
        }

        return $object;
    }
}
