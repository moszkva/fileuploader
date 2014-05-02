<?php

namespace Moszkva\Fileuploader;

use \Symfony\Component\HttpFoundation\File\UploadedFile;

class Manager
{
	public function store(UploadedFile $file)
	{
		$file->getClientOriginalName();
		
		$fileResult = \DB::table('file')->where('name', $file->getClientOriginalName())->where('checksum', md5_file($file->getRealPath()))->first();
		
		if(isset($fileResult->id))
		{
			return $fileResult;
		}
		
		$fileResult = new Models\file;
		
		$fileResult->name			= $file->getClientOriginalName();
		$fileResult->size			= $file->getSize();
		$fileResult->mime_type		= $file->getMimeType();
		$fileResult->checksum		= md5_file($file->getRealPath());
		
		$fileResult->save();		
		
		$fileResult->path			= $this->moveFile($file, $fileResult->id);
		$fileResult->save();
		
		return $fileResult;		
	}
	
	public function getDestinationPath()
	{
		return \Config::get('fileuploader::paths.destination_path');
	}
	
	public function getFileById($id)
	{
		return Models\file::find($id);
	}
	
	public function getPathById($id)
	{
		$id = (string)$id;
		
		$parts = array();
		
		for($i=0; $i <= strlen($id)-1; $i++)
		{
			$parts[] = $id{$i};
		}
		
		return implode('/', $parts);		
	}
	
	public function initDirectory($path)
	{
		\File::makeDirectory($path, 511, true, true);
	}
	
	private function moveFile(UploadedFile $file, $id)
	{
		$destinationPath = $this->getDestinationPath().'/'.$this->getPathById($id);
		
		if($file->move($destinationPath, $file->getClientOriginalName()))
		{
			return $this->getPathById($id).'/'.$file->getClientOriginalName();
		}
		
		return '';
	}
	
}

?>