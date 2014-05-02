<?php

namespace Moszkva\Fileuploader\Test;

use Moszkva\Fileuploader\Manager;
use Moszkva\Fileuploader\FileuploaderServiceProvider;

class ManagerTest extends \TestCase
{	
	private $testFilePath	= 'tests/upload/test.txt';
	private $tempUploadDir	= 'tests/upload/tmp';
	
	public function setUp()
	{
		parent::setUp();

		if(!\Schema::hasTable('migrations'))
		{
			\Artisan::call('migrate', array('--env' => 'testing', '--bench' => 'moszkva/fileuploader'));
		}		
		
		$this->initTestFile();
		
		$FileUploaderServiceProvider = new FileuploaderServiceProvider($this->app);
		
		$FileUploaderServiceProvider->boot();
	}
	
	private function initTestFile($content = 'test')
	{
		file_put_contents($this->testFilePath, $content);
	}
	
    public function tearDown() 
	{
		\File::deleteDirectory('tests/upload/tmp', true);
	}	

	public function testTrue()
	{
		$this->assertTrue(true);
	}
	
	public function testConfigRetriveDestPath()
	{
		$manager = new \Moszkva\Fileuploader\Manager();
		$this->assertTrue(strlen($manager->getDestinationPath()) > 0);
	}
	
	public function getPathByIdDataprovider()
	{
		return array(
			array(1, '1'),
			array(0, '0'),
			array(11, '1/1'),
			array(112, '1/1/2'),
			array(12345, '1/2/3/4/5'),
			array('123450', '1/2/3/4/5/0')
		);
	}
	
	/**
	 * @dataProvider getPathByIdDataprovider
	 */
	public function testGetPathById($id, $return)
	{
		$manager = new \Moszkva\Fileuploader\Manager();
				
		$this->assertEquals($manager->getPathById($id), $return);
	}
	
	public function testInitDirectory()
	{
		$manager = new \Moszkva\Fileuploader\Manager();

		$manager->initDirectory($this->tempUploadDir.'/'.$manager->getPathById('1234'));
		
		$this->assertTrue(is_dir($this->tempUploadDir.'/'.$manager->getPathById('1234')));
	}
	
	public function testUploader()
	{
		$checksum	= md5_file($this->testFilePath);
		$mimeType	= mime_content_type($this->testFilePath);
		$fileSize	= filesize($this->testFilePath);
		
		$uploadedFile = new \Symfony\Component\HttpFoundation\File\UploadedFile($this->testFilePath, basename($this->testFilePath), mime_content_type($this->testFilePath), $fileSize, null, true);

		$manager = new \Moszkva\Fileuploader\Manager();
		
		$file = $manager->store($uploadedFile);
		
		$this->assertTrue(file_exists($this->tempUploadDir.'/'.$file->path));
		$this->assertEquals($file->name, basename($this->testFilePath));
		$this->assertEquals($file->path, $manager->getPathById($file->id).'/'.$file->name);
		$this->assertEquals($file->checksum, $checksum);
		$this->assertEquals($file->mime_type, $mimeType);
		$this->assertEquals($file->size, $fileSize);
		
		$this->initTestFile();
		
		$uploadedFile = new \Symfony\Component\HttpFoundation\File\UploadedFile($this->testFilePath, basename($this->testFilePath), mime_content_type($this->testFilePath), filesize($this->testFilePath), null, true);

		$manager = new \Moszkva\Fileuploader\Manager();
		
		$fileNew = $manager->store($uploadedFile);
		
		$this->assertEquals($file->id, $fileNew->id);
		
		$this->initTestFile('test.');
		
		$uploadedFile = new \Symfony\Component\HttpFoundation\File\UploadedFile($this->testFilePath, basename($this->testFilePath), mime_content_type($this->testFilePath), filesize($this->testFilePath), null, true);

		$manager = new \Moszkva\Fileuploader\Manager();
		
		$fileNew = $manager->store($uploadedFile);
		
		$this->assertTrue($file->id < $fileNew->id);		
	}

}

?>