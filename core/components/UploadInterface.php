<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\components;

interface UploadInterface
{
    public function upload($source, $target);

    public function uploadThumbnails($srcFile, $target, $type);
}
