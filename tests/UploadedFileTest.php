<?php

use Francerz\Http\HttpFactory;
use Francerz\Http\Utils\HttpFactoryManager;
use Francerz\Http\Utils\HttpHelper;
use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{
    public function testNormalizeFilesVar()
    {
        $http = new HttpHelper(new HttpFactoryManager(new HttpFactory()));
        $files = array(
            'single' => array(
                'error' => 0,
                'size' => 0,
                'tmp_name' => '/tmp/single.tmp',
                'name' => 'single.txt',
                'type' => 'text/plain'
            ),
            'multiple' => array(
                'error' => array(
                    0 => 0,
                    'root' => 0,
                    'inner'=> array(0 => 0)
                ),
                'size' => array(
                    0 => 0,
                    'root' => 0,
                    'inner'=> array(0 => 0)
                ),
                'tmp_name' => array(
                    0 => '/tmp/multiple0.tmp',
                    'root' => '/tmp/multipleRoot.tmp',
                    'inner'=> array(0 => '/tmp/multipleInner0.tmp')
                ),
                'name' => array(
                    0 => 'multiple0.txt',
                    'root' => 'multipleRoot.txt',
                    'inner'=> array(0 => 'multipleInner0.txt')
                ),
                'type' => array(
                    0 => 'text/plain',
                    'root' => 'text/plain',
                    'inner'=> array(0 => 'text/plain')
                )
            )
        );

        $expected = array(
            'single' => $http->createUploadedFile('/tmp/single.tmp', 0, 0, 'single.txt', 'text/plain'),
            'multiple' => array(
                0 => $http->createUploadedFile('/tmp/multiple0.tmp', 0, 0, 'multiple0.txt', 'text/plain'),
                'root' => $http->createUploadedFile('/tmp/multipleRoot.tmp', 0, 0, 'multipleRoot.txt', 'text/plain'),
                'inner' => array(
                    0 => $http->createUploadedFile('/tmp/multipleInner0.tmp', 0, 0, 'multipleInner0.txt', 'text/plain')
                )
            )
        );

        $actual = $http->normalizeFiles($files);

        $this->assertEquals($expected, $actual);

        $this->assertFalse(HttpHelper::isUploadedFileArray($files));
        $this->assertTrue(HttpHelper::isUploadedFileArray($actual));
        $this->assertTrue(HttpHelper::isUploadedFileArray($expected));

    }
}