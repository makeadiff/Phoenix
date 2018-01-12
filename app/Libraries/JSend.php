<?php

class JSend
{
    static public function success($message, $data = array()) {
		return JSend::situation('success', $message, $data);
	}

	static public function fail($message, $data = array()) {
		return JSend::situation('fail', $message, $data);
	}

	static public function error($message, $data = array()) {
		return JSend::situation('error', $message, $data);
	}

	static public function situation($status, $message, $data) {
		$template = array(
			'success'	=> true,
			'error'		=> false,
			'status'	=> 'success',
			'data'		=> null
		);

		if($status == 'error') {
			$template['error'] = true;
			$template['success'] = false;
			$template['fail'] = false;

			$template['message'] = $message;

		} else if($status == 'fail') {
			$template['error'] = true;
			$template['success'] = false;
			$template['fail'] = true;

			$template['data'] = array($message);
		}

		$template['status'] = $status;

		if(is_string($message)) {
			$template[$status] = $message;

		} elseif(is_array($message)) {
			$template = array_merge($template, $message);
		} 

		if($data)
			$template['data'] = $data;

		return json_encode($template);
	}
}
