<?php

namespace Kobas\Request;

interface HttpRequest
{
	/**
	 * @return $this
	 */
	public function init();

	/**
	 * @param $url
	 * @return $this
	 */
	public function setUrl($url);

	/**
	 * @param $name
	 * @param $value
	 * @return $this
	 */
	public function setOption($name, $value);

	/**
	 * @return mixed
	 */
	public function execute();
	public function getInfo($name);

	/**
	 * @return $this
	 */
	public function close();
}
