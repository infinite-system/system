<?php

namespace System\Essence;

/**
 * Intelligent infinite system ∞ options processor.
 * options simplifier, shortcuter, appender,
 * transformer, converter to javaScript.
 *
 * @copyright Evgeny Kalashnikov 2015 - infinity
 * @author Evgeny Kalashnikov
 */
class Options
{

	public static $alphas = [
		"uppercase" => ["A" => "a", "B" => "b", "C" => "c", "D" => "d", "E" => "e", "F" => "f", "G" => "g", "H" => "h", "I" => "i", "J" => "j", "K" => "k", "L" => "l", "M" => "m", "N" => "n", "O" => "o", "P" => "p", "Q" => "q", "R" => "r", "S" => "s", "T" => "t", "U" => "u", "V" => "v", "W" => "w", "X" => "x", "Y" => "y", "Z" => "z", 0 => "0", 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5", 6 => "6", 7 => "7", 8 => "8", 9 => "9", "+" => "+", "-" => "-", "^" => "^", "_" => "_"],
		"lowercase" => ["a" => "A", "b" => "B", "c" => "C", "d" => "D", "e" => "E", "f" => "F", "g" => "G", "h" => "H", "i" => "I", "j" => "J", "k" => "K", "l" => "L", "m" => "M", "n" => "N", "o" => "O", "p" => "P", "q" => "Q", "r" => "R", "s" => "S", "t" => "T", "u" => "U", "v" => "V", "w" => "W", "x" => "X", "y" => "Y", "z" => "Z", 0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, "+" => "+", "-" => "-", "^" => "^", "_" => "_"],
		"all" => [0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, "A" => "a", "a" => "a", "B" => "b", "b" => "b", "C" => "c", "c" => "c", "D" => "d", "d" => "d", "E" => "e", "e" => "e", "F" => "f", "f" => "f", "G" => "g", "g" => "g", "H" => "h", "h" => "h", "I" => "i", "i" => "i", "J" => "j", "j" => "j", "K" => "k", "k" => "k", "L" => "l", "l" => "l", "M" => "m", "m" => "m", "N" => "n", "n" => "n", "O" => "o", "o" => "o", "P" => "p", "p" => "p", "Q" => "q", "q" => "q", "R" => "r", "r" => "r", "S" => "s", "s" => "s", "T" => "t", "t" => "t", "U" => "u", "u" => "u", "V" => "v", "v" => "v", "W" => "w", "w" => "w", "X" => "x", "x" => "x", "Y" => "y", "y" => "y", "Z" => "z", "z" => "z", "+" => "+", "-" => "-", "^" => "^", "_" => "_"]
	];

	/**
	 * special first letter keys recognized by
	 * Options::init & Options::initShortcuts,
	 * and implemented in Options::alterOption
	 *
	 * @var array
	 */
	protected static $specialKeys = ["+" => "+", "-" => "-", "^" => "^", "~" => "~", "*" => "*"];

	/**
	 * initialize options
	 *
	 * @param $defaults
	 * @param $opts
	 * @param array $subOptions
	 * @return mixed
	 */
	public static function init($defaults, $opts, $subOptions = [])
	{
		if (empty($opts)) return $defaults;

		$newOpts = $defaults;//new \ArrayObject($defaults, \ArrayObject::STD_PROP_LIST);

		foreach ($opts as $key => $value) {

			$alter = '';
			$doAlter = false;
			$newKey = '';
			$keyPointer = $key;

			if (isset($key[0]) && isset(self::$specialKeys[$key[0]])) {
				$alter = $key[0];
				$doAlter = true;
				$keyInc = 1; // offset by 1 character

				while (isset($key[$keyInc])) {
					$newKey .= $key[$keyInc];
					++$keyInc;
				}
				$keyPointer = $newKey;
			}

			if ($doAlter) {
				self::alterOption($alter, $newOpts, $keyPointer, $value);
			} else {
				$newOpts[$keyPointer] = $value;
			}
		}

		if (isset($defaults['__subOptions']))
			$subOptions = array_merge($defaults['__subOptions'], $subOptions);

		self::initSubOptions($newOpts, $defaults, $opts, $subOptions, $method = 'init');

		return $newOpts;
	}

	/**
	 * special method for appending and modifying options.
	 * based on first letter of the option.
	 *
	 * @param $alter
	 * @param $options
	 * @param $key
	 * @param $value
	 */
	private static function alterOption($alter, &$options, $key, $value)
	{
		if (isset($options[$key])) {
			switch ($alter) {
				case '+':
					if (is_array($options[$key])) {
						array_push($options[$key], $value);
					} else {
						$options[$key] .= $value;
					}
					break;
				case '*':
					if (is_array($options[$key])) {
						$options[$key] = [$options[$key]];
						array_push($options[$key], $value);
					} else {
						$options[$key] .= $value;
					}
					break;
				case '%':
					$options[$key] = str_replace(
						array_keys($value),
						array_values($value),
						$options[$key]);
					break;
				case '~':
					$defaultIsArray = is_array($options[$key]);
					$valueIsArray = is_array($value);
					if ($defaultIsArray && $valueIsArray) {
						$options[$key] = array_merge($options[$key], $value);
					} else if ($defaultIsArray && !$valueIsArray) {
						array_push($options[$key], $value);
					} else {
						$options[$key] = [$value];
					}
					break;
				case '^':
					if (is_array($options[$key])) {
						array_unshift($options[$key], $value);
					} else {
						$options[$key] = $value . $options[$key];
					}
					break;
				case '=': // full reassignment
					$options[$key] = $value;
					break;
			}
		} else {
			$options[$key] = $value;
		}
	}

	/**
	 * Initialize sub options in Options::init and Options::initShortcuts.
	 *
	 * @param $newOpts
	 * @param $defaults
	 * @param $opts
	 * @param $subOptions
	 * @param string $method
	 */
	private static function initSubOptions(&$newOpts, $defaults, $opts, $subOptions, $method = 'init')
	{
		if (is_array($subOptions) && !empty($subOptions)) {
			foreach ($subOptions as $key => $sub) {
				$pointer = is_int($key) ? $sub : $key;
				$defaultsRef = self::dot($defaults, $pointer);
				$optsRef = self::dot($opts, $pointer);
				if (is_array($sub)) $defaultsRef = $sub;
				if ($defaultsRef !== null && $optsRef !== null)
					self::dot($newOpts, $pointer, self::$method($defaultsRef, $optsRef), true);
			}
		}
	}

	/**
	 * Resolve dot notation for array.
	 *
	 * 'settings.settingOne' will resolve to ['settings']['settingOne'].
	 *
	 * @param $ref
	 * @param $option
	 * @param null $setValue
	 * @param bool $setNull
	 * @param bool $asReference
	 * @return mixed|null
	 */
	public static function resolveDotNotation(&$ref, $option, $setValue = null, $setNull = false, $asReference = false)
	{
		if (isset($ref[$option])) return $setValue !== null ? $ref[$option] = $setValue : $ref[$option];

		static $memory;
		if (is_null($memory)) $memory = [];

		$partsString = '';
		if (isset($memory[$option])) {
			$partsString = $memory[$option];
		} else {
			$inc = 0;
			$dotInc = 0;
			$part = '';
			while (isset($option[$inc])) {
				$isDot = $option[$inc] === '.';
				$is0 = $option[$inc] === "*";
				$part .= $isDot ? '' : $option[$inc];
				if ($isDot) {
					$dotInc++;
					$partsString .= ($part[0] === "*" ? '' . "\0" . '*' . "\0" . $part : $part) . '"]["';
					$part = '';
				}
				$inc++;
			}
			if ($part !== '') {
				$partsString .= $part;
			}
			$memory[$option] = $partsString;
		}

		$asReferenceSymbol = $asReference ? '&' : '';
		if ($setValue !== null || $setNull) {
			$evalString = 'return $ref["' . $partsString . '"] = ' . $asReferenceSymbol . '$setValue;';
		} else {
			$evalString = 'return isset($ref["' . $partsString . '"]) ? $ref["' . $partsString . '"] : null;';
		}

		return eval($evalString);
	}

	/**
	 * init shortcut options.
	 *
	 * @param $defaults
	 * @param $opts
	 * @param string $functionName
	 * @param array $subOptions
	 * @return array|mixed
	 */
	public static function initShortcuts($defaults, $opts, string $functionName = '', array $subOptions = [])
	{
		static $memory;
		if (is_null($memory)) $memory = [];

		if (empty($opts)) return $defaults;


		$createDefaults = true;
		$defaultsWithMap = ['__map' => []];
		if ($functionName !== '') {
			if (!isset($memory[$functionName])) {
				$memory[$functionName] = [];
				$memory[$functionName]['__map'] = [];
			}
			$createDefaults = empty($memory[$functionName]['__map']);
		}
		if ($createDefaults) {
			foreach ($defaults as $longHand => $value) {
				$longHandLower = '';
				$shortHand = '';
				$shortHandLower = '';
				$inc = 0;
				while (isset($longHand[$inc])) {
					if (isset(self::$alphas['uppercase'][$longHand[$inc]]) || $inc == 0) {
						$shortHand .= $longHand[$inc];
						$shortHandLower .= self::$alphas['all'][$longHand[$inc]];
						$longHandLower .= self::$alphas['all'][$longHand[$inc]];
					} else {
						$longHandLower .= $longHand[$inc];
					}
					++$inc;
				}
				$defaultsWithMap[$longHand] = $value;
				$defaultsWithMap['__map'][$longHandLower] = $longHand;
				$defaultsWithMap['__map'][$shortHand] = $longHand;
				$defaultsWithMap['__map'][$shortHandLower] = $longHand;
			}
			if ($functionName !== '') $memory[$functionName] = &$defaultsWithMap;
		}

		$defaults = $functionName !== '' ? $memory[$functionName] : $defaultsWithMap;

		foreach ($opts as $key => $value) {
			$alter = '';
			$newKey = '';
			$doAlter = false;
			$keyPointer = $key;
			if (isset(self::$specialKeys[$key[0]])) {
				$alter = $key[0];
				$doAlter = true;
				$keyInc = 1; // offset by 1 character

				while (isset($key[$keyInc])) {
					$newKey .= $key[$keyInc];
					++$keyInc;
				}
				$keyPointer = $newKey;
			}
			if (isset($defaults[$keyPointer]) && $defaults[$keyPointer] !== $value) {
				if ($doAlter) {
					self::alterOption($alter, $defaults, $keyPointer, $value);
				} else {
					$defaults[$keyPointer] = $value;
				}
			} else {
				if (isset($defaults['__map'][$keyPointer])) {
					if ($doAlter) {
						self::alterOption($alter, $defaults, $defaults['__map'][$keyPointer], $value);
					} else {
						$defaults[$defaults['__map'][$keyPointer]] = $value;
					}
				} else {
					if ($doAlter) {
						self::alterOption($alter, $defaults, $keyPointer, $value);
					} else {
						$defaults[$keyPointer] = $value;
					}
				}
			}
		}
		unset($defaults['__map']);
		$newOpts = &$defaults;

		self::initSubOptions($newOpts, $defaults, $opts, $subOptions, $method = 'initShortcuts');

		return $newOpts;
	}

	/**
	 * resolve dot notation for array.
	 *
	 * 'settings.settingOne' will resolve to ['settings']['settingOne'].
	 *
	 * @param $ref
	 * @param $option
	 * @param null $setValue
	 * @param string $varName
	 * @return mixed|null
	 */
	public static function &dot(&$ref, $option, $setValue = null, $setNull = false)
	{
		$nonExistent = false;
		$refRef = &$ref;
		foreach (explode('.', $option) as $segment) {
			if (isset($refRef[$segment])) {
				$refRef = &$refRef[$segment];
			} else {
				$nonExistent = true;
				if ($setValue !== null) {
					$refRef[$segment] = [];
					$refRef = &$refRef[$segment];
				}
			}
		}

		$null = null;
		if ($nonExistent) return $null;

		if ($setValue !== null || $setNull) {
			$refRef = $setValue;
			if ($setNull && $refRef === null) $refRef = null;
			return $refRef;
		}

		return $refRef;
	}

	/**
	 * resolve dot notation for array.
	 *
	 * 'settings.settingOne' will resolve to ['settings']['settingOne'].
	 *
	 * @param $ref
	 * @param $option
	 * @param null $setValue
	 * @param string $varName
	 * @return mixed|null
	 */
	public static function &__dot(&$ref, $option)
	{
		$nonExistent = false;
		$refRef = &$ref;
		foreach (explode('.', $option) as $segment) {
			if (isset($refRef[$segment])) {
				$refRef = &$refRef[$segment];
			} else {
				$nonExistent = true;
				break;
			}
		}

		$null = null;
		if ($nonExistent) return $null;

		return $refRef;
	}

	/**
	 * resolve dot notation for array.
	 *
	 * 'settings.settingOne' will resolve to ['settings']['settingOne'].
	 *
	 * @param $ref
	 * @param $option
	 * @param null $setValue
	 * @param string $varName
	 * @return mixed|null
	 */
	public static function &dotSet(&$ref, $option, $setValue)
	{
		$refRef = &self::__dot($ref, $option);
		$refRef = $setValue;

		return $refRef;
	}

	/**
	 * overwrite options.
	 *
	 * @param $defaults
	 * @param $opts
	 * @return mixed
	 */
	public static function overwrite($defaults, $opts)
	{
		if (empty($opts)) return $defaults;

		foreach ($opts as $key => $value) {
			$alter = '';
			$newKey = '';
			$doAlter = false;
			$keyPointer = $key;

			if (isset(self::$specialKeys[$key[0]])) {
				$alter = $key[0];
				$doAlter = true;
				$keyInc = 1; // offset by 1 character

				while (isset($key[$keyInc])) {
					$newKey .= $key[$keyInc];
					++$keyInc;
				}
				$keyPointer = $newKey;
			}

			if (is_array($value)) {
				$defaultsVar = isset($defaults[$keyPointer])
				&& is_array($defaults[$keyPointer])
					? $defaults[$keyPointer]
					: [];

				$defaults[$keyPointer] = self::overwrite($defaultsVar, $value);
			} else {
				if ($doAlter) {
					self::alterOption($alter, $defaults, $keyPointer, $value);
				} else {
					$defaults[$keyPointer] = $value;
				}
			}
		}

		return $defaults;
	}

	/**
	 * create javaScript options from PHP array.
	 *
	 * @param $defaults
	 * @param $options
	 * @param string $method
	 * @return string json encoded options string
	 */
	public static function js($defaults = [], $options = [], $ignoreOptions = [])
	{
		$ignoreOptions = array_flip($ignoreOptions);

		$inc = 0;
		$optionsStr = '{';

		$optionsCount = count($options);
		foreach ($options as $key => $option) {
			if (!isset($ignoreOptions[$key])) {
				$value = self::getJsValue($defaults, $option, $key);
				$optionsStr .= json_encode($key) . ':' . $value;
				$optionsStr .= $optionsCount == $inc + 1 ? "" : ",";
			}
			++$inc;
		}
		$optionsStr .= '}';
		return $optionsStr;
	}

	/**
	 * Get javaScript value from PHP value.
	 *
	 * @param $defaults
	 * @param $option
	 * @param $key
	 * @return bool|int|string
	 */
	private static function getJsValue($defaults, $option, $key)
	{
		$type = gettype($option);
		switch ($type) {
			case 'boolean':
				$value = $option === true ? 'true' : 'false';
				break;
			case 'integer':
				$value = $option;
				break;
			case 'string':
				// "js::" at the beginning of the string signifies javascript
				// function, that means there is a function declaration so there should
				// be no quotes surrounding the output value
				if (isset($options[0]) && ($option[0] === ' ' || $option[0] === "\t")) {
					$option = ltrim($option);
				}

				$option = trim($option);
				if (isset($option[0]) && $option[0] === 'j'
					&& isset($option[1]) && $option[1] === 's'
					&& isset($option[2]) && $option[2] === ':'
					&& isset($option[3]) && $option[3] === ':') {
					$value = substr($option, 4);
				} else {
					$value = '"' . str_replace('"', '\\"', $option) . '"';
				}
				break;
			case 'array':

				if (isset($option[0]) || empty($option)) {
					$value = json_encode($option);
				} else {
					$value = self::js($defaults[$key] ?? [], $option);
				}
				break;
			case 'object':
				$value = self::js($defaults[$key] ?? [], $option);
				break;
			case 'NULL';
				$value = 'null';
				break;
			default:
				$value = '"' . $option . '"';
				break;
		}
		return $value;
	}

	/**
	 * Build shortcuts array by first capital letter.
	 * 'ajaxOptions' will become 'ao'.
	 * 'settings' will become 's'.
	 * see \System\Options::buildShortcutLowercase()
	 *
	 * @param array $defaults
	 * @param array $options
	 * @return array
	 */
	public static function buildShortcuts($defaults = [], $options = [])
	{
		$optionsArray = [];
		foreach ($options as $key => $value) {
			if ($options[$key] != $defaults[$key]) $optionsArray[self::buildShortcutLowercase($key)] = $value;
		}
		return $optionsArray;
	}

	/**
	 * build shortcut by first capital letter.
	 * 'ajaxOptions' will become 'ao'.
	 * 'settings' will become 's'.
	 * 'appendToConsole' will become 'atc'.
	 *
	 * @param $key
	 * @return string
	 */
	private static function buildShortcutLowercase($key)
	{
		$shortHandLower = '';
		$inc = 0;
		$foundCapitals = 0;
		while (isset($key[$inc])) {
			if (isset(self::$alphas['uppercase'][$longHand[$inc]])) {
				$shortHandLower .= self::$alphas['all'][$key[$inc]];
				++$foundCapitals;
			}
			++$inc;
		}
		if ($foundCapitals === 0) $shortHandLower = $key;

		return $shortHandLower;
	}

	/**
	 * replace options.
	 *
	 * @param $newOpts
	 * @param $defaults
	 * @param $opts
	 * @param $subOptions
	 * @param string $method
	 */
	private static function replaceOptions(&$newOpts, $replace)
	{
		if (is_array($replace) && !empty($replace)) {
			foreach ($replace as $key => $rep) {
				foreach ($rep as $name => $replaceWith) {
					$value = self::dot($newOpts, $key);
					$setValue = is_array($value)
						? $replaceWith
						: str_replace($name, $replaceWith, $value);

					self::dot($newOpts, $key, $setValue, true);
				}
			}
		}
	}
}