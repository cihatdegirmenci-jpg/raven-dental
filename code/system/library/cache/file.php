<?php
namespace Cache;
class File {
	private $expire;

	public function __construct($expire = 3600) {
		$this->expire = $expire;
		
		// Cache klasörü kontrolü
		if (!is_dir(DIR_CACHE)) {
			@mkdir(DIR_CACHE, 0755, true);
		}

		$files = glob(DIR_CACHE . 'cache.*');

		if ($files) {
			foreach ($files as $file) {
				$time = substr(strrchr($file, '.'), 1);

				if ($time < time()) {
					if (file_exists($file)) {
						unlink($file);
					}
				}
			}
		}
	}

	public function get($key) {
		// Cache klasörü kontrolü
		if (!is_dir(DIR_CACHE)) {
			@mkdir(DIR_CACHE, 0755, true);
		}
		
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			$handle = @fopen($files[0], 'r');
			
			if ($handle === false) {
				return false;
			}

			@flock($handle, LOCK_SH);

			$size = @filesize($files[0]);
			if ($size === false) {
				@flock($handle, LOCK_UN);
				@fclose($handle);
				return false;
			}

			$data = @fread($handle, $size);

			@flock($handle, LOCK_UN);

			@fclose($handle);

			return json_decode($data, true);
		}

		return false;
	}

	public function set($key, $value) {
		// Cache klasörü kontrolü
		if (!is_dir(DIR_CACHE)) {
			if (!@mkdir(DIR_CACHE, 0755, true)) {
				return false;
			}
		}
		
		// Klasör yazılabilir mi kontrol et
		if (!is_writable(DIR_CACHE)) {
			return false;
		}
		
		$this->delete($key);

		$file = DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $this->expire);

		$handle = @fopen($file, 'w');
		
		if ($handle === false) {
			return false;
		}

		@flock($handle, LOCK_EX);

		@fwrite($handle, json_encode($value));

		@fflush($handle);

		@flock($handle, LOCK_UN);

		@fclose($handle);
	}

	public function delete($key) {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
	}
}