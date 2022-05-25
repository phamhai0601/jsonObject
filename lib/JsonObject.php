<?php
/**
 * Created by FesVPN.
 * @project json-object
 * @author  Pham Hai
 * @email   mitto.hai.7356@gmail.com
 * @date    21/4/2022
 * @time    9:15 PM
 */

namespace JsonObject;
class JsonObject {

	public $path;

	public $json;

	/**
	 * JsonObject constructor.
	 *
	 * @param string $path
	 */
	public function __construct(string $path) {
		$this->path = $path;
		$this->json = $this->json = file_get_contents($this->path);
	}

	/**
	 * @param string $json
	 *
	 * @return mixed
	 */
	public function create(string $json) {
		file_put_contents($this->path, $json, LOCK_EX);
		return json_decode($json);
	}

	/**
	 * @param array|null $keys
	 *
	 * @return mixed|null
	 */
	public function get(array $keys = null) {
		try {
			$arrJson = json_decode($this->json, true);
			if ($keys == null) {
				return $arrJson;
			} else {
				$flag     = true;
				$index    = 0;
				$response = $arrJson;
				while ($flag == true) {
					$response = $response[$keys[$index]];
					$index ++;
					if (!isset($keys[$index])) {
						$flag = false;
					}
				}
				return $response;
			}
			return [];
		} catch (\Exception $exception) {
			return [];
		}
	}

	/**
	 * @param array $keys
	 * @param       $value
	 *
	 * @return bool
	 */
	public function set($value, array $keys): bool {
		try {
			$arrJson  = $this->get();
			$flag     = true;
			$index    = 0;
			$response = &$arrJson;
			if (count($keys) == 1) {
				$response[$keys[0]] = $value;
				$this->update(json_encode($response));
				return true;
			} else {
				while ($flag == true) {
					$response = &$response[$keys[$index]];
					$index ++;
					if (!isset($keys[$index + 1])) {
						$response[$keys[$index]] = $value;
						$this->update(json_encode($arrJson));
						return true;
					}
				}
			}
		} catch (\Exception $exception) {
			return false;
		}
	}

	/**
	 *
	 * @param array $keys
	 *
	 * @return bool
	 */
	public function delete(array $keys): bool {
		try {
			$arrJson  = $this->get();
			$flag     = true;
			$index    = 0;
			$response = &$arrJson;
			if (count($keys) == 1) {
				unset($response[$keys[0]]);
				$this->update(json_encode($response));
				return true;
			}
			while ($flag == true) {
				$response = &$response[$keys[$index]];
				$index ++;
				if (!isset($keys[$index + 1])) {
					unset($response[$keys[$index]]);
					$this->update(json_encode($arrJson));
					return true;
				}
			}
		} catch (\Exception $exception) {
			return false;
		}
	}

	/**
	 * @param null  $key
	 * @param null  $value
	 * @param array $keys
	 *
	 * @return bool
	 */
	public function insert($key = null, $value = null, array $keys = []): bool {
		if (is_null($key) || is_null($value)) {
			return false;
		}
		try {
			$arrJson  = $this->get();
			$flag     = true;
			$index    = 0;
			$response = &$arrJson;
			if (empty($keys)) {
				$arrJson[$key] = $value;
				$this->update(json_encode($arrJson));
				return true;
			}
			while ($flag == true) {
				$response = &$response[$keys[$index]];
				$index ++;
				if (!isset($keys[$index + 1])) {
					unset($response[$keys[$index]]);
					$this->update(json_encode($arrJson));
					return true;
				}
			}
		} catch (\Exception $exception) {
		}
		return false;
	}

	/**
	 * @param string $json
	 */
	public function update(string $json) {
		$this->json = $json;
		$this->create($json);
	}
}
