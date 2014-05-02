<?php namespace Moszkva\Fileuploader\Facades;
 
use Illuminate\Support\Facades\Facade;
 
class Fileuploader extends Facade
{ 
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{ 
		return 'Fileuploader'; 
	}
 
}

?>